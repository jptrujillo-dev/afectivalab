<?php
/**
 * Perfiles de hijo dentro de la cuenta del padre.
 *
 * El hijo NO es un usuario de WordPress: es un perfil dentro de la cuenta del
 * padre, como los perfiles de Netflix. Se guarda como contenido propio con
 * post_author = el padre, y ese autor es la única fuente de verdad sobre quién
 * puede verlo o editarlo — todas las funciones de abajo lo comprueban.
 *
 * Se pide **mes y año de nacimiento**, no la edad escrita a mano, por dos
 * razones: una edad guardada como número queda desactualizada sola, y el
 * concepto de "GPS de crianza" necesita saber cuándo el hijo cambia de etapa
 * para avisar. No se pide el día: con mes y año alcanza para las dos cosas, y
 * es un dato menos de un menor de edad.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AFECTIVALAB_CPT_HIJO = 'afectivalab_hijo';

function afectivalab_register_hijos() {
	register_post_type(
		AFECTIVALAB_CPT_HIJO,
		array(
			'labels'              => array(
				'name'          => __( 'Hijos', 'afectivalab' ),
				'singular_name' => __( 'Hijo', 'afectivalab' ),
			),
			// Sin interfaz en el escritorio y fuera de las búsquedas: son
			// datos de menores, no contenido del sitio. Se administran solo
			// desde /mis-hijos, con la cuenta del padre.
			'public'              => false,
			'show_ui'             => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'supports'            => array( 'title', 'author' ),
		)
	);
}
add_action( 'init', 'afectivalab_register_hijos', 5 );

function afectivalab_meses() {
	return array(
		1  => __( 'Enero', 'afectivalab' ),
		2  => __( 'Febrero', 'afectivalab' ),
		3  => __( 'Marzo', 'afectivalab' ),
		4  => __( 'Abril', 'afectivalab' ),
		5  => __( 'Mayo', 'afectivalab' ),
		6  => __( 'Junio', 'afectivalab' ),
		7  => __( 'Julio', 'afectivalab' ),
		8  => __( 'Agosto', 'afectivalab' ),
		9  => __( 'Septiembre', 'afectivalab' ),
		10 => __( 'Octubre', 'afectivalab' ),
		11 => __( 'Noviembre', 'afectivalab' ),
		12 => __( 'Diciembre', 'afectivalab' ),
	);
}

/**
 * Perfiles de hijo de un padre, del más antiguo al más nuevo.
 *
 * @param int $user_id Padre. Por defecto, el usuario actual.
 * @return WP_Post[]
 */
function afectivalab_get_hijos( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();

	if ( ! $user_id ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'      => AFECTIVALAB_CPT_HIJO,
			'post_status'    => 'publish',
			'author'         => $user_id,
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'ASC',
		)
	);
}

/**
 * Devuelve el perfil solo si de verdad pertenece a ese padre.
 *
 * Es la comprobación que evita que alguien edite o borre el perfil de otra
 * familia cambiando el número en el formulario, así que toda operación sobre
 * un hijo pasa por aquí.
 *
 * @param int $hijo_id
 * @param int $user_id Por defecto, el usuario actual.
 * @return WP_Post|null
 */
function afectivalab_get_hijo_propio( $hijo_id, $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();
	$hijo    = get_post( absint( $hijo_id ) );

	if ( ! $hijo || AFECTIVALAB_CPT_HIJO !== $hijo->post_type ) {
		return null;
	}

	if ( ! $user_id || (int) $hijo->post_author !== $user_id ) {
		return null;
	}

	return $hijo;
}

/**
 * Edad del hijo en años cumplidos.
 *
 * Como no se guarda el día de nacimiento, se cuenta el cumpleaños desde el
 * primer día del mes: durante todo su mes de cumpleaños ya figura con la
 * edad nueva.
 *
 * @param int $hijo_id
 * @return int|null Null si el perfil no tiene fecha guardada.
 */
function afectivalab_hijo_edad( $hijo_id ) {
	$mes  = (int) get_post_meta( $hijo_id, '_afectivalab_nacimiento_mes', true );
	$anio = (int) get_post_meta( $hijo_id, '_afectivalab_nacimiento_anio', true );

	if ( ! $mes || ! $anio ) {
		return null;
	}

	$anio_actual = (int) current_time( 'Y' );
	$mes_actual  = (int) current_time( 'n' );

	$edad = $anio_actual - $anio;

	if ( $mes_actual < $mes ) {
		$edad--;
	}

	return max( 0, $edad );
}

/**
 * Etapa que le corresponde hoy al hijo, o null si su edad aún no entra en el
 * rango de 3 a 17 años que cubre la plataforma.
 *
 * @param int $hijo_id
 * @return WP_Term|null
 */
function afectivalab_hijo_etapa( $hijo_id ) {
	$edad = afectivalab_hijo_edad( $hijo_id );

	return null === $edad ? null : afectivalab_etapa_para_edad( $edad );
}

/**
 * Preocupaciones marcadas por el padre, como slugs de eje temático.
 *
 * Se guardan como post meta y no como términos de la taxonomía de ejes a
 * propósito: si los hijos llevaran términos, el conteo de cada eje en el
 * escritorio sumaría hijos y cursos mezclados, y el equipo de contenido
 * dejaría de poder confiar en esos números.
 *
 * @param int $hijo_id
 * @return string[]
 */
function afectivalab_hijo_preocupaciones( $hijo_id ) {
	$slugs = get_post_meta( $hijo_id, '_afectivalab_preocupaciones', true );

	return is_array( $slugs ) ? $slugs : array();
}

/**
 * Año de nacimiento más antiguo que se acepta: el de alguien que cumple 17
 * este año. Más arriba de los 17 la plataforma ya no tiene contenido.
 */
function afectivalab_hijo_anio_minimo() {
	return (int) current_time( 'Y' ) - 17;
}

/**
 * Valida y guarda el formulario de /mis-hijos (alta, edición y baja).
 *
 * @return array{errors: string[], notice: string, editando: int, valores: array}
 */
function afectivalab_handle_hijo_forms() {
	$result = array(
		'errors'   => array(),
		'notice'   => '',
		'editando' => 0,
		'valores'  => array(
			'nombre'         => '',
			'mes'            => 0,
			'anio'           => 0,
			'preocupaciones' => array(),
		),
	);

	$user_id = get_current_user_id();

	if ( ! $user_id ) {
		return $result;
	}

	// Baja de un perfil.
	if ( ! empty( $_POST['afectivalab_hijo_eliminar'] ) ) {
		if ( ! isset( $_POST['afectivalab_hijo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_hijo_nonce'] ) ), 'afectivalab_hijo' ) ) {
			$result['errors'][] = __( 'Tu sesión expiró, por favor intenta de nuevo.', 'afectivalab' );
			return $result;
		}

		$hijo = afectivalab_get_hijo_propio( absint( $_POST['afectivalab_hijo_eliminar'] ) );

		if ( ! $hijo ) {
			$result['errors'][] = __( 'No encontramos ese perfil.', 'afectivalab' );
			return $result;
		}

		// A la papelera y no borrado definitivo: si el padre se equivoca de
		// perfil, el progreso no se pierde sin remedio.
		wp_trash_post( $hijo->ID );

		$result['notice'] = sprintf(
			/* translators: %s: nombre del hijo. */
			__( 'Quitamos el perfil de %s.', 'afectivalab' ),
			$hijo->post_title
		);

		return $result;
	}

	// Alta o edición. Si no se envió el formulario, solo se prepara el estado
	// de edición para que la plantilla muestre los datos actuales.
	if ( empty( $_POST['afectivalab_hijo_guardar'] ) ) {
		$editar = isset( $_GET['editar'] ) ? absint( $_GET['editar'] ) : 0;
		$hijo   = $editar ? afectivalab_get_hijo_propio( $editar ) : null;

		if ( $hijo ) {
			$result['editando'] = $hijo->ID;
			$result['valores']  = array(
				'nombre'         => $hijo->post_title,
				'mes'            => (int) get_post_meta( $hijo->ID, '_afectivalab_nacimiento_mes', true ),
				'anio'           => (int) get_post_meta( $hijo->ID, '_afectivalab_nacimiento_anio', true ),
				'preocupaciones' => afectivalab_hijo_preocupaciones( $hijo->ID ),
			);
		}

		return $result;
	}

	if ( ! isset( $_POST['afectivalab_hijo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_hijo_nonce'] ) ), 'afectivalab_hijo' ) ) {
		$result['errors'][] = __( 'Tu sesión expiró, por favor intenta de nuevo.', 'afectivalab' );
		return $result;
	}

	$nombre = sanitize_text_field( wp_unslash( $_POST['afectivalab_hijo_nombre'] ?? '' ) );
	$mes    = absint( $_POST['afectivalab_hijo_mes'] ?? 0 );
	$anio   = absint( $_POST['afectivalab_hijo_anio'] ?? 0 );

	$preocupaciones = array();
	if ( isset( $_POST['afectivalab_hijo_preocupaciones'] ) && is_array( $_POST['afectivalab_hijo_preocupaciones'] ) ) {
		$ejes_validos   = array_keys( afectivalab_ejes() );
		$enviadas       = array_map( 'sanitize_key', wp_unslash( $_POST['afectivalab_hijo_preocupaciones'] ) );
		$preocupaciones = array_values( array_intersect( $enviadas, $ejes_validos ) );
	}

	// Se devuelven al formulario para que no haya que reescribir todo si algo
	// falló la validación.
	$result['valores'] = array(
		'nombre'         => $nombre,
		'mes'            => $mes,
		'anio'           => $anio,
		'preocupaciones' => $preocupaciones,
	);

	$hijo_id = absint( $_POST['afectivalab_hijo_id'] ?? 0 );
	$hijo    = $hijo_id ? afectivalab_get_hijo_propio( $hijo_id ) : null;

	if ( $hijo_id && ! $hijo ) {
		$result['errors'][] = __( 'No encontramos ese perfil.', 'afectivalab' );
		return $result;
	}

	$result['editando'] = $hijo ? $hijo->ID : 0;

	if ( '' === $nombre ) {
		$result['errors'][] = __( 'Escribe el nombre de tu hijo o hija.', 'afectivalab' );
	}

	if ( $mes < 1 || $mes > 12 ) {
		$result['errors'][] = __( 'Elige el mes de nacimiento.', 'afectivalab' );
	}

	$anio_actual = (int) current_time( 'Y' );
	$anio_minimo = afectivalab_hijo_anio_minimo();

	if ( $anio < $anio_minimo || $anio > $anio_actual ) {
		$result['errors'][] = sprintf(
			/* translators: 1: año más antiguo aceptado, 2: año actual. */
			__( 'Elige un año de nacimiento entre %1$d y %2$d. Acompañamos a las familias hasta los 17 años.', 'afectivalab' ),
			$anio_minimo,
			$anio_actual
		);
	}

	if ( $result['errors'] ) {
		return $result;
	}

	if ( $hijo ) {
		wp_update_post(
			array(
				'ID'         => $hijo->ID,
				'post_title' => $nombre,
			)
		);

		$guardado_id = $hijo->ID;
	} else {
		$guardado_id = wp_insert_post(
			array(
				'post_type'   => AFECTIVALAB_CPT_HIJO,
				'post_status' => 'publish',
				'post_title'  => $nombre,
				'post_author' => $user_id,
			),
			true
		);

		if ( is_wp_error( $guardado_id ) ) {
			$result['errors'][] = __( 'No pudimos guardar el perfil, intenta de nuevo.', 'afectivalab' );
			return $result;
		}
	}

	update_post_meta( $guardado_id, '_afectivalab_nacimiento_mes', $mes );
	update_post_meta( $guardado_id, '_afectivalab_nacimiento_anio', $anio );
	update_post_meta( $guardado_id, '_afectivalab_preocupaciones', $preocupaciones );

	$result['editando'] = 0;
	$result['valores']  = array(
		'nombre'         => '',
		'mes'            => 0,
		'anio'           => 0,
		'preocupaciones' => array(),
	);

	$result['notice'] = $hijo
		? sprintf(
			/* translators: %s: nombre del hijo. */
			__( 'Actualizamos el perfil de %s.', 'afectivalab' ),
			$nombre
		)
		: sprintf(
			/* translators: %s: nombre del hijo. */
			__( 'Agregamos a %s. Ya podemos armar su ruta.', 'afectivalab' ),
			$nombre
		);

	return $result;
}
