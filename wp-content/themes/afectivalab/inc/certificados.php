<?php
/**
 * Certificado de finalización de un curso.
 *
 * Decisiones confirmadas por el usuario:
 * - El protagonista es el **hijo**: su nombre es el titular del certificado
 *   ("Juanito completó el curso..."). El padre/madre aparece igual, pero en
 *   una línea secundaria ("Acompañado por..."), no como el nombre principal
 *   — cambio pedido después de ver la primera versión, que ponía al padre
 *   arriba. Los datos (afectivalab_datos_certificado()) siguen devolviendo
 *   ambos nombres; lo que cambió es solo cuál manda en la plantilla.
 * - Se genera como una **página imprimible**, no un archivo .pdf armado en
 *   el servidor: el hosting no tiene Composer, así que sumar una librería de
 *   PDF sería una dependencia nueva sin poder verificar su código desde
 *   aquí. La página trae su propio CSS de impresión y un botón que abre el
 *   diálogo "Guardar como PDF" del navegador — cero dependencias nuevas, y
 *   funciona en cualquier hosting.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * La fecha en que un hijo terminó un curso.
 *
 * Se registra la primera vez que se detecta el curso completo (patrón
 * perezoso: no hace falta enganchar esto a cada sitio donde se marca una
 * clase, alcanza con guardarla la primera vez que alguien la pide después de
 * completarlo). Guardada una vez, no se vuelve a tocar — si el padre
 * desmarca y vuelve a completar una clase, la fecha del logro no debería
 * moverse.
 *
 * @param int $hijo_id
 * @param int $curso_id
 * @return string Fecha en formato mysql, o cadena vacía si el curso no está completo.
 */
function afectivalab_curso_fecha_completado( $hijo_id, $curso_id ) {
	$curso_id = absint( $curso_id );
	$fechas   = get_post_meta( $hijo_id, '_afectivalab_cursos_completados', true );
	$fechas   = is_array( $fechas ) ? $fechas : array();

	if ( isset( $fechas[ $curso_id ] ) ) {
		return $fechas[ $curso_id ];
	}

	if ( ! afectivalab_progreso_curso( $hijo_id, $curso_id )['completo'] ) {
		return '';
	}

	$fechas[ $curso_id ] = current_time( 'mysql' );
	update_post_meta( $hijo_id, '_afectivalab_cursos_completados', $fechas );

	return $fechas[ $curso_id ];
}

/**
 * URL del certificado de un hijo para un curso, o cadena vacía si ese curso
 * todavía no está completo para él (no se ofrece un enlace a un certificado
 * que no se puede emitir).
 *
 * @param int $hijo_id
 * @param int $curso_id
 * @return string
 */
function afectivalab_certificado_url( $hijo_id, $curso_id ) {
	if ( ! afectivalab_progreso_curso( $hijo_id, $curso_id )['completo'] ) {
		return '';
	}

	return add_query_arg(
		array( 'hijo' => absint( $hijo_id ), 'curso' => absint( $curso_id ) ),
		home_url( '/certificado' )
	);
}

/**
 * Los datos para imprimir el certificado, con todas las comprobaciones de
 * seguridad: que el hijo sea del padre que pide el certificado y que el
 * curso esté de verdad completo para él. Devuelve null si cualquiera de las
 * dos cosas falla — la plantilla no debe mostrar nada en ese caso.
 *
 * @param int $hijo_id
 * @param int $curso_id
 * @param int $user_id Padre que pide el certificado. Por defecto, el usuario actual.
 * @return array{padre: string, hijo: string, curso: string, habilidad: string, fecha: string}|null
 */
function afectivalab_datos_certificado( $hijo_id, $curso_id, $user_id = 0 ) {
	$hijo = afectivalab_get_hijo_propio( $hijo_id, $user_id );

	if ( ! $hijo ) {
		return null;
	}

	$curso_id = absint( $curso_id );
	$curso    = get_post( $curso_id );

	if ( ! $curso || AFECTIVALAB_CPT_CURSO !== $curso->post_type ) {
		return null;
	}

	$fecha = afectivalab_curso_fecha_completado( $hijo->ID, $curso_id );

	if ( '' === $fecha ) {
		return null;
	}

	$padre     = get_userdata( $user_id ? $user_id : get_current_user_id() );
	$habilidad = get_post_meta( $curso_id, '_afectivalab_habilidad', true );

	return array(
		'padre'     => $padre ? $padre->display_name : '',
		'hijo'      => $hijo->post_title,
		'curso'     => $curso->post_title,
		'habilidad' => $habilidad ? $habilidad : $curso->post_title,
		// date_i18n() y no una fecha en inglés: el sitio es en español y el
		// certificado es un documento para imprimir y guardar.
		'fecha'     => date_i18n( 'j \d\e F \d\e Y', strtotime( $fecha ) ),
	);
}
