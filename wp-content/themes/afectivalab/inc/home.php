<?php
/**
 * Datos reales para la home pública (template-parts/home/showcase.php).
 *
 * Selección automática, sin un campo de "destacado en portada" que el
 * equipo tenga que mantener — decisión del usuario. Si todavía no hay
 * contenido real publicado que califique, la plantilla cae de vuelta al
 * ejemplo ilustrativo original en vez de mostrar una sección vacía.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * El curso para la tarjeta "Ruta recomendada": el primero publicado de la
 * etapa de niñez inicial (6-8 años), en el mismo orden que ya usa el resto
 * de la plataforma para esa etapa.
 *
 * @return WP_Post|null
 */
function afectivalab_showcase_curso() {
	$etapa = get_term_by( 'slug', 'ninez-inicial', AFECTIVALAB_TAX_ETAPA );

	if ( ! $etapa ) {
		return null;
	}

	$cursos = afectivalab_cursos_de_la_etapa( $etapa );

	return $cursos ? $cursos[0] : null;
}

/**
 * La primera microclase publicada (de cualquier curso) que tenga un caso
 * interactivo real configurado, para el demo de "casos prácticos" de la
 * home. No se limita al curso de afectivalab_showcase_curso() a propósito:
 * así el demo no queda vacío solo porque ese curso en particular todavía no
 * tenga un caso cargado.
 *
 * @return array{clase: WP_Post, caso: array}|null
 */
function afectivalab_showcase_caso() {
	$clases = get_posts(
		array(
			'post_type'      => AFECTIVALAB_CPT_CLASE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
		)
	);

	foreach ( $clases as $clase ) {
		$caso = afectivalab_clase_caso( $clase->ID );

		if ( $caso ) {
			return array(
				'clase' => $clase,
				'caso'  => $caso,
			);
		}
	}

	return null;
}
