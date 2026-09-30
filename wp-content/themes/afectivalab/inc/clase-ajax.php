<?php
/**
 * Las interacciones de una microclase por AJAX: responder el caso práctico,
 * resolver o posponer la misión (con su foto) y "marcar como vista". Así la
 * familia no pierde el lugar de la página en cada clic.
 *
 * Cada respuesta devuelve el HTML ya actualizado de los bloques que
 * cambiaron, pintado con las mismas piezas de template-parts/clase/ que usa
 * la página: nunca hay dos versiones del marcado. JS los reemplaza en su
 * lugar (assets/js/clase.js).
 *
 * Las comprobaciones son las mismas que las de los formularios normales, que
 * siguen existiendo para cuando JS no carga: sesión, que el hijo sea del
 * usuario, suscripción activa (o ser del equipo), que la clase esté
 * desbloqueada para ese hijo, y el nonce propio de cada formulario.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Valida la petición y devuelve la clase y el hijo, o corta con un error.
 *
 * @param string $nonce_campo  Nombre del campo del nonce en el formulario.
 * @param string $nonce_accion Prefijo de la acción del nonce (se le suma el id de la clase).
 * @return array{clase_id: int, hijo_id: int}
 */
function afectivalab_ajax_clase_contexto( $nonce_campo, $nonce_accion ) {
	$error_sesion = __( 'Tu sesión expiró. Recarga la página e intenta de nuevo.', 'afectivalab' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'mensaje' => $error_sesion ), 401 );
	}

	$clase_id = isset( $_POST['clase'] ) ? absint( $_POST['clase'] ) : 0;
	$hijo     = afectivalab_get_hijo_propio( isset( $_POST['hijo'] ) ? absint( $_POST['hijo'] ) : 0 );
	$clase    = $clase_id ? get_post( $clase_id ) : null;

	if ( ! $hijo || ! $clase || AFECTIVALAB_CPT_CLASE !== $clase->post_type ) {
		wp_send_json_error( array( 'mensaje' => __( 'No encontramos esta clase. Recarga la página.', 'afectivalab' ) ), 404 );
	}

	$nonce = isset( $_POST[ $nonce_campo ] ) ? sanitize_text_field( wp_unslash( $_POST[ $nonce_campo ] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, $nonce_accion . $clase_id ) ) {
		wp_send_json_error( array( 'mensaje' => $error_sesion ), 403 );
	}

	if ( ! afectivalab_es_del_equipo() && ! afectivalab_usuario_tiene_suscripcion_activa() ) {
		wp_send_json_error( array( 'mensaje' => __( 'Tu suscripción no está activa.', 'afectivalab' ) ), 403 );
	}

	if ( ! afectivalab_clase_desbloqueada( $hijo->ID, $clase_id ) ) {
		wp_send_json_error( array( 'mensaje' => __( 'Esta clase todavía no está disponible.', 'afectivalab' ) ), 403 );
	}

	return array(
		'clase_id' => $clase_id,
		'hijo_id'  => (int) $hijo->ID,
	);
}

/**
 * HTML de una pieza de template-parts/clase/.
 *
 * @param string $pieza 'caso' | 'accion' | 'progreso'.
 * @param array  $args
 * @return string
 */
function afectivalab_clase_pieza( $pieza, $args ) {
	ob_start();
	get_template_part( 'template-parts/clase/' . $pieza, null, $args );
	return (string) ob_get_clean();
}

/**
 * Los bloques que cambian cuando la clase se completa.
 *
 * @param array $ctx
 * @return array
 */
function afectivalab_clase_bloques_completada( $ctx ) {
	return array(
		'accion'   => afectivalab_clase_pieza( 'accion', $ctx ),
		'progreso' => afectivalab_clase_pieza( 'progreso', $ctx ),
	);
}

function afectivalab_ajax_clase_caso() {
	$ctx  = afectivalab_ajax_clase_contexto( 'afectivalab_caso_nonce', 'afectivalab_caso_' );
	$paso = isset( $_POST['afectivalab_caso_paso'] ) ? sanitize_key( wp_unslash( $_POST['afectivalab_caso_paso'] ) ) : '';

	if ( ! in_array( $paso, array( 'paso1', 'paso2' ), true ) || ! isset( $_POST['afectivalab_caso_opcion'] ) ) {
		wp_send_json_error( array( 'mensaje' => __( 'Elige una de las opciones.', 'afectivalab' ) ), 400 );
	}

	if ( ! afectivalab_caso_responder( $ctx['hijo_id'], $ctx['clase_id'], $paso, absint( $_POST['afectivalab_caso_opcion'] ) ) ) {
		wp_send_json_error( array( 'mensaje' => __( 'No pudimos guardar tu respuesta. Recarga la página e intenta de nuevo.', 'afectivalab' ) ), 400 );
	}

	wp_send_json_success(
		array(
			'caso' => afectivalab_clase_pieza( 'caso', $ctx ),
		)
	);
}
add_action( 'wp_ajax_afectivalab_clase_caso', 'afectivalab_ajax_clase_caso' );

function afectivalab_ajax_clase_vista() {
	$ctx = afectivalab_ajax_clase_contexto( 'afectivalab_clase_nonce', 'afectivalab_clase_' );

	// Una clase con misión se completa resolviendo la misión, no con este
	// botón (decisión de producto, ver inc/misiones.php).
	if ( afectivalab_clase_mision( $ctx['clase_id'] ) ) {
		wp_send_json_error( array( 'mensaje' => __( 'Esta clase se completa haciendo su misión.', 'afectivalab' ) ), 400 );
	}

	afectivalab_marcar_clase( $ctx['hijo_id'], $ctx['clase_id'], true );

	wp_send_json_success( afectivalab_clase_bloques_completada( $ctx ) );
}
add_action( 'wp_ajax_afectivalab_clase_vista', 'afectivalab_ajax_clase_vista' );

function afectivalab_ajax_clase_mision() {
	$ctx    = afectivalab_ajax_clase_contexto( 'afectivalab_mision_nonce', 'afectivalab_mision_' );
	$accion = isset( $_POST['afectivalab_mision_accion'] ) ? sanitize_key( wp_unslash( $_POST['afectivalab_mision_accion'] ) ) : '';
	$mision = afectivalab_clase_mision( $ctx['clase_id'] );

	if ( ! $mision || ! in_array( $accion, array( 'hecha', 'despues' ), true ) ) {
		wp_send_json_error( array( 'mensaje' => __( 'No pudimos procesar la misión. Recarga la página.', 'afectivalab' ) ), 400 );
	}

	if ( 'despues' === $accion ) {
		afectivalab_posponer_mision( $ctx['hijo_id'], $ctx['clase_id'] );

		wp_send_json_success(
			array(
				'accion'  => afectivalab_clase_pieza( 'accion', $ctx ),
				'mensaje' => __( 'Listo, la dejamos pendiente. Cuando la hagan, vuelve aquí para marcarla.', 'afectivalab' ),
			)
		);
	}

	$evidencia_id = 0;

	if ( $mision['requiere_evidencia'] ) {
		// Las mismas reglas que el formulario normal (JPG/PNG/WEBP, 3MB):
		// JS ya las revisa antes de enviar, pero el servidor no confía en eso.
		$subida = afectivalab_subir_evidencia_mision( $ctx['clase_id'] );

		if ( is_wp_error( $subida ) ) {
			wp_send_json_error( array( 'mensaje' => $subida->get_error_message() ), 400 );
		}

		if ( ! $subida ) {
			wp_send_json_error( array( 'mensaje' => __( 'Sube una foto para completar esta misión.', 'afectivalab' ) ), 400 );
		}

		$evidencia_id = (int) $subida;
	}

	afectivalab_completar_mision( $ctx['hijo_id'], $ctx['clase_id'], $evidencia_id );

	wp_send_json_success( afectivalab_clase_bloques_completada( $ctx ) );
}
add_action( 'wp_ajax_afectivalab_clase_mision', 'afectivalab_ajax_clase_mision' );
