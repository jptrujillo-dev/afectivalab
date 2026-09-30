<?php
/**
 * Suscripción mensual — gestionada por Paid Memberships Pro (plugin,
 * `wp-content/plugins/paid-memberships-pro`) con PayPal como pasarela
 * (`wp-content/plugins/pmpro-paypal`). Este archivo NO llama a ninguna API
 * de pago directamente — solo consulta el nivel de membresía del usuario
 * (PMPro) para decidir acceso, y orquesta la cancelación con gracia.
 *
 * Reemplaza dos intentos anteriores con Mercado Pago (ver docs/bitacora.md):
 * primero el producto nativo de Suscripciones/Preapproval (exigía que cada
 * familia tuviera cuenta propia de Mercado Pago), después un modelo
 * semi-automático con tarjeta guardada (bloqueado por un 403
 * "PA_UNAUTHORIZED_RESULT_FROM_POLICIES" de la cuenta/aplicación del
 * cliente, sin forma de diagnosticar ni arreglar desde el código). Mercado
 * Pago queda pendiente para más adelante.
 *
 * El nivel de membresía ya lo creó el cliente desde el escritorio (PMPro >
 * Membership Levels): 15 USD/mes, recurrente — id 1.
 *
 * Decisión de producto (usuario, no PMPro): al cancelar, la cuenta sigue
 * teniendo acceso hasta el final del período ya pagado — PMPro por
 * default corta el acceso al instante, así que esa gracia se maneja acá
 * (ver afectivalab_handle_suscripcion_cancelar() y
 * afectivalab_pmpro_revisar_cancelaciones()).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'AFECTIVALAB_PMPRO_LEVEL_ID' ) ) {
	define( 'AFECTIVALAB_PMPRO_LEVEL_ID', 1 );
}

/**
 * El precio se fija en dólares ($15/mes, decisión del usuario — es lo que
 * de verdad se cobra, PayPal no admite soles) y se muestra además como
 * referencia en soles para que la familia entienda cuánto es en su moneda
 * — ver afectivalab_mp_precio_local() para la conversión.
 */
if ( ! defined( 'AFECTIVALAB_MP_PRECIO_USD' ) ) {
	define( 'AFECTIVALAB_MP_PRECIO_USD', 15 );
}
if ( ! defined( 'AFECTIVALAB_MP_MONEDA' ) ) {
	define( 'AFECTIVALAB_MP_MONEDA', 'PEN' );
}
if ( ! defined( 'AFECTIVALAB_MP_TIPO_CAMBIO_EMERGENCIA' ) ) {
	// Solo se usa si la consulta de cotización falla Y todavía no hay ningún
	// tipo de cambio guardado de una consulta anterior — mejor mostrar un
	// valor de referencia algo viejo que no poder mostrar ninguno.
	define( 'AFECTIVALAB_MP_TIPO_CAMBIO_EMERGENCIA', 3.75 );
}

/**
 * Único punto que llama al servicio externo de cotizaciones — aísla la
 * llamada real para que se pueda mockear en un harness de pruebas.
 * Servicio gratuito, sin API key (open.er-api.com); si algún día deja de
 * responder, afectivalab_mp_tipo_cambio_usd_pen() sigue funcionando con el
 * último valor conocido.
 *
 * @return float|null
 */
function afectivalab_mp_consultar_tipo_cambio_remoto() {
	$response = wp_remote_get( 'https://open.er-api.com/v6/latest/USD', array( 'timeout' => 10 ) );

	if ( is_wp_error( $response ) ) {
		return null;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( empty( $data['rates']['PEN'] ) ) {
		return null;
	}

	return (float) $data['rates']['PEN'];
}

/**
 * Tipo de cambio USD → PEN, con caché de 12 horas para no consultar el
 * servicio externo en cada carga de /suscribirse. Si la consulta falla, se
 * usa el último valor que sí funcionó (guardado sin vencimiento); si nunca
 * hubo ninguno, el valor de emergencia fijo. Es solo informativo: el cobro
 * real en PayPal siempre es en dólares, esto nunca participa de un pago.
 *
 * @return float
 */
function afectivalab_mp_tipo_cambio_usd_pen() {
	$cacheado = get_transient( 'afectivalab_mp_tipo_cambio' );

	if ( false !== $cacheado ) {
		return (float) $cacheado;
	}

	$valor = afectivalab_mp_consultar_tipo_cambio_remoto();

	if ( null === $valor ) {
		error_log( '[Afectivalab] no se pudo consultar el tipo de cambio, se usa el último conocido o el de emergencia' );
		$ultimo_conocido = get_option( 'afectivalab_mp_ultimo_tipo_cambio' );
		return $ultimo_conocido ? (float) $ultimo_conocido : AFECTIVALAB_MP_TIPO_CAMBIO_EMERGENCIA;
	}

	set_transient( 'afectivalab_mp_tipo_cambio', $valor, 12 * HOUR_IN_SECONDS );
	update_option( 'afectivalab_mp_ultimo_tipo_cambio', $valor );

	return $valor;
}

/**
 * El precio de referencia en soles, a partir del precio ancla en dólares y
 * el tipo de cambio del momento. Solo para mostrar — PayPal cobra en
 * dólares siempre.
 *
 * @return float
 */
function afectivalab_mp_precio_local() {
	return round( AFECTIVALAB_MP_PRECIO_USD * afectivalab_mp_tipo_cambio_usd_pen(), 2 );
}

/**
 * Predicado sin redirigir — lo usa tanto el gate como el curso-vitrina
 * (que necesita seguir mostrando la página, solo con otro cartel).
 *
 * Delegado por completo a Paid Memberships Pro: mientras el nivel siga
 * activo ahí (`pmpro_hasMembershipLevel()`), hay acceso — sin importar si
 * hay una cancelación pendiente (ver afectivalab_handle_suscripcion_cancelar()),
 * porque esa gracia se resuelve dejando el nivel activo hasta que se agote.
 *
 * @param int $user_id Por defecto, el usuario actual.
 * @return bool
 */
function afectivalab_usuario_tiene_suscripcion_activa( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();

	if ( ! $user_id || ! function_exists( 'pmpro_hasMembershipLevel' ) ) {
		return false;
	}

	return (bool) pmpro_hasMembershipLevel( AFECTIVALAB_PMPRO_LEVEL_ID, $user_id );
}

/**
 * Resumen para pintar la tarjeta de /mi-cuenta.
 *
 * @param int $user_id
 * @return array{status: string, label: string, activa: bool, proximo_cobro: int, puede_cancelar: bool}
 */
function afectivalab_suscripcion_resumen( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();
	$activa  = afectivalab_usuario_tiene_suscripcion_activa( $user_id );

	if ( ! $activa ) {
		return array(
			'status'         => 'ninguna',
			'label'          => __( 'Sin suscripción', 'afectivalab' ),
			'activa'         => false,
			'proximo_cobro'  => 0,
			'puede_cancelar' => false,
			'nivel_nombre'   => '',
			'nivel_precio'   => '',
			'nivel_periodo'  => '',
		);
	}

	// Nombre y precio real del plan — mismo nivel para toda la cuenta (un
	// único nivel de pago, AFECTIVALAB_PMPRO_LEVEL_ID), leído directo de
	// PMPro en vez de repetir el precio a mano en el theme.
	$nivel         = function_exists( 'pmpro_getLevel' ) ? pmpro_getLevel( AFECTIVALAB_PMPRO_LEVEL_ID ) : false;
	$nivel_nombre  = $nivel ? $nivel->name : '';
	$nivel_precio  = ( $nivel && function_exists( 'pmpro_formatPrice' ) ) ? pmpro_formatPrice( $nivel->billing_amount ) : '';
	$nivel_periodo = ( $nivel && ! empty( $nivel->cycle_number ) && function_exists( 'pmpro_translate_billing_period' ) )
		? pmpro_translate_billing_period( $nivel->cycle_period, $nivel->cycle_number )
		: '';

	// Si ya pidió cancelar, el nivel de PMPro se deja activo a propósito
	// hasta esta fecha (ver afectivalab_handle_suscripcion_cancelar()) — acá
	// se refleja eso en vez de ofrecer cancelar de nuevo.
	$cancelacion_pendiente = (int) get_user_meta( $user_id, '_afectivalab_pmpro_cancelar_en', true );

	if ( $cancelacion_pendiente ) {
		return array(
			'status'         => 'cancelada',
			'label'          => __( 'Cancelada — con acceso hasta fin de período', 'afectivalab' ),
			'activa'         => true,
			'proximo_cobro'  => $cancelacion_pendiente,
			'puede_cancelar' => false,
			'nivel_nombre'   => $nivel_nombre,
			'nivel_precio'   => $nivel_precio,
			'nivel_periodo'  => $nivel_periodo,
		);
	}

	$proximo_cobro = function_exists( 'pmpro_next_payment' ) ? pmpro_next_payment( $user_id, 'success', 'timestamp' ) : false;

	return array(
		'status'         => 'activa',
		'label'          => __( 'Activa', 'afectivalab' ),
		'activa'         => true,
		'proximo_cobro'  => $proximo_cobro ? (int) $proximo_cobro : 0,
		'puede_cancelar' => true,
		'nivel_nombre'   => $nivel_nombre,
		'nivel_precio'   => $nivel_precio,
		'nivel_periodo'  => $nivel_periodo,
	);
}

/**
 * Historial de pedidos/pagos del usuario para la sección "Tus pedidos" de
 * /mi-cuenta — usa la propia clase MemberOrder de PMPro (misma fuente de
 * datos que pages/invoice.php, confirmada leyendo su rama de "lista de
 * pedidos") en vez de duplicar consultas SQL propias.
 *
 * @param int $user_id
 * @return array Lista de pedidos, más reciente primero: cada uno con
 *               codigo, fecha (timestamp), nivel, total (ya formateado con
 *               el símbolo de moneda) y estado/estado_clase.
 */
function afectivalab_suscripcion_pedidos( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();

	if ( ! class_exists( 'MemberOrder' ) || ! $user_id ) {
		return array();
	}

	$resumenes = MemberOrder::get_orders(
		array(
			'status'  => array( 'pending', 'refunded', 'success' ),
			'user_id' => $user_id,
		)
	);

	$pedidos = array();

	foreach ( $resumenes as $resumen ) {
		$orden = new MemberOrder();
		$orden->getMemberOrderByID( $resumen->id );
		$orden->getMembershipLevel();

		if ( in_array( $orden->status, array( '', 'success', 'cancelled' ), true ) ) {
			$estado        = __( 'Pagado', 'afectivalab' );
			$estado_clase  = 'pagado';
		} elseif ( 'refunded' === $orden->status ) {
			$estado        = __( 'Reembolsado', 'afectivalab' );
			$estado_clase  = 'reembolsado';
		} else {
			$estado        = __( 'Pendiente', 'afectivalab' );
			$estado_clase  = 'pendiente';
		}

		$pedidos[] = array(
			'codigo'       => $orden->code,
			'fecha'        => (int) $orden->getTimestamp(),
			'nivel'        => ! empty( $orden->membership_level ) ? $orden->membership_level->name : '',
			'total'        => $orden->get_formatted_total(),
			'estado'       => $estado,
			'estado_clase' => $estado_clase,
		);
	}

	return $pedidos;
}

/**
 * Si la página actual es alguna de las que crea Paid Memberships Pro
 * (checkout, cuenta, niveles, etc.) — las páginas de PMPro son páginas de
 * WordPress normales, así que ya heredan el header/footer del theme solas;
 * esto solo sirve para poder sumarles un poco de estilo propio encima (ver
 * inc/enqueue.php) sin tener que copiar sus plantillas internas.
 *
 * @return bool
 */
function afectivalab_es_pagina_pmpro() {
	$paginas = array(
		get_option( 'pmpro_checkout_page_id' ),
		get_option( 'pmpro_confirmation_page_id' ),
		get_option( 'pmpro_account_page_id' ),
		get_option( 'pmpro_levels_page_id' ),
		get_option( 'pmpro_billing_page_id' ),
		get_option( 'pmpro_cancel_page_id' ),
		get_option( 'pmpro_invoice_page_id' ),
	);

	foreach ( $paginas as $pagina_id ) {
		if ( $pagina_id && is_page( (int) $pagina_id ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Fuerza a PMPro a usar SIEMPRE la copia del theme de
 * paid-memberships-pro/pages/confirmation.php, sin importar la opción
 * "pmpro_use_custom_page_template_confirmation" que ya pudiera existir en
 * la base de datos (confirmado leyendo includes/page-templates.php,
 * función pmpro_loadTemplate(): si esa opción vale 'no', PMPro ignora por
 * completo la copia del theme aunque exista y sus números de versión
 * coincidan — el motivo real por el que la personalización de la
 * confirmación no se llegaba a ver, aun subiendo el archivo). Se corre en
 * cada carga de "init" pero solo escribe en la base de datos la primera
 * vez (y si alguna vez alguien la cambia a mano desde el admin de PMPro,
 * se corrige sola en la siguiente carga).
 *
 * @return void
 */
function afectivalab_pmpro_forzar_plantilla_confirmacion() {
	if ( 'yes' !== get_option( 'pmpro_use_custom_page_template_confirmation' ) ) {
		update_option( 'pmpro_use_custom_page_template_confirmation', 'yes' );
	}
}
add_action( 'init', 'afectivalab_pmpro_forzar_plantilla_confirmacion' );

/**
 * No existe un pmpro_is_confirmation() en el plugin (se revisó su código:
 * solo trae pmpro_is_checkout()) — se detecta igual que en
 * afectivalab_es_pagina_pmpro(), comparando contra el id real guardado en
 * la opción pmpro_confirmation_page_id. La usan tanto el filtro de título
 * de más abajo como el enqueue del modal del recibo en inc/enqueue.php.
 *
 * @return bool
 */
function afectivalab_es_pagina_confirmacion_pmpro() {
	$pagina_confirmacion = get_option( 'pmpro_confirmation_page_id' );
	return $pagina_confirmacion && is_page( (int) $pagina_confirmacion );
}

/**
 * Las páginas que PMPro crea solas al activarse traen título en inglés
 * ("Membership Checkout", "Membership Confirmation") — a diferencia del
 * resto del texto de esas páginas (que sí sale traducido, porque el plugin
 * lo pasa por __()/_e() y hay un .mo instalado), el título es el post_title
 * real que quedó guardado en la base de datos al crearse la página, así que
 * ningún paquete de idioma lo toca solo. Se sobreescribe acá en vez de
 * pedirle al cliente que la edite a mano en el escritorio, para que
 * sobreviva a una reinstalación del plugin.
 *
 * @return string|null Título en español, o null si no es ninguna de estas páginas.
 */
function afectivalab_pmpro_titulo_texto() {
	if ( function_exists( 'pmpro_is_checkout' ) && pmpro_is_checkout() ) {
		return __( 'Confirma tu suscripción', 'afectivalab' );
	}

	if ( afectivalab_es_pagina_confirmacion_pmpro() ) {
		return __( '¡Tu suscripción ya está activa!', 'afectivalab' );
	}

	return null;
}

/**
 * @param string $titulo
 * @return string
 */
function afectivalab_pmpro_titulo_checkout( $titulo ) {
	if ( in_the_loop() ) {
		$titulo_pmpro = afectivalab_pmpro_titulo_texto();
		if ( null !== $titulo_pmpro ) {
			return $titulo_pmpro;
		}
	}

	return $titulo;
}
add_filter( 'the_title', 'afectivalab_pmpro_titulo_checkout' );

/**
 * Mismo criterio que afectivalab_pmpro_titulo_checkout(), pero para el
 * <title> de la pestaña del navegador.
 *
 * @param array $titulo
 * @return array
 */
function afectivalab_pmpro_titulo_documento( $titulo ) {
	$titulo_pmpro = afectivalab_pmpro_titulo_texto();
	if ( null !== $titulo_pmpro ) {
		$titulo['title'] = $titulo_pmpro;
	}

	return $titulo;
}
add_filter( 'document_title_parts', 'afectivalab_pmpro_titulo_documento' );

/**
 * PMPro deja agregar clases a cualquiera de sus elementos vía este filtro
 * (`pmpro_element_class`) en vez de tener que copiar sus plantillas —
 * se usa para que el botón real de "Check Out with PayPal" tenga el mismo
 * look que el resto de los botones del sitio (.btn/.btn-primary/.btn-lg de
 * base.css), sin tocar ni un archivo del plugin.
 *
 * @param array  $clases
 * @param string $elemento
 * @return array
 */
function afectivalab_pmpro_clases_extra( $clases, $elemento ) {
	if ( is_array( $clases ) && in_array( 'pmpro_btn-submit-checkout-paypal', $clases, true ) ) {
		$clases[] = 'btn';
		$clases[] = 'btn-primary';
		$clases[] = 'btn-lg';
	}

	return $clases;
}
add_filter( 'pmpro_element_class', 'afectivalab_pmpro_clases_extra', 10, 2 );

/**
 * El gate: se llama justo después del chequeo de sesión de siempre en cada
 * página que requiere suscripción activa. El equipo de contenido queda
 * exento — no son clientes, son quienes producen el contenido.
 */
function afectivalab_requiere_suscripcion() {
	if ( afectivalab_es_del_equipo() ) {
		return;
	}

	if ( afectivalab_usuario_tiene_suscripcion_activa() ) {
		return;
	}

	wp_safe_redirect( home_url( '/suscribirse' ) );
	exit;
}

/**
 * Procesa el botón "Cancelar" de /mi-cuenta.
 *
 * PMPro cancela el nivel al instante por default — para la gracia hasta fin
 * de período (decisión de producto, ver la nota al principio del archivo),
 * acá se corta el cobro futuro en PayPal ya mismo
 * (`PMPro_Subscription::cancel_at_gateway()`, que cancela SOLO la
 * suscripción en la pasarela, sin tocar el nivel de PMPro) y se guarda hasta
 * cuándo ya está pagado — afectivalab_pmpro_revisar_cancelaciones() recién
 * quita el nivel de PMPro cuando llega esa fecha.
 *
 * @return array{errors: string[], notice: string}
 */
function afectivalab_handle_suscripcion_cancelar() {
	$result = array( 'errors' => array(), 'notice' => '' );

	if ( empty( $_POST['afectivalab_suscripcion_cancelar'] ) ) {
		return $result;
	}

	if ( ! isset( $_POST['afectivalab_suscripcion_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_suscripcion_nonce'] ) ), 'afectivalab_suscripcion_cancelar' ) ) {
		$result['errors'][] = __( 'Tu sesión expiró, por favor intenta de nuevo.', 'afectivalab' );
		return $result;
	}

	if ( ! class_exists( 'PMPro_Subscription' ) || ! function_exists( 'pmpro_next_payment' ) ) {
		$result['errors'][] = __( 'No pudimos cancelar la suscripción, intenta de nuevo.', 'afectivalab' );
		return $result;
	}

	$user_id = get_current_user_id();

	$suscripciones = PMPro_Subscription::get_subscriptions_for_user( $user_id, AFECTIVALAB_PMPRO_LEVEL_ID, 'active' );

	if ( ! empty( $suscripciones ) ) {
		$suscripciones[0]->cancel_at_gateway();
	}

	$hasta = pmpro_next_payment( $user_id, 'success', 'timestamp' );
	update_user_meta( $user_id, '_afectivalab_pmpro_cancelar_en', $hasta ? (int) $hasta : current_time( 'timestamp' ) );

	error_log( '[Afectivalab] usuario ' . $user_id . ' canceló la renovación — acceso hasta ' . gmdate( 'Y-m-d H:i:s', $hasta ? (int) $hasta : current_time( 'timestamp' ) ) . ' UTC' );

	$result['notice'] = __( 'Cancelamos tu suscripción. Sigues teniendo acceso hasta el final del período que ya pagaste.', 'afectivalab' );

	return $result;
}

/**
 * Cron diario: a quien ya pidió cancelar y se le venció el período que
 * tenía pagado, se le quita el nivel de PMPro de verdad (la pasarela ya se
 * había cancelado antes, en afectivalab_handle_suscripcion_cancelar()).
 */
function afectivalab_pmpro_revisar_cancelaciones() {
	if ( ! function_exists( 'pmpro_cancelMembershipLevel' ) ) {
		return;
	}

	$usuarios = get_users(
		array(
			'meta_key'     => '_afectivalab_pmpro_cancelar_en',
			'meta_compare' => 'EXISTS',
			'fields'       => 'ID',
		)
	);

	$ahora = current_time( 'timestamp' );

	foreach ( $usuarios as $user_id ) {
		$cancelar_en = (int) get_user_meta( $user_id, '_afectivalab_pmpro_cancelar_en', true );

		if ( ! $cancelar_en || $ahora < $cancelar_en ) {
			continue;
		}

		pmpro_cancelMembershipLevel( AFECTIVALAB_PMPRO_LEVEL_ID, $user_id );
		delete_user_meta( $user_id, '_afectivalab_pmpro_cancelar_en' );

		error_log( '[Afectivalab] nivel de PMPro retirado tras agotarse el período pagado: usuario=' . $user_id );
	}
}

/**
 * Registra el evento diario de revisión de cancelaciones — solo si no está
 * programado ya.
 */
function afectivalab_pmpro_programar_revision_cancelaciones() {
	if ( ! wp_next_scheduled( 'afectivalab_pmpro_revisar_cancelaciones' ) ) {
		wp_schedule_event( time(), 'daily', 'afectivalab_pmpro_revisar_cancelaciones' );
	}
}
add_action( 'init', 'afectivalab_pmpro_programar_revision_cancelaciones' );
add_action( 'afectivalab_pmpro_revisar_cancelaciones', 'afectivalab_pmpro_revisar_cancelaciones' );
