<?php
/**
 * Rutas propias del theme (/registro, /ingresar) resueltas por reescritura
 * de URL, sin depender de que exista una Página creada en el escritorio con
 * ese slug. Cada ruta carga su plantilla directamente desde
 * page-templates/.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function afectivalab_routes() {
	return array(
		'registro'    => 'page-templates/registro.php',
		'ingresar'    => 'page-templates/ingresar.php',
		'recuperar'   => 'page-templates/recuperar.php',
		'restablecer' => 'page-templates/restablecer.php',
	);
}

function afectivalab_register_rewrites() {
	foreach ( array_keys( afectivalab_routes() ) as $slug ) {
		add_rewrite_rule( '^' . $slug . '/?$', 'index.php?afectivalab_route=' . $slug, 'top' );
	}
}
add_action( 'init', 'afectivalab_register_rewrites' );

function afectivalab_query_vars( $vars ) {
	$vars[] = 'afectivalab_route';
	return $vars;
}
add_filter( 'query_vars', 'afectivalab_query_vars' );

function afectivalab_route_template( $template ) {
	$route  = get_query_var( 'afectivalab_route' );
	$routes = afectivalab_routes();

	if ( $route && isset( $routes[ $route ] ) ) {
		return get_theme_file_path( $routes[ $route ] );
	}

	return $template;
}
add_filter( 'template_include', 'afectivalab_route_template' );

function afectivalab_route_document_title( $title ) {
	$route = get_query_var( 'afectivalab_route' );

	if ( 'registro' === $route ) {
		$title['title'] = __( 'Crea tu cuenta', 'afectivalab' );
	} elseif ( 'ingresar' === $route ) {
		$title['title'] = __( 'Ingresa a tu cuenta', 'afectivalab' );
	} elseif ( 'recuperar' === $route ) {
		$title['title'] = __( 'Recupera tu contraseña', 'afectivalab' );
	} elseif ( 'restablecer' === $route ) {
		$title['title'] = __( 'Restablece tu contraseña', 'afectivalab' );
	}

	return $title;
}
add_filter( 'document_title_parts', 'afectivalab_route_document_title' );

/**
 * Las reglas de reescritura solo se leen de la base de datos, no del código
 * en cada carga — normalmente se regeneran al activar el theme
 * (after_switch_theme), pero este theme ya estaba activo cuando se agregaron
 * estas rutas. Se fuerza un único flush automático la primera vez que corre
 * este código; si más adelante se agregan más rutas, subir el número de la
 * clave de la opción para forzar otro flush (por eso ya va en v2: se sumaron
 * /recuperar y /restablecer después del primer flush).
 */
function afectivalab_maybe_flush_rewrites() {
	if ( ! get_option( 'afectivalab_rewrites_flushed_v2' ) ) {
		afectivalab_register_rewrites();
		flush_rewrite_rules();
		update_option( 'afectivalab_rewrites_flushed_v2', 1 );
	}
}
add_action( 'init', 'afectivalab_maybe_flush_rewrites', 20 );
