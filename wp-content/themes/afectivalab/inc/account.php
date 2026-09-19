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

	if ( $_FILES['avatar']['size'] > 3 * MB_IN_BYTES ) {
		$result['errors'][] = __( 'La foto no debe pesar más de 3MB.', 'afectivalab' );
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
