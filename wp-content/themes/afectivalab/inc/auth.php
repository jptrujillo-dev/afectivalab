<?php
/**
 * Lógica de registro e inicio de sesión para las páginas
 * page-templates/registro.php y page-templates/ingresar.php.
 *
 * Ambas funciones, si el formulario es válido, loguean al usuario y
 * redirigen (terminando la ejecución ahí mismo). Si no, devuelven los
 * errores y los valores ya escritos para volver a pintar el formulario.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Procesa el formulario de /registro si fue enviado.
 *
 * @return array{errors: string[], values: array<string,string>}
 */
function afectivalab_handle_registration() {
	$result = array(
		'errors' => array(),
		'values' => array(
			'nombre' => '',
			'email'  => '',
		),
	);

	if ( empty( $_POST['afectivalab_registro_submit'] ) ) {
		return $result;
	}

	if ( ! isset( $_POST['afectivalab_registro_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_registro_nonce'] ) ), 'afectivalab_registro' ) ) {
		$result['errors'][] = __( 'Tu sesión expiró, por favor intenta de nuevo.', 'afectivalab' );
		return $result;
	}

	$nombre    = isset( $_POST['nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['nombre'] ) ) : '';
	$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$password  = isset( $_POST['password'] ) ? (string) $_POST['password'] : '';
	$password2 = isset( $_POST['password2'] ) ? (string) $_POST['password2'] : '';
	$acepta    = ! empty( $_POST['acepta_terminos'] );

	$result['values']['nombre'] = $nombre;
	$result['values']['email']  = $email;

	if ( '' === $nombre ) {
		$result['errors'][] = __( 'Cuéntanos tu nombre.', 'afectivalab' );
	}

	if ( '' === $email || ! is_email( $email ) ) {
		$result['errors'][] = __( 'Ingresa un correo electrónico válido.', 'afectivalab' );
	} elseif ( email_exists( $email ) ) {
		$result['errors'][] = __( 'Ya existe una cuenta con ese correo. Prueba iniciando sesión.', 'afectivalab' );
	}

	if ( strlen( $password ) < 8 ) {
		$result['errors'][] = __( 'La contraseña debe tener al menos 8 caracteres.', 'afectivalab' );
	} elseif ( $password !== $password2 ) {
		$result['errors'][] = __( 'Las contraseñas no coinciden.', 'afectivalab' );
	}

	if ( ! $acepta ) {
		$result['errors'][] = __( 'Necesitamos que aceptes los Términos y la Política de Privacidad.', 'afectivalab' );
	}

	if ( ! empty( $result['errors'] ) ) {
		return $result;
	}

	$user_login = sanitize_user( current( explode( '@', $email ) ), true );
	if ( '' === $user_login ) {
		$user_login = 'familia';
	}
	$base_login = $user_login;
	$suffix     = 1;
	while ( username_exists( $user_login ) ) {
		++$suffix;
		$user_login = $base_login . $suffix;
	}

	$user_id = wp_insert_user(
		array(
			'user_login'   => $user_login,
			'user_email'   => $email,
			'user_pass'    => $password,
			'display_name' => $nombre,
			'first_name'   => $nombre,
			'role'         => 'afectivalab_padre',
		)
	);

	if ( is_wp_error( $user_id ) ) {
		$result['errors'][] = $user_id->get_error_message();
		return $result;
	}

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id, true );

	wp_safe_redirect( home_url( '/' ) );
	exit;
}

/**
 * Procesa el formulario de /ingresar si fue enviado.
 *
 * @return array{errors: string[], values: array<string,string>}
 */
function afectivalab_handle_login() {
	$result = array(
		'errors' => array(),
		'values' => array(
			'email' => '',
		),
	);

	if ( empty( $_POST['afectivalab_login_submit'] ) ) {
		return $result;
	}

	if ( ! isset( $_POST['afectivalab_login_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_login_nonce'] ) ), 'afectivalab_login' ) ) {
		$result['errors'][] = __( 'Tu sesión expiró, por favor intenta de nuevo.', 'afectivalab' );
		return $result;
	}

	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$password = isset( $_POST['password'] ) ? (string) $_POST['password'] : '';
	$remember = ! empty( $_POST['recordarme'] );

	$result['values']['email'] = $email;

	$user = wp_signon(
		array(
			'user_login'    => $email,
			'user_password' => $password,
			'remember'      => $remember,
		),
		is_ssl()
	);

	if ( is_wp_error( $user ) ) {
		$result['errors'][] = __( 'Correo o contraseña incorrectos.', 'afectivalab' );
		return $result;
	}

	wp_safe_redirect( home_url( '/' ) );
	exit;
}
