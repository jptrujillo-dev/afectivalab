<?php
/**
 * Formulario de /contacto — el único formulario del sitio que puede llenar
 * cualquier visitante sin sesión, así que necesita su propia defensa contra
 * spam además del nonce de siempre: un campo trampa ("empresa"), oculto por
 * CSS, que ningún humano llena pero sí los bots que completan formularios
 * a ciegas. Sin CAPTCHA ni servicio externo — no hay Composer y esto alcanza
 * para el volumen de un sitio nuevo.
 *
 * No hay una dirección de correo del cliente confirmada en el proyecto, así
 * que el mensaje va al correo de administración de WordPress
 * (get_option('admin_email')) en vez de inventar una casilla — es el mismo
 * correo que WordPress ya usa para sus propios avisos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Valida y envía el formulario de contacto.
 *
 * @return array{errors: string[], enviado: bool, valores: array}
 */
function afectivalab_handle_contacto_form() {
	$result = array(
		'errors'  => array(),
		'enviado' => false,
		'valores' => array(
			'nombre'  => '',
			'email'   => '',
			'mensaje' => '',
		),
	);

	if ( empty( $_POST['afectivalab_contacto_submit'] ) ) {
		return $result;
	}

	if ( ! isset( $_POST['afectivalab_contacto_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_contacto_nonce'] ) ), 'afectivalab_contacto' ) ) {
		$result['errors'][] = __( 'Tu sesión expiró, por favor intenta de nuevo.', 'afectivalab' );
		return $result;
	}

	// Campo trampa: si viene con algo, es un bot. Se responde como si hubiera
	// salido bien (para no darle pistas de que lo detectamos) pero sin enviar
	// nada de verdad.
	if ( ! empty( $_POST['afectivalab_contacto_empresa'] ) ) {
		$result['enviado'] = true;
		return $result;
	}

	$nombre  = sanitize_text_field( wp_unslash( $_POST['nombre'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$mensaje = sanitize_textarea_field( wp_unslash( $_POST['mensaje'] ?? '' ) );

	$result['valores'] = array(
		'nombre'  => $nombre,
		'email'   => $email,
		'mensaje' => $mensaje,
	);

	if ( '' === $nombre ) {
		$result['errors'][] = __( 'Escribe tu nombre.', 'afectivalab' );
	}

	if ( '' === $email || ! is_email( $email ) ) {
		$result['errors'][] = __( 'Ingresa un correo electrónico válido.', 'afectivalab' );
	}

	if ( '' === trim( $mensaje ) ) {
		$result['errors'][] = __( 'Escribe tu mensaje.', 'afectivalab' );
	}

	if ( $result['errors'] ) {
		return $result;
	}

	$destino = get_option( 'admin_email' );

	$subject = sprintf(
		/* translators: %s: nombre de quien escribe. */
		__( 'Nuevo mensaje de contacto de %s', 'afectivalab' ),
		$nombre
	);

	$message = sprintf(
		/* translators: 1: nombre, 2: correo, 3: mensaje. */
		__( "Nombre: %1\$s\nCorreo: %2\$s\n\nMensaje:\n%3\$s\n", 'afectivalab' ),
		$nombre,
		$email,
		$mensaje
	);

	wp_mail(
		$destino,
		$subject,
		$message,
		array( 'Reply-To: ' . $nombre . ' <' . $email . '>' )
	);

	$result['enviado'] = true;
	$result['valores'] = array(
		'nombre'  => '',
		'email'   => '',
		'mensaje' => '',
	);

	return $result;
}
