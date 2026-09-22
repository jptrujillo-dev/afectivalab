<?php
/**
 * Guía de primeros pasos para el padre o la madre recién registrada.
 *
 * Después de crear la cuenta, la plataforma cae en /mis-hijos sin explicar
 * nada: quien llega no sabe qué es una ruta, ni por qué le piden la edad de
 * su hijo, ni qué pasa después. Esta guía lo cuenta mientras hace falta.
 *
 * Es **dinámica**: cada paso se marca solo cuando de verdad ocurrió, así que
 * va indicando qué toca ahora en vez de ser un texto fijo. Cuando los cuatro
 * están hechos desaparece sola.
 *
 * También esconde el escritorio de WordPress a las familias: una cuenta de
 * padre no tiene nada que hacer ahí, y ver la barra gris de administración
 * arriba rompe la sensación de estar en una plataforma propia.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Los cuatro pasos, con su estado real.
 *
 * @return array<int, array{clave: string, titulo: string, texto: string, hecho: bool, enlace: string, accion: string}>
 */
function afectivalab_onboarding_pasos() {
	$hijos = afectivalab_get_hijos();

	$tiene_hijo    = (bool) $hijos;
	$vio_clase     = false;
	$termino_curso = false;

	foreach ( $hijos as $hijo ) {
		if ( afectivalab_clases_completadas( $hijo->ID ) ) {
			$vio_clase = true;
		}

		if ( afectivalab_habilidades_del_hijo( $hijo->ID ) ) {
			$termino_curso = true;
			break;
		}
	}

	// El curso que toca, para que el paso 3 lleve directo ahí en vez de a una
	// pantalla genérica.
	$enlace_clase = home_url( '/panel' );

	if ( $hijos ) {
		$actual = afectivalab_curso_actual( $hijos[0]->ID );

		if ( $actual ) {
			$enlace_clase = get_permalink( $actual['curso'] );
		}
	}

	return array(
		array(
			'clave'  => 'cuenta',
			'titulo' => __( 'Crea tu cuenta', 'afectivalab' ),
			'texto'  => __( 'Listo, ya estás dentro.', 'afectivalab' ),
			'hecho'  => true,
			'enlace' => '',
			'accion' => '',
		),
		array(
			'clave'  => 'hijo',
			'titulo' => __( 'Cuéntanos de tu hijo o hija', 'afectivalab' ),
			'texto'  => __( 'Su nombre y cuándo nació. Con eso sabemos por qué etapa está pasando. También puedes marcar qué temas te preocupan hoy.', 'afectivalab' ),
			'hecho'  => $tiene_hijo,
			// Con ancla: quien ya está en /mis-hijos y pulsa el botón tiene
			// que llegar al formulario, no quedarse donde estaba sin que pase
			// nada visible.
			'enlace' => home_url( '/mis-hijos#formulario' ),
			'accion' => __( 'Agregar a mi hijo', 'afectivalab' ),
		),
		array(
			'clave'  => 'clase',
			'titulo' => __( 'Empieza su ruta', 'afectivalab' ),
			'texto'  => __( 'Armamos un camino de cursos para su edad. Cada curso son microclases cortas, de 10 a 15 minutos, que se van abriendo una tras otra.', 'afectivalab' ),
			'hecho'  => $vio_clase,
			'enlace' => $enlace_clase,
			'accion' => __( 'Ver la primera clase', 'afectivalab' ),
		),
		array(
			'clave'  => 'curso',
			'titulo' => __( 'Gana tu primera habilidad', 'afectivalab' ),
			'texto'  => __( 'Al terminar un curso completo obtienes una habilidad y su certificado. Y la ruta se va ajustando sola a medida que tu hijo crece.', 'afectivalab' ),
			'hecho'  => $termino_curso,
			'enlace' => '',
			'accion' => '',
		),
	);
}

/**
 * ¿Hay que mostrarle la guía a esta persona?
 *
 * Solo a las familias, solo mientras quede algo por hacer, y solo si no la
 * escondió a mano.
 */
function afectivalab_mostrar_onboarding() {
	if ( ! is_user_logged_in() || afectivalab_es_del_equipo() ) {
		return false;
	}

	if ( get_user_meta( get_current_user_id(), 'afectivalab_guia_oculta', true ) ) {
		return false;
	}

	foreach ( afectivalab_onboarding_pasos() as $paso ) {
		if ( ! $paso['hecho'] ) {
			return true;
		}
	}

	return false;
}

/**
 * El enlace de "ya entendí, no me la muestres más".
 */
function afectivalab_onboarding_url_ocultar() {
	return wp_nonce_url( add_query_arg( 'ocultar_guia', '1', home_url( '/panel' ) ), 'afectivalab_ocultar_guia' );
}

function afectivalab_onboarding_ocultar() {
	if ( empty( $_GET['ocultar_guia'] ) || ! is_user_logged_in() ) {
		return;
	}

	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'afectivalab_ocultar_guia' ) ) {
		return;
	}

	update_user_meta( get_current_user_id(), 'afectivalab_guia_oculta', 1 );

	wp_safe_redirect( home_url( '/panel' ) );
	exit;
}
add_action( 'template_redirect', 'afectivalab_onboarding_ocultar' );

/**
 * La barra gris de administración solo para quien trabaja en el contenido.
 */
function afectivalab_admin_bar( $mostrar ) {
	return afectivalab_es_del_equipo() ? $mostrar : false;
}
add_filter( 'show_admin_bar', 'afectivalab_admin_bar' );

/**
 * El escritorio de WordPress, tampoco.
 *
 * Una cuenta de padre no tiene nada que hacer ahí; si llega (por un enlace
 * viejo o escribiendo /wp-admin), se la devuelve a su panel.
 *
 * Se deja pasar admin-ajax.php a propósito: es el que atiende el inicio de
 * sesión por AJAX y otras peticiones de fondo, y bloquearlo rompería el
 * sitio en vez de protegerlo.
 */
function afectivalab_bloquear_escritorio() {
	if ( wp_doing_ajax() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
		return;
	}

	if ( ! is_user_logged_in() || afectivalab_es_del_equipo() ) {
		return;
	}

	wp_safe_redirect( home_url( '/panel' ) );
	exit;
}
add_action( 'admin_init', 'afectivalab_bloquear_escritorio' );
