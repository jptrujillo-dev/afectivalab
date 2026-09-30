<?php
/**
 * Límite de intentos de inicio de sesión.
 *
 * Se engancha a los hooks nativos de WordPress (`authenticate`,
 * `wp_login_failed`, `wp_login`) en vez de modificar
 * afectivalab_login_attempt() directamente. Con eso alcanza para cubrir
 * **los tres caminos de login a la vez** — el formulario normal de
 * /ingresar, el endpoint AJAX, y hasta el wp-login.php por defecto de
 * WordPress (que seguía siendo un cuarto camino sin ningún límite) — sin
 * duplicar la lógica en cada uno.
 *
 * Regla: 5 intentos fallidos en 15 minutos desde la misma IP bloquean esa IP
 * durante 15 minutos más, aunque el siguiente intento tenga la contraseña
 * correcta. Son valores de partida razonables, no un número que haya pedido
 * el cliente — fáciles de ajustar si hace falta.
 *
 * **Limitación conocida**: se identifica a quien intenta por
 * `$_SERVER['REMOTE_ADDR']` únicamente. A propósito no se confía en
 * cabeceras como X-Forwarded-For — son triviales de falsificar salvo que se
 * sepa con certeza que el proxy del hosting las sobrescribe siempre, y no
 * hay esa garantía documentada para este hosting. Si Hostinger pone un proxy
 * delante que sí las controla, esto puede ajustarse para leer la IP real del
 * visitante en vez de la del proxy.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AFECTIVALAB_LOGIN_MAX_INTENTOS = 5;
const AFECTIVALAB_LOGIN_VENTANA      = 15 * MINUTE_IN_SECONDS;
const AFECTIVALAB_LOGIN_BLOQUEO      = 15 * MINUTE_IN_SECONDS;

/**
 * La IP de quien hace la petición. Ver la nota de arriba sobre por qué no se
 * confía en cabeceras de proxy.
 */
function afectivalab_ip_actual() {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
}

function afectivalab_login_clave_intentos( $ip ) {
	return 'afectivalab_intentos_' . md5( $ip );
}

function afectivalab_login_clave_bloqueo( $ip ) {
	return 'afectivalab_bloqueo_' . md5( $ip );
}

/**
 * ¿Esta IP está bloqueada ahora mismo?
 */
function afectivalab_login_bloqueado( $ip ) {
	return false !== get_transient( afectivalab_login_clave_bloqueo( $ip ) );
}

/**
 * Minutos que faltan para que se levante el bloqueo, redondeado hacia
 * arriba. get_transient() no expone cuánto le queda de vida a una
 * transiente, así que se lee el timeout guardado en options directamente —
 * es el mismo truco que usan los plugins de seguridad para esto. Si por
 * algún motivo no se puede leer (por ejemplo, un object cache persistente
 * que no pasa por la tabla de options), se devuelve un valor de partida en
 * vez de romper el mensaje de error.
 *
 * @param string $ip
 * @return int
 */
function afectivalab_login_minutos_restantes( $ip ) {
	$vence = get_option( '_transient_timeout_' . afectivalab_login_clave_bloqueo( $ip ) );

	if ( ! $vence ) {
		return (int) ceil( AFECTIVALAB_LOGIN_BLOQUEO / MINUTE_IN_SECONDS );
	}

	$segundos = max( 0, (int) $vence - time() );

	return (int) ceil( $segundos / MINUTE_IN_SECONDS );
}

/**
 * Registra un intento fallido y bloquea la IP si llegó al máximo.
 *
 * Enganchado a `wp_login_failed`, que WordPress dispara en cualquier intento
 * de autenticación que falle — no solo desde nuestros formularios.
 */
function afectivalab_login_registrar_fallo() {
	$ip = afectivalab_ip_actual();

	if ( afectivalab_login_bloqueado( $ip ) ) {
		return;
	}

	$clave    = afectivalab_login_clave_intentos( $ip );
	$intentos = (int) get_transient( $clave );
	$intentos++;

	if ( $intentos >= AFECTIVALAB_LOGIN_MAX_INTENTOS ) {
		set_transient( afectivalab_login_clave_bloqueo( $ip ), 1, AFECTIVALAB_LOGIN_BLOQUEO );
		delete_transient( $clave );
		return;
	}

	// Cada intento renueva la ventana: 5 fallos seguidos, no importa si se
	// reparten en 2 minutos o en 14, bloquean igual.
	set_transient( $clave, $intentos, AFECTIVALAB_LOGIN_VENTANA );
}
add_action( 'wp_login_failed', 'afectivalab_login_registrar_fallo' );

/**
 * Corta el login antes de que WordPress llegue a comprobar la contraseña, si
 * la IP está bloqueada — así un acierto durante el bloqueo tampoco entra.
 *
 * Prioridad 1: por delante de wp_authenticate_username_password() (20), que
 * es donde WordPress de verdad revisa las credenciales.
 */
function afectivalab_login_cortar_si_bloqueado( $user, $username, $password ) {
	if ( '' === $username && '' === $password ) {
		// Es la primera pasada del filtro (sin credenciales todavía, WordPress
		// la dispara al cargar wp-login.php); no hay nada que bloquear aquí.
		return $user;
	}

	$ip = afectivalab_ip_actual();

	if ( ! afectivalab_login_bloqueado( $ip ) ) {
		return $user;
	}

	return new WP_Error(
		'afectivalab_demasiados_intentos',
		sprintf(
			/* translators: %d: minutos que faltan para poder volver a intentar. */
			__( 'Demasiados intentos. Espera %d minutos antes de volver a intentar.', 'afectivalab' ),
			afectivalab_login_minutos_restantes( $ip )
		)
	);
}
add_filter( 'authenticate', 'afectivalab_login_cortar_si_bloqueado', 1, 3 );

/**
 * Un login correcto borra el historial de fallos de esa IP: no tiene sentido
 * seguir contando intentos viejos después de haber entrado bien.
 */
function afectivalab_login_limpiar_intentos() {
	$ip = afectivalab_ip_actual();

	delete_transient( afectivalab_login_clave_intentos( $ip ) );
	delete_transient( afectivalab_login_clave_bloqueo( $ip ) );
}
add_action( 'wp_login', 'afectivalab_login_limpiar_intentos' );
