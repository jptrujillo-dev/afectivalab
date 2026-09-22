<?php
/**
 * Administración de cursos, microclases y usuarios desde /panel, sin entrar
 * al escritorio de WordPress.
 *
 * Todo esto ya existe en el escritorio; aquí se repite en el front porque el
 * cliente quiere que su equipo trabaje sin salir del panel. Eso significa que
 * **cada acción vuelve a ser una puerta**, así que todas pasan sin excepción
 * por las mismas tres comprobaciones, en este orden:
 *
 *   1. nonce propio de la acción,
 *   2. capacidad concreta (no "está logueado", sino "puede editar este post"),
 *   3. validación de los datos.
 *
 * Y después redirigen, para que recargar no repita la operación.
 *
 * Lo que **no** se hace aquí a propósito: borrar usuarios y tocar cuentas de
 * administrador. Ver afectivalab_panel_cambiar_rol().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Secciones del panel del equipo.
 */
function afectivalab_panel_secciones() {
	return array(
		'resumen'  => __( 'Resumen', 'afectivalab' ),
		'cursos'   => __( 'Cursos', 'afectivalab' ),
		'clases'   => __( 'Microclases', 'afectivalab' ),
		'usuarios' => __( 'Usuarios', 'afectivalab' ),
	);
}

function afectivalab_panel_seccion() {
	$seccion = isset( $_GET['seccion'] ) ? sanitize_key( wp_unslash( $_GET['seccion'] ) ) : 'resumen';

	if ( ! array_key_exists( $seccion, afectivalab_panel_secciones() ) ) {
		return 'resumen';
	}

	// La sección de usuarios es solo para quien administra el sitio.
	if ( 'usuarios' === $seccion && ! current_user_can( 'list_users' ) ) {
		return 'resumen';
	}

	return $seccion;
}

function afectivalab_panel_accion() {
	$accion = isset( $_GET['accion'] ) ? sanitize_key( wp_unslash( $_GET['accion'] ) ) : 'lista';

	return in_array( $accion, array( 'lista', 'nuevo', 'editar' ), true ) ? $accion : 'lista';
}

/**
 * Arma una URL dentro del panel.
 *
 * @param array $args Parámetros a añadir (seccion, accion, id, msg…).
 */
function afectivalab_panel_url( $args = array() ) {
	$base = home_url( '/panel' );

	return $args ? add_query_arg( $args, $base ) : $base;
}

/**
 * Mensajes de confirmación después de una acción.
 */
function afectivalab_panel_mensajes() {
	return array(
		'curso-creado'     => __( 'Curso creado.', 'afectivalab' ),
		'curso-guardado'   => __( 'Cambios guardados.', 'afectivalab' ),
		'curso-eliminado'  => __( 'Curso enviado a la papelera.', 'afectivalab' ),
		'clase-creada'     => __( 'Microclase creada.', 'afectivalab' ),
		'clase-guardada'   => __( 'Cambios guardados.', 'afectivalab' ),
		'clase-eliminada'  => __( 'Microclase enviada a la papelera.', 'afectivalab' ),
		'rol-cambiado'     => __( 'Rol actualizado.', 'afectivalab' ),
	);
}

/**
 * Post del tipo indicado que esta persona sí puede editar.
 *
 * current_user_can( 'edit_post', $id ) es lo que aplica de verdad las reglas
 * del rol (un instructor puede editar lo de sus colegas, pero no borrarlo).
 *
 * @param int    $id
 * @param string $tipo
 * @return WP_Post|null
 */
function afectivalab_panel_post_editable( $id, $tipo ) {
	$post = get_post( absint( $id ) );

	if ( ! $post || $post->post_type !== $tipo ) {
		return null;
	}

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return null;
	}

	return $post;
}

/**
 * Procesa las acciones del panel. Devuelve los errores de validación (si el
 * formulario volvió mal) para que la plantilla los pinte; si todo salió bien,
 * redirige y termina.
 *
 * @return array{errors: string[], valores: array}
 */
function afectivalab_panel_handle() {
	$estado = array(
		'errors'  => array(),
		'valores' => array(),
	);

	if ( ! afectivalab_es_del_equipo() ) {
		return $estado;
	}

	if ( isset( $_POST['afectivalab_panel_accion'] ) ) {
		$accion = sanitize_key( wp_unslash( $_POST['afectivalab_panel_accion'] ) );

		if ( 'guardar_curso' === $accion ) {
			return afectivalab_panel_guardar_curso();
		}

		if ( 'guardar_clase' === $accion ) {
			return afectivalab_panel_guardar_clase();
		}

		if ( 'eliminar_curso' === $accion ) {
			afectivalab_panel_eliminar( AFECTIVALAB_CPT_CURSO, 'cursos', 'curso-eliminado' );
		}

		if ( 'eliminar_clase' === $accion ) {
			afectivalab_panel_eliminar( AFECTIVALAB_CPT_CLASE, 'clases', 'clase-eliminada' );
		}

		if ( 'cambiar_rol' === $accion ) {
			afectivalab_panel_cambiar_rol();
		}
	}

	return $estado;
}

/**
 * Alta y edición de un curso.
 *
 * @return array{errors: string[], valores: array}
 */
function afectivalab_panel_guardar_curso() {
	$estado = array( 'errors' => array(), 'valores' => array() );

	if ( ! isset( $_POST['afectivalab_panel_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_panel_nonce'] ) ), 'afectivalab_panel_curso' ) ) {
		$estado['errors'][] = __( 'Tu sesión expiró, vuelve a intentarlo.', 'afectivalab' );
		return $estado;
	}

	$id    = isset( $_POST['curso_id'] ) ? absint( $_POST['curso_id'] ) : 0;
	$curso = $id ? afectivalab_panel_post_editable( $id, AFECTIVALAB_CPT_CURSO ) : null;

	if ( $id && ! $curso ) {
		$estado['errors'][] = __( 'No puedes editar ese curso.', 'afectivalab' );
		return $estado;
	}

	if ( ! $id && ! current_user_can( 'edit_afectivalab_cursos' ) ) {
		$estado['errors'][] = __( 'No puedes crear cursos.', 'afectivalab' );
		return $estado;
	}

	$valores = array(
		'titulo'    => sanitize_text_field( wp_unslash( $_POST['titulo'] ?? '' ) ),
		'resumen'   => sanitize_textarea_field( wp_unslash( $_POST['resumen'] ?? '' ) ),
		'contenido' => wp_kses_post( wp_unslash( $_POST['contenido'] ?? '' ) ),
		'etapa'     => sanitize_key( wp_unslash( $_POST['etapa'] ?? '' ) ),
		'eje'       => sanitize_key( wp_unslash( $_POST['eje'] ?? '' ) ),
		'habilidad' => sanitize_text_field( wp_unslash( $_POST['habilidad'] ?? '' ) ),
		'estado'    => 'publish' === ( $_POST['estado'] ?? '' ) ? 'publish' : 'draft',
	);

	$estado['valores'] = $valores;

	if ( '' === $valores['titulo'] ) {
		$estado['errors'][] = __( 'El curso necesita un título.', 'afectivalab' );
	}

	if ( ! array_key_exists( $valores['etapa'], afectivalab_etapas() ) ) {
		$estado['errors'][] = __( 'Elige la etapa de edad.', 'afectivalab' );
	}

	if ( ! array_key_exists( $valores['eje'], afectivalab_ejes() ) ) {
		$estado['errors'][] = __( 'Elige el eje temático.', 'afectivalab' );
	}

	// Publicar es una capacidad aparte de editar.
	if ( 'publish' === $valores['estado'] && ! current_user_can( 'publish_afectivalab_cursos' ) ) {
		$valores['estado'] = 'draft';
	}

	if ( $estado['errors'] ) {
		return $estado;
	}

	$datos = array(
		'post_type'    => AFECTIVALAB_CPT_CURSO,
		'post_title'   => $valores['titulo'],
		'post_excerpt' => $valores['resumen'],
		'post_content' => $valores['contenido'],
		'post_status'  => $valores['estado'],
	);

	if ( $curso ) {
		$datos['ID'] = $curso->ID;
		$curso_id    = wp_update_post( $datos, true );
		$mensaje     = 'curso-guardado';
	} else {
		$datos['post_author'] = get_current_user_id();
		$curso_id             = wp_insert_post( $datos, true );
		$mensaje              = 'curso-creado';
	}

	if ( is_wp_error( $curso_id ) ) {
		$estado['errors'][] = __( 'No pudimos guardar el curso, intenta de nuevo.', 'afectivalab' );
		return $estado;
	}

	afectivalab_panel_asignar_termino( $curso_id, $valores['etapa'], AFECTIVALAB_TAX_ETAPA );
	afectivalab_panel_asignar_termino( $curso_id, $valores['eje'], AFECTIVALAB_TAX_EJE );

	update_post_meta( $curso_id, '_afectivalab_habilidad', $valores['habilidad'] );

	$imagen = afectivalab_panel_imagen_destacada( $curso_id );

	if ( is_wp_error( $imagen ) ) {
		// El curso ya quedó guardado; solo falló la imagen, así que se avisa
		// sin perder el resto del trabajo.
		$estado['errors'][] = $imagen->get_error_message();
		return $estado;
	}

	wp_safe_redirect( afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'editar', 'id' => $curso_id, 'msg' => $mensaje ) ) );
	exit;
}

/**
 * Asigna un término buscándolo por slug, nunca por texto libre: pasarle un
 * texto a wp_set_object_terms() crea el término si no existe, y una etapa
 * duplicada rompería la matriz de rutas sin que se note.
 */
function afectivalab_panel_asignar_termino( $post_id, $slug, $taxonomia ) {
	$term = get_term_by( 'slug', $slug, $taxonomia );

	if ( $term ) {
		wp_set_object_terms( $post_id, array( (int) $term->term_id ), $taxonomia );
	}
}

/**
 * Alta y edición de una microclase.
 *
 * @return array{errors: string[], valores: array}
 */
function afectivalab_panel_guardar_clase() {
	$estado = array( 'errors' => array(), 'valores' => array() );

	if ( ! isset( $_POST['afectivalab_panel_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_panel_nonce'] ) ), 'afectivalab_panel_clase' ) ) {
		$estado['errors'][] = __( 'Tu sesión expiró, vuelve a intentarlo.', 'afectivalab' );
		return $estado;
	}

	$id    = isset( $_POST['clase_id'] ) ? absint( $_POST['clase_id'] ) : 0;
	$clase = $id ? afectivalab_panel_post_editable( $id, AFECTIVALAB_CPT_CLASE ) : null;

	if ( $id && ! $clase ) {
		$estado['errors'][] = __( 'No puedes editar esa microclase.', 'afectivalab' );
		return $estado;
	}

	if ( ! $id && ! current_user_can( 'edit_afectivalab_clases' ) ) {
		$estado['errors'][] = __( 'No puedes crear microclases.', 'afectivalab' );
		return $estado;
	}

	$tipo_video = sanitize_key( wp_unslash( $_POST['video_tipo'] ?? 'ninguno' ) );
	if ( ! in_array( $tipo_video, array( 'ninguno', 'url', 'media' ), true ) ) {
		$tipo_video = 'ninguno';
	}

	$tipo_mision = sanitize_key( wp_unslash( $_POST['mision_tipo'] ?? 'ninguna' ) );
	if ( 'ninguna' !== $tipo_mision && ! array_key_exists( $tipo_mision, afectivalab_mision_tipos() ) ) {
		$tipo_mision = 'ninguna';
	}

	$valores = array(
		'titulo'       => sanitize_text_field( wp_unslash( $_POST['titulo'] ?? '' ) ),
		'curso'        => absint( $_POST['curso'] ?? 0 ),
		'orden'        => absint( $_POST['orden'] ?? 0 ),
		'duracion'     => absint( $_POST['duracion'] ?? 0 ),
		'video_tipo'   => $tipo_video,
		'video_url'    => esc_url_raw( wp_unslash( $_POST['video_url'] ?? '' ) ),
		'mision_tipo'  => $tipo_mision,
		'mision_texto' => sanitize_textarea_field( wp_unslash( $_POST['mision_texto'] ?? '' ) ),
		'contenido'    => wp_kses_post( wp_unslash( $_POST['contenido'] ?? '' ) ),
		'estado'       => 'publish' === ( $_POST['estado'] ?? '' ) ? 'publish' : 'draft',
	);

	$estado['valores'] = $valores;

	if ( '' === $valores['titulo'] ) {
		$estado['errors'][] = __( 'La microclase necesita un título.', 'afectivalab' );
	}

	if ( $valores['curso'] && ! afectivalab_panel_post_editable( $valores['curso'], AFECTIVALAB_CPT_CURSO ) ) {
		$estado['errors'][] = __( 'Ese curso no existe o no puedes usarlo.', 'afectivalab' );
	}

	if ( 'url' === $valores['video_tipo'] && '' === $valores['video_url'] ) {
		$estado['errors'][] = __( 'Pega el enlace del video, o elige "sin video".', 'afectivalab' );
	}

	if ( 'ninguna' !== $valores['mision_tipo'] && '' === $valores['mision_texto'] ) {
		$estado['errors'][] = __( 'Escribe qué debe hacer la familia en la misión, o elige "sin misión".', 'afectivalab' );
	}

	if ( 'publish' === $valores['estado'] && ! current_user_can( 'publish_afectivalab_clases' ) ) {
		$valores['estado'] = 'draft';
	}

	if ( $estado['errors'] ) {
		return $estado;
	}

	$datos = array(
		'post_type'    => AFECTIVALAB_CPT_CLASE,
		'post_title'   => $valores['titulo'],
		'post_content' => $valores['contenido'],
		'post_status'  => $valores['estado'],
		'menu_order'   => $valores['orden'],
	);

	if ( $clase ) {
		$datos['ID'] = $clase->ID;
		$clase_id    = wp_update_post( $datos, true );
		$mensaje     = 'clase-guardada';
	} else {
		$datos['post_author'] = get_current_user_id();
		$clase_id             = wp_insert_post( $datos, true );
		$mensaje              = 'clase-creada';
	}

	if ( is_wp_error( $clase_id ) ) {
		$estado['errors'][] = __( 'No pudimos guardar la microclase, intenta de nuevo.', 'afectivalab' );
		return $estado;
	}

	update_post_meta( $clase_id, '_afectivalab_curso', $valores['curso'] );
	update_post_meta( $clase_id, '_afectivalab_duracion', $valores['duracion'] );
	update_post_meta( $clase_id, '_afectivalab_video_tipo', $valores['video_tipo'] );
	update_post_meta( $clase_id, '_afectivalab_video_url', $valores['video_url'] );
	update_post_meta( $clase_id, '_afectivalab_mision_tipo', $valores['mision_tipo'] );
	update_post_meta( $clase_id, '_afectivalab_mision_texto', $valores['mision_texto'] );

	$imagen = afectivalab_panel_imagen_destacada( $clase_id );

	if ( is_wp_error( $imagen ) ) {
		$estado['errors'][] = $imagen->get_error_message();
		$estado['valores']['clase_id'] = $clase_id;
		return $estado;
	}

	$subido = afectivalab_panel_subir_video( $clase_id );

	if ( is_wp_error( $subido ) ) {
		$estado['errors'][] = $subido->get_error_message();
		$estado['valores']['clase_id'] = $clase_id;
		return $estado;
	}

	wp_safe_redirect( afectivalab_panel_url( array( 'seccion' => 'clases', 'accion' => 'editar', 'id' => $clase_id, 'msg' => $mensaje ) ) );
	exit;
}

/**
 * Guarda la imagen destacada: la quita si lo pidieron, o sube la nueva.
 *
 * Sirve igual para cursos y para microclases.
 *
 * @param int $post_id
 * @return true|WP_Error
 */
function afectivalab_panel_imagen_destacada( $post_id ) {
	if ( ! empty( $_POST['quitar_imagen'] ) ) {
		delete_post_thumbnail( $post_id );
	}

	if ( empty( $_FILES['imagen'] ) || empty( $_FILES['imagen']['name'] ) ) {
		return true;
	}

	if ( ! current_user_can( 'upload_files' ) ) {
		return new WP_Error( 'sin_permiso', __( 'No tienes permiso para subir archivos.', 'afectivalab' ) );
	}

	$tipo = wp_check_filetype( sanitize_file_name( $_FILES['imagen']['name'] ) );

	if ( empty( $tipo['type'] ) || ! in_array( $tipo['type'], array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) {
		return new WP_Error( 'tipo_invalido', __( 'La imagen debe ser JPG, PNG o WEBP.', 'afectivalab' ) );
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$attachment_id = media_handle_upload( 'imagen', $post_id );

	if ( is_wp_error( $attachment_id ) ) {
		return new WP_Error( 'subida_fallida', __( 'No pudimos subir la imagen, intenta de nuevo.', 'afectivalab' ) );
	}

	set_post_thumbnail( $post_id, $attachment_id );

	return true;
}

/**
 * Sube el archivo de video de una microclase, si se eligió uno.
 *
 * Se apoya en media_handle_upload(), que ya valida tipo y tamaño según lo que
 * permite el sitio; aquí solo se acota a video y se comprueba la capacidad.
 *
 * @param int $clase_id
 * @return int|null|WP_Error
 */
function afectivalab_panel_subir_video( $clase_id ) {
	if ( empty( $_FILES['video_archivo'] ) || empty( $_FILES['video_archivo']['name'] ) ) {
		return null;
	}

	if ( ! current_user_can( 'upload_files' ) ) {
		return new WP_Error( 'sin_permiso', __( 'No tienes permiso para subir archivos.', 'afectivalab' ) );
	}

	$tipo = wp_check_filetype( sanitize_file_name( $_FILES['video_archivo']['name'] ) );

	if ( empty( $tipo['type'] ) || 0 !== strpos( $tipo['type'], 'video/' ) ) {
		return new WP_Error( 'tipo_invalido', __( 'El archivo debe ser un video.', 'afectivalab' ) );
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$attachment_id = media_handle_upload( 'video_archivo', $clase_id );

	if ( is_wp_error( $attachment_id ) ) {
		return new WP_Error( 'subida_fallida', __( 'No pudimos subir el video, intenta de nuevo.', 'afectivalab' ) );
	}

	update_post_meta( $clase_id, '_afectivalab_video_id', $attachment_id );
	update_post_meta( $clase_id, '_afectivalab_video_tipo', 'media' );

	return $attachment_id;
}

/**
 * Manda un curso o una microclase a la papelera.
 *
 * A la papelera y no borrado definitivo: desde el front es demasiado fácil
 * pulsar de más, y el escritorio permite restaurarlo.
 *
 * @param string $tipo
 * @param string $seccion
 * @param string $mensaje
 */
function afectivalab_panel_eliminar( $tipo, $seccion, $mensaje ) {
	if ( ! isset( $_POST['afectivalab_panel_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_panel_nonce'] ) ), 'afectivalab_panel_eliminar' ) ) {
		return;
	}

	$id   = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$post = get_post( $id );

	if ( ! $post || $post->post_type !== $tipo ) {
		return;
	}

	// delete_post, no edit_post: un instructor puede editar lo de un colega
	// pero no borrárselo (ver inc/roles.php).
	if ( ! current_user_can( 'delete_post', $post->ID ) ) {
		wp_safe_redirect( afectivalab_panel_url( array( 'seccion' => $seccion, 'error' => 'sin-permiso' ) ) );
		exit;
	}

	wp_trash_post( $post->ID );

	wp_safe_redirect( afectivalab_panel_url( array( 'seccion' => $seccion, 'msg' => $mensaje ) ) );
	exit;
}

/**
 * Roles que se pueden asignar desde el panel.
 *
 * **Nunca incluye administrador.** Un formulario del front que pueda repartir
 * el rol de administrador es una escalada de privilegios esperando a pasar:
 * si hace falta crear otro administrador, se hace desde el escritorio.
 */
function afectivalab_panel_roles_asignables() {
	return array(
		'afectivalab_padre'      => __( 'Padre/Madre', 'afectivalab' ),
		'afectivalab_instructor' => __( 'Instructor', 'afectivalab' ),
	);
}

/**
 * Cambia el rol de un usuario.
 *
 * Tres cosas que no se permiten, y por qué:
 * - cambiarse el rol a uno mismo (es la forma más fácil de quedarse fuera),
 * - tocar a un administrador (no se degrada a un admin desde el front),
 * - asignar un rol que no esté en la lista de arriba.
 */
function afectivalab_panel_cambiar_rol() {
	if ( ! isset( $_POST['afectivalab_panel_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_panel_nonce'] ) ), 'afectivalab_panel_usuario' ) ) {
		return;
	}

	if ( ! current_user_can( 'promote_users' ) ) {
		return;
	}

	$user_id = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
	$rol     = sanitize_key( wp_unslash( $_POST['rol'] ?? '' ) );
	$usuario = get_userdata( $user_id );

	if ( ! $usuario || ! array_key_exists( $rol, afectivalab_panel_roles_asignables() ) ) {
		return;
	}

	if ( $user_id === get_current_user_id() || user_can( $usuario, 'manage_options' ) ) {
		wp_safe_redirect( afectivalab_panel_url( array( 'seccion' => 'usuarios', 'error' => 'rol-protegido' ) ) );
		exit;
	}

	$usuario->set_role( $rol );

	wp_safe_redirect( afectivalab_panel_url( array( 'seccion' => 'usuarios', 'msg' => 'rol-cambiado' ) ) );
	exit;
}

/**
 * Errores que llegan por la URL después de una redirección.
 */
function afectivalab_panel_errores_url() {
	return array(
		'sin-permiso'   => __( 'No tienes permiso para hacer eso.', 'afectivalab' ),
		'rol-protegido' => __( 'No puedes cambiar tu propio rol ni el de un administrador. Eso se hace desde el escritorio.', 'afectivalab' ),
	);
}
