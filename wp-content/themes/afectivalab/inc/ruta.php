<?php
/**
 * Armado de la ruta personalizada de cada hijo.
 *
 * La regla, según el concepto: la ruta la define la **edad del hijo** (que da
 * su etapa) y la ordenan las **preocupaciones que marcó el padre**. Lo que le
 * preocupa va primero; el resto de los cursos de esa etapa viene después, en
 * el orden de prioridad que confirmó el cliente (autoestima, bullying,
 * sexualidad, comunicación, emociones, pantallas, límites, amistades).
 *
 * Nada de esto se guarda: la ruta se calcula cada vez a partir de la edad de
 * hoy. Así es como un hijo cambia de etapa solo al cumplir años, sin que
 * nadie tenga que regenerar nada.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cursos publicados de una etapa.
 *
 * @param WP_Term|null $etapa
 * @return WP_Post[]
 */
function afectivalab_cursos_de_la_etapa( $etapa ) {
	if ( ! $etapa instanceof WP_Term ) {
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
					'taxonomy' => AFECTIVALAB_TAX_ETAPA,
					'field'    => 'term_id',
					'terms'    => $etapa->term_id,
				),
			),
		)
	);
}

/**
 * Eje temático principal de un curso (el primero que tenga asignado).
 *
 * @param int $curso_id
 * @return WP_Term|null
 */
function afectivalab_eje_del_curso( $curso_id ) {
	$terms = get_the_terms( $curso_id, AFECTIVALAB_TAX_EJE );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}

	return reset( $terms );
}

/**
 * La ruta de un hijo: los cursos de su etapa, ordenados.
 *
 * @param int $hijo_id
 * @return array<int, array{curso: WP_Post, eje: WP_Term|null, prioritario: bool, progreso: array}>
 */
function afectivalab_ruta_del_hijo( $hijo_id ) {
	$etapa = afectivalab_hijo_etapa( $hijo_id );

	if ( ! $etapa ) {
		return array();
	}

	$preocupaciones = afectivalab_hijo_preocupaciones( $hijo_id );
	$orden_ejes     = array_keys( afectivalab_ejes() );

	$ruta = array();

	foreach ( afectivalab_cursos_de_la_etapa( $etapa ) as $curso ) {
		$eje  = afectivalab_eje_del_curso( $curso->ID );
		$slug = $eje ? $eje->slug : '';

		// Un curso sin eje asignado va al final, pero no se esconde: el
		// equipo de contenido puede haberlo dejado así sin querer y es mejor
		// que se vea a que desaparezca sin explicación.
		$posicion = array_search( $slug, $orden_ejes, true );

		$ruta[] = array(
			'curso'       => $curso,
			'eje'         => $eje,
			'prioritario' => $slug && in_array( $slug, $preocupaciones, true ),
			'posicion'    => false === $posicion ? count( $orden_ejes ) : $posicion,
			'progreso'    => afectivalab_progreso_curso( $hijo_id, $curso->ID ),
		);
	}

	usort(
		$ruta,
		function ( $a, $b ) {
			// Lo que le preocupa al padre va primero; dentro de cada grupo
			// manda el orden de prioridad de los ejes.
			if ( $a['prioritario'] !== $b['prioritario'] ) {
				return $a['prioritario'] ? -1 : 1;
			}

			return $a['posicion'] <=> $b['posicion'];
		}
	);

	return $ruta;
}

/**
 * El curso que le toca al hijo ahora: el primero de su ruta sin terminar.
 *
 * @param int $hijo_id
 * @return array|null Un elemento de afectivalab_ruta_del_hijo(), o null si
 *                    ya terminó todo lo que hay para su etapa.
 */
function afectivalab_curso_actual( $hijo_id ) {
	foreach ( afectivalab_ruta_del_hijo( $hijo_id ) as $paso ) {
		if ( ! $paso['progreso']['completo'] ) {
			return $paso;
		}
	}

	return null;
}

/**
 * El hijo sobre el que está trabajando el padre en este momento.
 *
 * Con varios hijos hace falta saber a cuál se refiere la pantalla. Se toma
 * de la URL (?hijo=ID) y, si no viene, del último que eligió; si tampoco hay,
 * del primero que agregó. Siempre comprobando que el perfil sea suyo.
 *
 * @return WP_Post|null
 */
function afectivalab_hijo_activo() {
	$hijos = afectivalab_get_hijos();

	if ( ! $hijos ) {
		return null;
	}

	if ( isset( $_GET['hijo'] ) ) {
		$elegido = afectivalab_get_hijo_propio( absint( $_GET['hijo'] ) );

		if ( $elegido ) {
			update_user_meta( get_current_user_id(), 'afectivalab_hijo_activo', $elegido->ID );
			return $elegido;
		}
	}

	$recordado = (int) get_user_meta( get_current_user_id(), 'afectivalab_hijo_activo', true );

	if ( $recordado ) {
		$hijo = afectivalab_get_hijo_propio( $recordado );

		if ( $hijo ) {
			return $hijo;
		}
	}

	return $hijos[0];
}

/**
 * Saludo según la hora del sitio. Pequeño detalle del concepto: el padre
 * entra y la plataforma le habla, no le muestra un catálogo.
 */
function afectivalab_saludo() {
	$hora = (int) current_time( 'G' );

	if ( $hora < 12 ) {
		return __( 'Buenos días', 'afectivalab' );
	}

	if ( $hora < 19 ) {
		return __( 'Buenas tardes', 'afectivalab' );
	}

	return __( 'Buenas noches', 'afectivalab' );
}
