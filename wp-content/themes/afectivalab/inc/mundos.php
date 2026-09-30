<?php
/**
 * Exploración libre por mundo temático — navegación SECUNDARIA a la ruta
 * personalizada, confirmada por el cliente: "la ruta que arma la plataforma
 * según edad + intereses es lo primero que ve el padre y el motor principal;
 * además puede explorar el resto del contenido libremente por mundo
 * temático" (docs/concepto-plataforma.md).
 *
 * A propósito NO filtra por la edad del hijo activo — "libremente" es lo que
 * la distingue de /panel, que sí arma la ruta según edad+intereses. Por eso
 * tampoco depende de que haya un hijo activo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cursos publicados de un mundo (eje temático), sin filtrar por etapa.
 *
 * @param string $eje_slug
 * @return WP_Post[]
 */
function afectivalab_cursos_del_eje( $eje_slug ) {
	if ( ! $eje_slug ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'      => AFECTIVALAB_CPT_CURSO,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'tax_query'      => array(
				array(
					'taxonomy' => AFECTIVALAB_TAX_EJE,
					'field'    => 'slug',
					'terms'    => $eje_slug,
				),
			),
		)
	);
}

/**
 * Los cursos de un mundo, agrupados por etapa de edad y en el orden de las 5
 * etapas (no el orden en que se crearon) — así la exploración libre también
 * ayuda a entender qué tan progresivo es el contenido dentro de un mismo
 * tema, aunque no esté atada a la edad de ningún hijo en particular.
 *
 * Un curso publicado sin etapa asignada no se esconde (podría ser un olvido
 * del equipo de contenido) — cae en un grupo aparte al final, igual que ya
 * hace afectivalab_ruta_del_hijo() con los ejes sin asignar.
 *
 * @param string $eje_slug
 * @return array<string, array{etapa: WP_Term|null, cursos: WP_Post[]}>
 */
function afectivalab_cursos_del_eje_por_etapa( $eje_slug ) {
	$orden_etapas = array_keys( afectivalab_etapas() );
	$grupos       = array();

	foreach ( afectivalab_cursos_del_eje( $eje_slug ) as $curso ) {
		$terms = get_the_terms( $curso->ID, AFECTIVALAB_TAX_ETAPA );
		$etapa = ( $terms && ! is_wp_error( $terms ) ) ? reset( $terms ) : null;
		$slug  = $etapa ? $etapa->slug : '';

		if ( ! isset( $grupos[ $slug ] ) ) {
			$grupos[ $slug ] = array( 'etapa' => $etapa, 'cursos' => array() );
		}

		$grupos[ $slug ]['cursos'][] = $curso;
	}

	uksort(
		$grupos,
		function ( $a, $b ) use ( $orden_etapas ) {
			$pa = array_search( $a, $orden_etapas, true );
			$pb = array_search( $b, $orden_etapas, true );

			return ( false === $pa ? count( $orden_etapas ) : $pa ) <=> ( false === $pb ? count( $orden_etapas ) : $pb );
		}
	);

	return $grupos;
}
