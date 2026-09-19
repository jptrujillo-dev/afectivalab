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

/**
 * Procesa el formulario de /recuperar (pedir el link de restablecimiento).
 *
 * A propósito no usa el email por defecto de retrieve_password(): esa
 * función arma su mensaje apuntando a wp-login.php, y acá queremos que el
 * link lleve a nuestra propia página /restablecer. Se genera la key con la
 * misma función que usa el core y se arma un correo propio.
 *
 * @return array{errors: string[], sent: bool, values: array<string,string>}
 */
function afectivalab_handle_forgot_password() {
	$result = array(
		'errors' => array(),
		'sent'   => false,
		'values' => array(
			'email' => '',
		),
	);

	if ( empty( $_POST['afectivalab_recuperar_submit'] ) ) {
		return $result;
	}

	if ( ! isset( $_POST['afectivalab_recuperar_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_recuperar_nonce'] ) ), 'afectivalab_recuperar' ) ) {
		$result['errors'][] = __( 'Tu sesión expiró, por favor intenta de nuevo.', 'afectivalab' );
		return $result;
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	$result['values']['email'] = $email;

	if ( '' === $email || ! is_email( $email ) ) {
		$result['errors'][] = __( 'Ingresa un correo electrónico válido.', 'afectivalab' );
		return $result;
	}

	$user = get_user_by( 'email', $email );

	// No decimos si el correo existe o no (evita filtrar qué correos están
	// registrados) — en ambos casos se muestra el mismo mensaje de éxito.
	if ( $user ) {
		$key = get_password_reset_key( $user );

		if ( ! is_wp_error( $key ) ) {
			$reset_url = add_query_arg(
				array(
					'login' => rawurlencode( $user->user_login ),
					'key'   => rawurlencode( $key ),
				),
				home_url( '/restablecer' )
			);

			$subject = sprintf(
				/* translators: %s: nombre del sitio. */
				__( 'Restablece tu contraseña de %s', 'afectivalab' ),
				get_bloginfo( 'name' )
			);

			$message = sprintf(
				/* translators: 1: nombre del usuario, 2: link para restablecer contraseña. */
				__( "Hola %1\$s,\n\nRecibimos una solicitud para restablecer tu contraseña. Si fuiste tú, entra a este link para elegir una nueva:\n\n%2\$s\n\nEste link expira en 24 horas. Si no pediste esto, puedes ignorar el correo.\n", 'afectivalab' ),
				$user->display_name,
				$reset_url
			);

			wp_mail( $user->user_email, $subject, $message );
		}
	}

	$result['sent'] = true;

	return $result;
}

/**
 * Valida el link de /restablecer (login + key por query string).
 *
 * @return WP_User|null Usuario si el link es válido, null si no.
 */
function afectivalab_get_reset_password_user() {
	$login = isset( $_GET['login'] ) ? sanitize_text_field( wp_unslash( $_GET['login'] ) ) : '';
	$key   = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';

	if ( '' === $login || '' === $key ) {
		return null;
	}

	$user = check_password_reset_key( $key, $login );

	return is_wp_error( $user ) ? null : $user;
}

/**
 * Procesa el formulario de /restablecer (elegir la nueva contraseña).
 *
 * @param WP_User $user El usuario ya validado por afectivalab_get_reset_password_user().
 * @return array{errors: string[]}
 */
function afectivalab_handle_reset_password( $user ) {
	$result = array(
		'errors' => array(),
	);

	if ( empty( $_POST['afectivalab_restablecer_submit'] ) ) {
		return $result;
	}

	if ( ! isset( $_POST['afectivalab_restablecer_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_restablecer_nonce'] ) ), 'afectivalab_restablecer' ) ) {
		$result['errors'][] = __( 'Tu sesión expiró, por favor intenta de nuevo.', 'afectivalab' );
		return $result;
	}

	$password  = isset( $_POST['password'] ) ? (string) $_POST['password'] : '';
	$password2 = isset( $_POST['password2'] ) ? (string) $_POST['password2'] : '';

	if ( strlen( $password ) < 8 ) {
		$result['errors'][] = __( 'La contraseña debe tener al menos 8 caracteres.', 'afectivalab' );
	} elseif ( $password !== $password2 ) {
		$result['errors'][] = __( 'Las contraseñas no coinciden.', 'afectivalab' );
	}

	if ( ! empty( $result['errors'] ) ) {
		return $result;
	}

	reset_password( $user, $password );

	wp_safe_redirect( add_query_arg( 'reset', 'ok', home_url( '/ingresar' ) ) );
	exit;
}
