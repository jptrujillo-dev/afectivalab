<?php
/**
 * Panel del equipo sin recargas: guardar, publicar/despublicar, eliminar,
 * reordenar microclases y cambiar roles por AJAX.
 *
 * No hay lógica nueva aquí a propósito. Cada formulario del panel manda el
 * mismo campo afectivalab_panel_accion que sin JavaScript, y esta función lo
 * despacha a las mismas funciones de inc/panel-admin.php (nonce, permiso y
 * validación incluidos). Lo único que cambia es la respuesta: JSON con lo
 * que la pantalla necesita para actualizarse en su lugar, en vez de una
 * redirección.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renderiza una pieza del panel a texto, para devolverla en la respuesta.
 *
 * @param string $pieza Nombre dentro de template-parts/panel/.
 * @param array  $args
 * @return string
 */
function afectivalab_panel_pieza( $pieza, $args = array() ) {
	ob_start();
	get_template_part( 'template-parts/panel/' . $pieza, null, $args );
	return trim( ob_get_clean() );
}

/**
 * "Publicado", "Publicada" o "Borrador", según el tipo y el estado.
 *
 * @param string $estado
 * @param string $tipo   Tipo de post.
 */
function afectivalab_panel_estado_etiqueta( $estado, $tipo ) {
	if ( 'publish' !== $estado ) {
		return __( 'Borrador', 'afectivalab' );
	}

	return AFECTIVALAB_CPT_CLASE === $tipo ? __( 'Publicada', 'afectivalab' ) : __( 'Publicado', 'afectivalab' );
}

/**
 * Lo que la pantalla de edición necesita saber después de guardar.
 *
 * @param WP_Post $post
 * @param string  $seccion 'cursos' o 'clases'.
 */
function afectivalab_panel_datos_guardado( $post, $seccion ) {
	return array(
		'id'            => (int) $post->ID,
		'titulo'        => $post->post_title,
		'estado'        => $post->post_status,
		'estado_label'  => afectivalab_panel_estado_etiqueta( $post->post_status, $post->post_type ),
		'url_editar'    => afectivalab_panel_url( array( 'seccion' => $seccion, 'accion' => 'editar', 'id' => $post->ID ) ),
		'url_ver'       => get_permalink( $post ),
		'imagen'        => has_post_thumbnail( $post->ID ) ? get_the_post_thumbnail( $post->ID, 'medium' ) : '',
		'modificado'    => afectivalab_panel_hace( $post->ID ),
	);
}

/**
 * "Guardado hace un momento" o la fecha de la última edición.
 *
 * @param int $post_id
 */
function afectivalab_panel_hace( $post_id ) {
	$fecha = get_post_modified_time( 'U', true, $post_id );

	if ( ! $fecha ) {
		return '';
	}

	if ( time() - $fecha < MINUTE_IN_SECONDS ) {
		return __( 'Guardado hace un momento', 'afectivalab' );
	}

	/* translators: %s: tiempo transcurrido, ej. "5 minutos". */
	return sprintf( __( 'Última edición hace %s', 'afectivalab' ), human_time_diff( $fecha ) );
}

function afectivalab_ajax_panel() {
	if ( ! afectivalab_es_del_equipo() ) {
		wp_send_json_error( array( 'mensaje' => __( 'No tienes acceso al panel de contenido.', 'afectivalab' ) ), 403 );
	}

	$accion   = sanitize_key( wp_unslash( $_POST['afectivalab_panel_accion'] ?? '' ) );
	$invalida = __( 'Esta acción ya no es válida. Recarga la página e intenta de nuevo.', 'afectivalab' );

	switch ( $accion ) {
		case 'guardar_curso':
		case 'guardar_clase':
			$es_curso = 'guardar_curso' === $accion;
			$estado   = $es_curso ? afectivalab_panel_procesar_curso() : afectivalab_panel_procesar_clase();

			// Sin id no se guardó nada: son errores de validación.
			if ( ! $estado['id'] ) {
				wp_send_json_error(
					array(
						'mensaje' => __( 'Revisa lo que falta antes de guardar.', 'afectivalab' ),
						'errores' => $estado['errors'],
					),
					422
				);
			}

			$post  = get_post( $estado['id'] );
			$datos = afectivalab_panel_datos_guardado( $post, $es_curso ? 'cursos' : 'clases' );
			$textos = afectivalab_panel_mensajes();

			$datos['creado']  = in_array( $estado['mensaje'], array( 'curso-creado', 'clase-creada' ), true );
			$datos['mensaje'] = $textos[ $estado['mensaje'] ] ?? __( 'Cambios guardados.', 'afectivalab' );
			// Se guardó, pero algo secundario falló (una imagen o un video):
			// la pantalla lo avisa sin dar por perdido el resto.
			$datos['errores'] = $estado['errors'];

			if ( $es_curso && $datos['creado'] ) {
				$datos['clases_html'] = afectivalab_panel_pieza( 'curso-clases', array( 'curso' => $post ) );
			}

			// Creada desde el modal del curso: la fila nueva para su lista.
			if ( ! $es_curso && 'curso' === sanitize_key( wp_unslash( $_POST['desde'] ?? '' ) ) ) {
				$datos['fila'] = afectivalab_panel_pieza( 'fila-clase', array( 'clase' => $post, 'contexto' => 'curso' ) );
			}

			wp_send_json_success( $datos );
			break;

		case 'eliminar_curso':
		case 'eliminar_clase':
			$es_curso = 'eliminar_curso' === $accion;
			$res      = afectivalab_panel_procesar_eliminar( $es_curso ? AFECTIVALAB_CPT_CURSO : AFECTIVALAB_CPT_CLASE );

			if ( null === $res ) {
				wp_send_json_error( array( 'mensaje' => $invalida ), 400 );
			}

			if ( is_wp_error( $res ) ) {
				wp_send_json_error( array( 'mensaje' => $res->get_error_message() ), 403 );
			}

			$msg = $es_curso ? 'curso-eliminado' : 'clase-eliminada';

			wp_send_json_success(
				array(
					'mensaje'   => afectivalab_panel_mensajes()[ $msg ],
					'url_lista' => afectivalab_panel_url( array( 'seccion' => $es_curso ? 'cursos' : 'clases', 'msg' => $msg ) ),
				)
			);
			break;

		case 'cambiar_estado':
			$res = afectivalab_panel_procesar_estado();

			if ( is_wp_error( $res ) ) {
				wp_send_json_error( array( 'mensaje' => $res->get_error_message() ), 403 );
			}

			wp_send_json_success(
				array(
					'estado'   => $res['estado'],
					'etiqueta' => afectivalab_panel_estado_etiqueta( $res['estado'], $res['post']->post_type ),
					'mensaje'  => afectivalab_panel_mensajes()[ 'publish' === $res['estado'] ? 'publicado' : 'borrador' ],
				)
			);
			break;

		case 'mover_clase':
			$res = afectivalab_panel_procesar_mover();

			if ( is_wp_error( $res ) ) {
				wp_send_json_error( array( 'mensaje' => $res->get_error_message() ), 403 );
			}

			wp_send_json_success( array( 'mensaje' => afectivalab_panel_mensajes()['orden-guardado'] ) );
			break;

		case 'cambiar_rol':
			$res = afectivalab_panel_procesar_rol();

			if ( null === $res ) {
				wp_send_json_error( array( 'mensaje' => $invalida ), 400 );
			}

			if ( is_wp_error( $res ) ) {
				wp_send_json_error( array( 'mensaje' => $res->get_error_message() ), 403 );
			}

			wp_send_json_success( array( 'mensaje' => afectivalab_panel_mensajes()['rol-cambiado'] ) );
			break;
	}

	wp_send_json_error( array( 'mensaje' => $invalida ), 400 );
}
add_action( 'wp_ajax_afectivalab_panel', 'afectivalab_ajax_panel' );
