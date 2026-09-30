<?php
/**
 * Foto de perfil del usuario (usada en el chip del header y en /mi-cuenta).
 *
 * La foto se guarda como un attachment normal de la librería de medios
 * (vía media_handle_upload, que ya valida tipo/tamaño de archivo) y solo se
 * guarda su ID en user meta. Si el usuario no ha subido nada, se muestra un
 * círculo con su inicial en vez de un ícono genérico.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Devuelve el markup del avatar de un usuario: su foto si tiene una subida,
 * o un círculo con su inicial si no.
 *
 * @param WP_User|int $user  Usuario o ID de usuario.
 * @param int         $size  Tamaño en píxeles (cuadrado).
 * @param string      $class Clases CSS adicionales para el contenedor.
 */
function afectivalab_get_avatar_html( $user, $size = 40, $class = '' ) {
	$user = is_numeric( $user ) ? get_user_by( 'id', $user ) : $user;

	if ( ! $user instanceof WP_User ) {
		return '';
	}

	$attachment_id = (int) get_user_meta( $user->ID, 'afectivalab_avatar_id', true );
	$image_url     = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'thumbnail' ) : '';

	$style = sprintf( 'width:%1$dpx;height:%1$dpx', absint( $size ) );

	if ( $image_url ) {
		return sprintf(
			'<span class="user-avatar %1$s" style="%2$s"><img src="%3$s" alt="" width="%4$d" height="%4$d"></span>',
			esc_attr( $class ),
			esc_attr( $style ),
			esc_url( $image_url ),
			absint( $size )
		);
	}

	$initial = mb_strtoupper( mb_substr( trim( $user->display_name ), 0, 1 ) );

	return sprintf(
		'<span class="user-avatar user-avatar--initial %1$s" style="%2$s">%3$s</span>',
		esc_attr( $class ),
		esc_attr( $style ),
		esc_html( $initial )
	);
}

/**
 * Opciones del menú de cuenta (header y menú móvil), en un solo lugar.
 * "Salir" va aparte porque se muestra separado del resto.
 *
 * @return array[] Cada opción: url, label, desc, icon, tono, activo.
 */
function afectivalab_menu_cuenta_items() {
	$route = get_query_var( 'afectivalab_route' );

	$items = array(
		array(
			'ruta'  => 'panel',
			'label' => __( 'Mi panel', 'afectivalab' ),
			'desc'  => __( 'La ruta y el avance de tus hijos', 'afectivalab' ),
			'icon'  => 'menu-panel',
			'tono'  => 'purple',
		),
	);

	if ( afectivalab_es_del_equipo() ) {
		$items[] = array(
			'url'   => admin_url(),
			'label' => __( 'Escritorio', 'afectivalab' ),
			'desc'  => __( 'Cargar y editar el contenido', 'afectivalab' ),
			'icon'  => 'menu-escritorio',
			'tono'  => 'green',
		);
	} else {
		$items[] = array(
			'ruta'  => 'mis-hijos',
			'label' => __( 'Mis hijos', 'afectivalab' ),
			'desc'  => __( 'Agregar o editar perfiles', 'afectivalab' ),
			'icon'  => 'menu-hijos',
			'tono'  => 'green',
		);
		$items[] = array(
			'ruta'  => 'mundos',
			'label' => __( 'Explorar mundos', 'afectivalab' ),
			'desc'  => __( 'Todos los cursos, por tema', 'afectivalab' ),
			'icon'  => 'menu-mundos',
			'tono'  => 'yellow',
		);
	}

	$items[] = array(
		'ruta'  => 'mi-cuenta',
		'label' => __( 'Mi cuenta', 'afectivalab' ),
		'desc'  => __( 'Tus datos, foto y suscripción', 'afectivalab' ),
		'icon'  => 'menu-cuenta',
		'tono'  => 'purple',
	);

	foreach ( $items as &$item ) {
		if ( isset( $item['ruta'] ) ) {
			$item['url']    = home_url( '/' . $item['ruta'] );
			$item['activo'] = $item['ruta'] === $route;
		} else {
			$item['activo'] = false;
		}
	}
	unset( $item );

	return $items;
}

/**
 * Procesa la subida de una nueva foto de perfil desde /mi-cuenta.
 *
 * @return array{errors: string[], success: bool}
 */
function afectivalab_handle_avatar_upload() {
	$result = array(
		'errors'  => array(),
		'success' => false,
	);

	if ( empty( $_POST['afectivalab_avatar_submit'] ) ) {
		return $result;
	}

	if ( ! isset( $_POST['afectivalab_avatar_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_avatar_nonce'] ) ), 'afectivalab_avatar' ) ) {
		$result['errors'][] = __( 'Tu sesión expiró, por favor intenta de nuevo.', 'afectivalab' );
		return $result;
	}

	if ( empty( $_FILES['avatar'] ) || empty( $_FILES['avatar']['name'] ) ) {
		$result['errors'][] = __( 'Elige una imagen antes de guardar.', 'afectivalab' );
		return $result;
	}

	$allowed_types = array( 'image/jpeg', 'image/png', 'image/webp' );
	$file_type     = wp_check_filetype( $_FILES['avatar']['name'] );

	if ( ! in_array( $file_type['type'], $allowed_types, true ) ) {
		$result['errors'][] = __( 'La foto debe ser JPG, PNG o WEBP.', 'afectivalab' );
		return $result;
	}

	if ( $_FILES['avatar']['size'] > MB_IN_BYTES ) {
		$result['errors'][] = __( 'La foto no debe pesar más de 1MB.', 'afectivalab' );
		return $result;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$attachment_id = media_handle_upload( 'avatar', 0 );

	if ( is_wp_error( $attachment_id ) ) {
		$result['errors'][] = __( 'No pudimos subir la foto, intenta de nuevo.', 'afectivalab' );
		return $result;
	}

	$user_id      = get_current_user_id();
	$previous_id  = (int) get_user_meta( $user_id, 'afectivalab_avatar_id', true );

	update_user_meta( $user_id, 'afectivalab_avatar_id', $attachment_id );

	// La foto anterior ya no la usa nadie más: se borra para no dejar archivos huérfanos.
	if ( $previous_id && $previous_id !== $attachment_id ) {
		wp_delete_attachment( $previous_id, true );
	}

	$result['success'] = true;

	return $result;
}

/**
 * La misma subida de foto de perfil, por AJAX (assets/js/cuenta.js): así la
 * página no se recarga y la foto nueva aparece al instante en la tarjeta y
 * en el header. Reutiliza afectivalab_handle_avatar_upload() tal cual, con
 * las mismas reglas (nonce, JPG/PNG/WEBP, 1MB); el formulario normal sigue
 * funcionando si JS no carga.
 */
function afectivalab_ajax_avatar() {
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'mensaje' => __( 'Tu sesión expiró. Recarga la página e intenta de nuevo.', 'afectivalab' ) ), 401 );
	}

	$_POST['afectivalab_avatar_submit'] = 1;

	$resultado = afectivalab_handle_avatar_upload();

	if ( ! $resultado['success'] ) {
		wp_send_json_error(
			array(
				'mensaje' => $resultado['errors'] ? implode( ' ', $resultado['errors'] ) : __( 'No pudimos subir la foto, intenta de nuevo.', 'afectivalab' ),
			),
			400
		);
	}

	$user = wp_get_current_user();

	wp_send_json_success(
		array(
			'mensaje' => __( 'Tu foto de perfil se actualizó.', 'afectivalab' ),
			'avatar'  => afectivalab_get_avatar_html( $user, 96 ),
			'header'  => afectivalab_get_avatar_html( $user, 32 ),
			'menu'    => afectivalab_get_avatar_html( $user, 44 ),
			'movil'   => afectivalab_get_avatar_html( $user, 40 ),
		)
	);
}
add_action( 'wp_ajax_afectivalab_avatar', 'afectivalab_ajax_avatar' );

/**
 * Preferencias de notificación de /mi-cuenta. Hoy solo se guardan (user
 * meta) — todavía no existe la infraestructura de envío (cron + plantillas
 * de correo) que las dispare de verdad, así que activarlas no manda nada
 * todavía. Se guardan desde ya para no tener que migrar datos el día que
 * ese envío se implemente. Sin configurar, aparecen activadas por defecto
 * (igual que se ofrecen en el formulario).
 *
 * @param int $user_id
 * @return array{nuevas_misiones: bool, resumen_progreso: bool}
 */
function afectivalab_preferencias_notificacion( $user_id ) {
	$nuevas_misiones  = get_user_meta( $user_id, '_afectivalab_pref_nuevas_misiones', true );
	$resumen_progreso = get_user_meta( $user_id, '_afectivalab_pref_resumen_progreso', true );

	return array(
		'nuevas_misiones'  => '' === $nuevas_misiones ? true : (bool) $nuevas_misiones,
		'resumen_progreso' => '' === $resumen_progreso ? true : (bool) $resumen_progreso,
	);
}

/**
 * Procesa el formulario de preferencias de notificación de /mi-cuenta.
 *
 * @return array{success: bool}
 */
function afectivalab_handle_preferencias_form() {
	$result = array( 'success' => false );

	if ( empty( $_POST['afectivalab_preferencias_submit'] ) ) {
		return $result;
	}

	if ( ! isset( $_POST['afectivalab_preferencias_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_preferencias_nonce'] ) ), 'afectivalab_preferencias' ) ) {
		return $result;
	}

	$user_id = get_current_user_id();

	update_user_meta( $user_id, '_afectivalab_pref_nuevas_misiones', ! empty( $_POST['pref_nuevas_misiones'] ) ? 1 : 0 );
	update_user_meta( $user_id, '_afectivalab_pref_resumen_progreso', ! empty( $_POST['pref_resumen_progreso'] ) ? 1 : 0 );

	$result['success'] = true;

	return $result;
}

/**
 * Procesa el formulario de "Mi perfil" de /mi-cuenta: nombre y correo. El
 * cambio de contraseña NO se hace acá — reusa el flujo real de /recuperar
 * (con su correo de verdad y su key de un solo uso vía
 * get_password_reset_key()) en vez de reimplementar un cambio inline con
 * reautenticación propia.
 *
 * @return array{errors: string[], success: bool}
 */
function afectivalab_handle_perfil_form() {
	$result = array(
		'errors'  => array(),
		'success' => false,
	);

	if ( empty( $_POST['afectivalab_perfil_submit'] ) ) {
		return $result;
	}

	if ( ! isset( $_POST['afectivalab_perfil_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_perfil_nonce'] ) ), 'afectivalab_perfil' ) ) {
		$result['errors'][] = __( 'Tu sesión expiró, por favor intenta de nuevo.', 'afectivalab' );
		return $result;
	}

	$user_id = get_current_user_id();
	$nombre  = isset( $_POST['nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['nombre'] ) ) : '';
	$correo  = isset( $_POST['correo'] ) ? sanitize_email( wp_unslash( $_POST['correo'] ) ) : '';

	if ( '' === trim( $nombre ) ) {
		$result['errors'][] = __( 'El nombre no puede quedar vacío.', 'afectivalab' );
	}

	if ( ! is_email( $correo ) ) {
		$result['errors'][] = __( 'Ese correo no parece válido.', 'afectivalab' );
	} else {
		$dueno_actual = email_exists( $correo );
		if ( $dueno_actual && (int) $dueno_actual !== $user_id ) {
			$result['errors'][] = __( 'Ya hay una cuenta registrada con ese correo.', 'afectivalab' );
		}
	}

	if ( ! empty( $result['errors'] ) ) {
		return $result;
	}

	wp_update_user(
		array(
			'ID'           => $user_id,
			'display_name' => $nombre,
			'user_email'   => $correo,
		)
	);

	$result['success'] = true;

	return $result;
}

/**
 * Cambio de contraseña desde el modal de /mi-cuenta — la persona ya está
 * logueada, así que no tiene sentido mandarla por el flujo de "olvidé mi
 * contraseña" (correo + key de un solo uso, pensado para quien no puede
 * entrar). Pide la contraseña actual para confirmar que es ella, no
 * alguien que encontró la sesión abierta.
 *
 * wp_set_password() por sí sola invalida las sesiones del usuario (cambia
 * el hash que valida la cookie de sesión) — así que justo después hay que
 * volver a autenticar a la misma persona en esta misma pestaña, si no
 * quedaría deslogueada apenas cambia su propia contraseña.
 *
 * Mismo patrón que afectivalab_ajax_login() en inc/auth.php:
 * check_ajax_referer() + wp_send_json_error()/_success(), vía
 * admin-ajax.php.
 *
 * @return void
 */
function afectivalab_ajax_cambiar_password() {
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Tu sesión expiró, por favor recarga la página.', 'afectivalab' ) ) );
	}

	if ( ! check_ajax_referer( 'afectivalab_cambiar_password', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Tu sesión expiró, por favor recarga la página.', 'afectivalab' ) ) );
	}

	$user_id = get_current_user_id();
	$user    = wp_get_current_user();

	$actual = isset( $_POST['password_actual'] ) ? (string) $_POST['password_actual'] : '';
	$nueva  = isset( $_POST['password_nueva'] ) ? (string) $_POST['password_nueva'] : '';
	$nueva2 = isset( $_POST['password_nueva_2'] ) ? (string) $_POST['password_nueva_2'] : '';

	if ( ! wp_check_password( $actual, $user->user_pass, $user_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Tu contraseña actual no es correcta.', 'afectivalab' ) ) );
	}

	if ( strlen( $nueva ) < 8 ) {
		wp_send_json_error( array( 'message' => __( 'La nueva contraseña debe tener al menos 8 caracteres.', 'afectivalab' ) ) );
	}

	if ( $nueva !== $nueva2 ) {
		wp_send_json_error( array( 'message' => __( 'Las contraseñas nuevas no coinciden.', 'afectivalab' ) ) );
	}

	if ( $actual === $nueva ) {
		wp_send_json_error( array( 'message' => __( 'La nueva contraseña debe ser distinta a la actual.', 'afectivalab' ) ) );
	}

	wp_set_password( $nueva, $user_id );

	// Reautenticar de inmediato: wp_set_password() ya invalidó la sesión.
	wp_clear_auth_cookie();
	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id );

	wp_send_json_success( array( 'message' => __( 'Tu contraseña se actualizó.', 'afectivalab' ) ) );
}
add_action( 'wp_ajax_afectivalab_cambiar_password', 'afectivalab_ajax_cambiar_password' );
