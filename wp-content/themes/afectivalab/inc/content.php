<?php
/**
 * Motor de contenido: cursos, microclases y sus dos taxonomías (etapa de edad
 * y eje temático).
 *
 * Se implementa a medida y no sobre un plugin LMS (LearnDash, Tutor, etc.)
 * porque en esta plataforma el "alumno" no es un usuario de WordPress sino un
 * perfil de hijo dentro de la cuenta del padre — ningún LMS modela eso, y
 * forzarlo complicaría también las rutas por edad, las misiones con evidencia
 * y los casos ramificados. Decisión registrada en
 * docs/concepto-plataforma.md.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AFECTIVALAB_CPT_CURSO = 'afectivalab_curso';
const AFECTIVALAB_CPT_CLASE = 'afectivalab_clase';
const AFECTIVALAB_TAX_ETAPA = 'afectivalab_etapa';
const AFECTIVALAB_TAX_EJE   = 'afectivalab_eje';

/**
 * Las 5 etapas de edad y los 8 ejes temáticos son un set cerrado definido por
 * el producto, no algo que el equipo de contenido invente sobre la marcha: la
 * matriz de 40 rutas (5 etapas × 8 ejes) depende de que sean exactamente
 * estos. Por eso viven aquí como código y se siembran una sola vez, en vez de
 * crearse a mano en el escritorio.
 */
function afectivalab_etapas() {
	return array(
		'primera-infancia'     => array(
			'nombre'   => 'Primera infancia',
			'edad_min' => 3,
			'edad_max' => 5,
			'enfoque'  => 'Vínculo, emociones básicas, límites, autonomía, protección corporal e inicio de educación sexual.',
		),
		'ninez-inicial'        => array(
			'nombre'   => 'Niñez inicial',
			'edad_min' => 6,
			'edad_max' => 8,
			'enfoque'  => 'Autoestima, amistades, bullying inicial, normas, comunicación y uso de pantallas.',
		),
		'ninez-media'          => array(
			'nombre'   => 'Niñez media',
			'edad_min' => 9,
			'edad_max' => 11,
			'enfoque'  => 'Pubertad temprana, presión social, bullying y cyberbullying, autoestima, internet y cambios emocionales.',
		),
		'adolescencia-inicial' => array(
			'nombre'   => 'Adolescencia inicial',
			'edad_min' => 12,
			'edad_max' => 14,
			'enfoque'  => 'Identidad, redes sociales, sexualidad, consentimiento, presión de grupo, comunicación y límites.',
		),
		'adolescencia-media'   => array(
			'nombre'   => 'Adolescencia media y tardía',
			'edad_min' => 15,
			'edad_max' => 17,
			'enfoque'  => 'Relaciones de pareja, sexualidad responsable, autonomía, proyecto de vida, salud emocional y toma de decisiones.',
		),
	);
}

/**
 * Los 8 ejes, en el orden de desarrollo confirmado por el cliente. El nombre
 * oficial es el del "mundo" temático (no el del tema prioritario), porque es
 * el que tiene ícono propio en el paquete de marca — la equivalencia entre
 * ambas listas está en docs/concepto-plataforma.md.
 */
function afectivalab_ejes() {
	return array(
		'crecer-seguro'          => array(
			'nombre'  => 'Crecer seguro',
			'resumen' => 'Autoestima, confianza, autonomía y resiliencia.',
		),
		'proteger'               => array(
			'nombre'  => 'Proteger',
			'resumen' => 'Bullying, cyberbullying, violencia, abuso y seguridad personal.',
		),
		'sexualidad-afectividad' => array(
			'nombre'  => 'Sexualidad y afectividad',
			'resumen' => 'Cuerpo, consentimiento, pubertad y relaciones.',
		),
		'conectar'               => array(
			'nombre'  => 'Conectar',
			'resumen' => 'Comunicación, vínculo, escucha y afectividad.',
		),
		'emociones'              => array(
			'nombre'  => 'Emociones',
			'resumen' => 'Rabia, frustración, ansiedad, miedos y autorregulación.',
		),
		'mundo-digital'          => array(
			'nombre'  => 'Mundo digital',
			'resumen' => 'Pantallas, videojuegos, redes sociales, cyberbullying y grooming.',
		),
		'convivir'               => array(
			'nombre'  => 'Convivir',
			'resumen' => 'Normas, límites, disciplina y responsabilidades.',
		),
		'vida-escolar'           => array(
			'nombre'  => 'Vida escolar',
			'resumen' => 'Motivación, aprendizaje, amistades y presión académica.',
		),
	);
}

/**
 * Capacidades primitivas que genera WordPress para un tipo de contenido con
 * capability_type propio. Las usa inc/roles.php para armar el rol de
 * instructor y para dárselas al administrador (que no las tiene por defecto:
 * al usar capacidades propias, un admin no vería estos menús sin esto).
 *
 * @param string $plural Nombre en plural del capability_type (ej. "afectivalab_cursos").
 */
function afectivalab_cpt_caps( $plural ) {
	return array(
		"edit_{$plural}",
		"edit_others_{$plural}",
		"edit_published_{$plural}",
		"edit_private_{$plural}",
		"publish_{$plural}",
		"read_private_{$plural}",
		"delete_{$plural}",
		"delete_published_{$plural}",
		"delete_others_{$plural}",
		"delete_private_{$plural}",
	);
}

function afectivalab_register_content() {
	register_post_type(
		AFECTIVALAB_CPT_CURSO,
		array(
			'labels'          => array(
				'name'               => __( 'Cursos', 'afectivalab' ),
				'singular_name'      => __( 'Curso', 'afectivalab' ),
				'add_new'            => __( 'Añadir curso', 'afectivalab' ),
				'add_new_item'       => __( 'Añadir nuevo curso', 'afectivalab' ),
				'edit_item'          => __( 'Editar curso', 'afectivalab' ),
				'new_item'           => __( 'Nuevo curso', 'afectivalab' ),
				'view_item'          => __( 'Ver curso', 'afectivalab' ),
				'search_items'       => __( 'Buscar cursos', 'afectivalab' ),
				'not_found'          => __( 'No hay cursos todavía', 'afectivalab' ),
				'not_found_in_trash' => __( 'No hay cursos en la papelera', 'afectivalab' ),
				'menu_name'          => __( 'Cursos', 'afectivalab' ),
			),
			'public'          => true,
			'show_in_rest'    => true,
			'menu_icon'       => 'dashicons-welcome-learn-more',
			'menu_position'   => 20,
			'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author' ),
			'taxonomies'      => array( AFECTIVALAB_TAX_ETAPA, AFECTIVALAB_TAX_EJE ),
			'has_archive'     => 'cursos',
			'rewrite'         => array( 'slug' => 'cursos', 'with_front' => false ),
			'capability_type' => array( 'afectivalab_curso', 'afectivalab_cursos' ),
			'map_meta_cap'    => true,
		)
	);

	register_post_type(
		AFECTIVALAB_CPT_CLASE,
		array(
			'labels'          => array(
				'name'               => __( 'Microclases', 'afectivalab' ),
				'singular_name'      => __( 'Microclase', 'afectivalab' ),
				'add_new'            => __( 'Añadir microclase', 'afectivalab' ),
				'add_new_item'       => __( 'Añadir nueva microclase', 'afectivalab' ),
				'edit_item'          => __( 'Editar microclase', 'afectivalab' ),
				'new_item'           => __( 'Nueva microclase', 'afectivalab' ),
				'view_item'          => __( 'Ver microclase', 'afectivalab' ),
				'search_items'       => __( 'Buscar microclases', 'afectivalab' ),
				'not_found'          => __( 'No hay microclases todavía', 'afectivalab' ),
				'not_found_in_trash' => __( 'No hay microclases en la papelera', 'afectivalab' ),
				'menu_name'          => __( 'Microclases', 'afectivalab' ),
			),
			'public'          => true,
			'show_in_rest'    => true,
			'menu_icon'       => 'dashicons-video-alt3',
			'menu_position'   => 21,
			// page-attributes da el campo "Orden", que es como el instructor
			// decide la secuencia de clases dentro de un curso.
			'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'page-attributes' ),
			'has_archive'     => false,
			'rewrite'         => array( 'slug' => 'clases', 'with_front' => false ),
			'capability_type' => array( 'afectivalab_clase', 'afectivalab_clases' ),
			'map_meta_cap'    => true,
		)
	);

	// hierarchical => true en ambas taxonomías no es por jerarquía real (no
	// hay términos anidados), sino porque es lo que hace que el editor las
	// muestre como casillas de un set fijo en vez de un campo libre de
	// etiquetas: el instructor elige entre las 5 etapas y los 8 ejes, no
	// inventa términos nuevos.
	register_taxonomy(
		AFECTIVALAB_TAX_ETAPA,
		array( AFECTIVALAB_CPT_CURSO ),
		array(
			'labels'            => array(
				'name'          => __( 'Etapas', 'afectivalab' ),
				'singular_name' => __( 'Etapa', 'afectivalab' ),
				'menu_name'     => __( 'Etapas de edad', 'afectivalab' ),
			),
			'public'            => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'etapa', 'with_front' => false ),
			'capabilities'      => array(
				'manage_terms' => 'manage_afectivalab_contenido',
				'edit_terms'   => 'manage_afectivalab_contenido',
				'delete_terms' => 'manage_afectivalab_contenido',
				'assign_terms' => 'edit_afectivalab_cursos',
			),
		)
	);

	register_taxonomy(
		AFECTIVALAB_TAX_EJE,
		array( AFECTIVALAB_CPT_CURSO ),
		array(
			'labels'            => array(
				'name'          => __( 'Ejes temáticos', 'afectivalab' ),
				'singular_name' => __( 'Eje temático', 'afectivalab' ),
				'menu_name'     => __( 'Ejes temáticos', 'afectivalab' ),
			),
			'public'            => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'eje', 'with_front' => false ),
			'capabilities'      => array(
				'manage_terms' => 'manage_afectivalab_contenido',
				'edit_terms'   => 'manage_afectivalab_contenido',
				'delete_terms' => 'manage_afectivalab_contenido',
				'assign_terms' => 'edit_afectivalab_cursos',
			),
		)
	);
}
add_action( 'init', 'afectivalab_register_content', 5 );

/**
 * Siembra las etapas y los ejes una sola vez. Se guarda el rango de edad como
 * term meta (no solo en el nombre) porque la ruta personalizada se arma
 * buscando qué etapa contiene la edad del hijo — eso necesita números
 * consultables, no un texto tipo "6–8 años".
 */
function afectivalab_seed_terms() {
	if ( get_option( 'afectivalab_terms_seeded' ) ) {
		return;
	}

	$orden = 1;
	foreach ( afectivalab_etapas() as $slug => $etapa ) {
		$term = term_exists( $slug, AFECTIVALAB_TAX_ETAPA );

		if ( ! $term ) {
			$term = wp_insert_term(
				$etapa['nombre'],
				AFECTIVALAB_TAX_ETAPA,
				array(
					'slug'        => $slug,
					'description' => $etapa['enfoque'],
				)
			);
		}

		if ( ! is_wp_error( $term ) ) {
			update_term_meta( $term['term_id'], 'afectivalab_edad_min', $etapa['edad_min'] );
			update_term_meta( $term['term_id'], 'afectivalab_edad_max', $etapa['edad_max'] );
			update_term_meta( $term['term_id'], 'afectivalab_orden', $orden );
		}

		$orden++;
	}

	$orden = 1;
	foreach ( afectivalab_ejes() as $slug => $eje ) {
		$term = term_exists( $slug, AFECTIVALAB_TAX_EJE );

		if ( ! $term ) {
			$term = wp_insert_term(
				$eje['nombre'],
				AFECTIVALAB_TAX_EJE,
				array(
					'slug'        => $slug,
					'description' => $eje['resumen'],
				)
			);
		}

		if ( ! is_wp_error( $term ) ) {
			// El ícono se guarda por nombre de archivo del theme
			// (assets/icons/eje-*.svg), no como URL, para que siga
			// funcionando si cambia el dominio del sitio.
			update_term_meta( $term['term_id'], 'afectivalab_icono', 'eje-' . $slug );
			update_term_meta( $term['term_id'], 'afectivalab_orden', $orden );
		}

		$orden++;
	}

	update_option( 'afectivalab_terms_seeded', 1 );
}
add_action( 'init', 'afectivalab_seed_terms', 15 );

/**
 * Devuelve la etapa que corresponde a una edad, o null si está fuera de los
 * 3–17 años que cubre la plataforma.
 *
 * @param int $edad Edad del hijo en años.
 * @return WP_Term|null
 */
function afectivalab_etapa_para_edad( $edad ) {
	$edad = absint( $edad );

	$terms = get_terms(
		array(
			'taxonomy'   => AFECTIVALAB_TAX_ETAPA,
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) ) {
		return null;
	}

	foreach ( $terms as $term ) {
		$min = (int) get_term_meta( $term->term_id, 'afectivalab_edad_min', true );
		$max = (int) get_term_meta( $term->term_id, 'afectivalab_edad_max', true );

		if ( $edad >= $min && $edad <= $max ) {
			return $term;
		}
	}

	return null;
}

/**
 * Microclases de un curso, en el orden que definió el instructor.
 *
 * @param int          $curso_id
 * @param string|array $post_status Por defecto solo publicadas, que es lo que
 *                                  ve el padre. El escritorio pasa también los
 *                                  borradores: un curso a medio armar mostraría
 *                                  "0 microclases" y parecería vacío.
 * @return WP_Post[]
 */
function afectivalab_clases_del_curso( $curso_id, $post_status = 'publish' ) {
	return get_posts(
		array(
			'post_type'      => AFECTIVALAB_CPT_CLASE,
			'post_status'    => $post_status,
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'meta_key'       => '_afectivalab_curso',
			'meta_value'     => absint( $curso_id ),
		)
	);
}
