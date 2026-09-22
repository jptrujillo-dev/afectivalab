<?php
/**
 * Progreso de cada hijo por microclase.
 *
 * El progreso es **por hijo, no por cuenta**: un padre con tres hijos lleva
 * tres avances distintos del mismo curso, porque la ruta de cada uno depende
 * de su edad. Por eso se guarda como post meta del perfil del hijo y no como
 * user meta del padre.
 *
 * Se guarda una sola lista de ids de microclase completadas. Alcanza para
 * calcular el avance de cualquier curso (cuántas de sus clases están en la
 * lista) sin tener que duplicar el dato por curso.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ids de las microclases que este hijo ya completó.
 *
 * @param int $hijo_id
 * @return int[]
 */
function afectivalab_clases_completadas( $hijo_id ) {
	$ids = get_post_meta( $hijo_id, '_afectivalab_clases_completadas', true );

	return is_array( $ids ) ? array_map( 'absint', $ids ) : array();
}

/**
 * @param int $hijo_id
 * @param int $clase_id
 */
function afectivalab_clase_completada( $hijo_id, $clase_id ) {
	return in_array( absint( $clase_id ), afectivalab_clases_completadas( $hijo_id ), true );
}

/**
 * Marca o desmarca una microclase como completada para un hijo.
 *
 * @param int  $hijo_id
 * @param int  $clase_id
 * @param bool $completada
 */
function afectivalab_marcar_clase( $hijo_id, $clase_id, $completada = true ) {
	$clase_id = absint( $clase_id );
	$ids      = afectivalab_clases_completadas( $hijo_id );

	if ( $completada ) {
		if ( ! in_array( $clase_id, $ids, true ) ) {
			$ids[] = $clase_id;
		}
	} else {
		$ids = array_diff( $ids, array( $clase_id ) );
	}

	update_post_meta( $hijo_id, '_afectivalab_clases_completadas', array_values( $ids ) );
}

/**
 * Avance de un hijo en un curso.
 *
 * @param int $hijo_id
 * @param int $curso_id
 * @return array{total: int, hechas: int, porcentaje: int, completo: bool, siguiente: WP_Post|null}
 */
function afectivalab_progreso_curso( $hijo_id, $curso_id ) {
	$clases      = afectivalab_clases_del_curso( $curso_id );
	$completadas = afectivalab_clases_completadas( $hijo_id );

	$total     = count( $clases );
	$hechas    = 0;
	$siguiente = null;

	foreach ( $clases as $clase ) {
		if ( in_array( $clase->ID, $completadas, true ) ) {
			$hechas++;
			continue;
		}

		// La siguiente es la primera sin completar, siguiendo el orden que
		// definió el instructor.
		if ( null === $siguiente ) {
			$siguiente = $clase;
		}
	}

	return array(
		'total'      => $total,
		'hechas'     => $hechas,
		'porcentaje' => $total ? (int) round( $hechas / $total * 100 ) : 0,
		'completo'   => $total > 0 && $hechas === $total,
		'siguiente'  => $siguiente,
	);
}

/**
 * Estado de cada microclase de un curso para pintar la ruta: completada, la
 * que toca ahora, o todavía bloqueada.
 *
 * Las clases se desbloquean en orden — es lo que convierte la lista en un
 * camino y no en un menú. La primera sin completar es la que toca; las de
 * después esperan.
 *
 * @param int $hijo_id
 * @param int $curso_id
 * @return array<int, array{clase: WP_Post, estado: string, numero: int}>
 */
function afectivalab_ruta_del_curso( $hijo_id, $curso_id ) {
	$clases      = afectivalab_clases_del_curso( $curso_id );
	$completadas = afectivalab_clases_completadas( $hijo_id );

	$nodos       = array();
	$ya_hay_actual = false;
	$numero      = 1;

	foreach ( $clases as $clase ) {
		if ( in_array( $clase->ID, $completadas, true ) ) {
			$estado = 'hecha';
		} elseif ( ! $ya_hay_actual ) {
			$estado        = 'actual';
			$ya_hay_actual = true;
		} else {
			$estado = 'bloqueada';
		}

		$nodos[] = array(
			'clase'  => $clase,
			'estado' => $estado,
			'numero' => $numero,
		);

		$numero++;
	}

	return $nodos;
}

/**
 * ¿Puede este hijo abrir esta microclase?
 *
 * Solo las completadas y la que toca ahora. Evita que alguien salte al final
 * del curso escribiendo la URL a mano.
 *
 * @param int $hijo_id
 * @param int $clase_id
 */
function afectivalab_clase_desbloqueada( $hijo_id, $clase_id ) {
	$curso_id = (int) get_post_meta( $clase_id, '_afectivalab_curso', true );

	if ( ! $curso_id ) {
		return false;
	}

	foreach ( afectivalab_ruta_del_curso( $hijo_id, $curso_id ) as $nodo ) {
		if ( $nodo['clase']->ID === (int) $clase_id ) {
			return 'bloqueada' !== $nodo['estado'];
		}
	}

	return false;
}

/**
 * Procesa el botón de "ya la vimos" de una microclase.
 *
 * Al marcar, se redirige en vez de seguir renderizando: así el navegador no
 * reenvía el formulario si el padre recarga, y la pantalla que ve después ya
 * es la siguiente clase (o el curso, si era la última).
 *
 * @param int $clase_id
 * @param int $hijo_id
 */
function afectivalab_handle_clase_form( $clase_id, $hijo_id ) {
	if ( empty( $_POST['afectivalab_clase_hecha'] ) ) {
		return;
	}

	if ( ! isset( $_POST['afectivalab_clase_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_clase_nonce'] ) ), 'afectivalab_clase_' . $clase_id ) ) {
		return;
	}

	if ( ! afectivalab_clase_desbloqueada( $hijo_id, $clase_id ) ) {
		return;
	}

	afectivalab_marcar_clase( $hijo_id, $clase_id, true );

	$curso_id  = (int) get_post_meta( $clase_id, '_afectivalab_curso', true );
	$progreso  = afectivalab_progreso_curso( $hijo_id, $curso_id );
	$siguiente = $progreso['siguiente'];

	wp_safe_redirect( $siguiente ? get_permalink( $siguiente ) : get_permalink( $curso_id ) );
	exit;
}

/**
 * Habilidades adquiridas: el nombre que el instructor le puso a cada curso
 * que este hijo terminó. Son las insignias del padre.
 *
 * @param int $hijo_id
 * @return array<int, array{curso: WP_Post, habilidad: string}>
 */
function afectivalab_habilidades_del_hijo( $hijo_id ) {
	$habilidades = array();

	foreach ( afectivalab_cursos_de_la_etapa( afectivalab_hijo_etapa( $hijo_id ) ) as $curso ) {
		$progreso = afectivalab_progreso_curso( $hijo_id, $curso->ID );

		if ( ! $progreso['completo'] ) {
			continue;
		}

		$habilidad = get_post_meta( $curso->ID, '_afectivalab_habilidad', true );

		$habilidades[] = array(
			'curso'     => $curso,
			'habilidad' => $habilidad ? $habilidad : $curso->post_title,
		);
	}

	return $habilidades;
}
