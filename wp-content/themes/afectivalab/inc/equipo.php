<?php
/**
 * Panel del equipo (administrador e instructor).
 *
 * Deliberadamente **no** duplica el escritorio de WordPress: cargar y editar
 * cursos ya se hace ahí, con los permisos que define inc/roles.php. Esta
 * pantalla responde lo que el escritorio no responde bien — cómo va el
 * contenido y cómo lo están usando las familias — y enlaza al resto.
 *
 * Las cifras sobre familias son **agregadas a propósito**: cuántos perfiles
 * hay por etapa y cuántos terminaron cada curso, nunca el nombre de un niño
 * ni el avance de una familia concreta. Son datos de menores, y para decidir
 * qué contenido producir alcanza con el agregado. Si el cliente pide ver
 * familias una por una, es una decisión suya que conviene dejar por escrito
 * antes de construirla.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ¿Esta persona es del equipo que produce el contenido?
 *
 * @param int $user_id Por defecto, el usuario actual.
 */
function afectivalab_es_del_equipo( $user_id = 0 ) {
	$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();

	if ( ! $user || ! $user->exists() ) {
		return false;
	}

	return user_can( $user, 'edit_afectivalab_cursos' ) || user_can( $user, 'manage_afectivalab_contenido' );
}

/**
 * Cifras para el panel del equipo.
 *
 * Recorre los perfiles de hijo una sola vez y se queda con lo completado de
 * cada uno en memoria; después cruza eso contra los cursos. Con el volumen
 * actual sobra, pero **si algún día hay miles de familias esto hay que
 * repensarlo** (una tabla propia de progreso, o cifras precalculadas).
 *
 * @return array
 */
function afectivalab_resumen_equipo() {
	$cursos_count = wp_count_posts( AFECTIVALAB_CPT_CURSO );
	$clases_count = wp_count_posts( AFECTIVALAB_CPT_CLASE );

	$familias = count(
		get_users(
			array(
				'role'   => 'afectivalab_padre',
				'fields' => 'ID',
			)
		)
	);

	$hijos = get_posts(
		array(
			'post_type'      => AFECTIVALAB_CPT_HIJO,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		)
	);

	$por_etapa   = array();
	$completadas = array();

	foreach ( afectivalab_etapas() as $slug => $etapa ) {
		$por_etapa[ $slug ] = array(
			'nombre' => $etapa['nombre'],
			'total'  => 0,
		);
	}

	$sin_etapa = 0;

	foreach ( $hijos as $hijo ) {
		$etapa = afectivalab_hijo_etapa( $hijo->ID );

		if ( $etapa && isset( $por_etapa[ $etapa->slug ] ) ) {
			$por_etapa[ $etapa->slug ]['total']++;
		} else {
			$sin_etapa++;
		}

		$completadas[ $hijo->ID ] = array_flip( afectivalab_clases_completadas( $hijo->ID ) );
	}

	$cursos = get_posts(
		array(
			'post_type'      => AFECTIVALAB_CPT_CURSO,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	$avance = array();

	foreach ( $cursos as $curso ) {
		$clases = afectivalab_clases_del_curso( $curso->ID );
		$total  = count( $clases );

		$empezaron = 0;
		$acabaron  = 0;

		foreach ( $completadas as $hechas ) {
			$cuantas = 0;

			foreach ( $clases as $clase ) {
				if ( isset( $hechas[ $clase->ID ] ) ) {
					$cuantas++;
				}
			}

			if ( $cuantas > 0 ) {
				$empezaron++;
			}

			if ( $total > 0 && $cuantas === $total ) {
				$acabaron++;
			}
		}

		$avance[] = array(
			'curso'     => $curso,
			'clases'    => $total,
			'empezaron' => $empezaron,
			'acabaron'  => $acabaron,
		);
	}

	return array(
		'cursos_publicados' => (int) $cursos_count->publish,
		'cursos_borrador'   => (int) $cursos_count->draft,
		'clases_publicadas' => (int) $clases_count->publish,
		'clases_borrador'   => (int) $clases_count->draft,
		'familias'          => $familias,
		'hijos'             => count( $hijos ),
		'por_etapa'         => $por_etapa,
		'sin_etapa'         => $sin_etapa,
		'avance'            => $avance,
	);
}

/**
 * Cursos publicados que todavía no tienen ninguna microclase: son los que el
 * padre ve como "en preparación", así que conviene que el equipo los tenga a
 * la vista.
 *
 * @return WP_Post[]
 */
function afectivalab_cursos_sin_clases() {
	$vacios = array();

	$cursos = get_posts(
		array(
			'post_type'      => AFECTIVALAB_CPT_CURSO,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		)
	);

	foreach ( $cursos as $curso ) {
		if ( ! afectivalab_clases_del_curso( $curso->ID ) ) {
			$vacios[] = $curso;
		}
	}

	return $vacios;
}
