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
		'mi-cuenta'   => 'page-templates/mi-cuenta.php',
		'mis-hijos'   => 'page-templates/mis-hijos.php',
		'panel'       => 'page-templates/panel.php',
		'certificado' => 'page-templates/certificado.php',
		'mundos'      => 'page-templates/mundos.php',
		'nosotros'    => 'page-templates/nosotros.php',
		'privacidad'  => 'page-templates/privacidad.php',
		'terminos'    => 'page-templates/terminos.php',
		'contacto'    => 'page-templates/contacto.php',
		'suscribirse' => 'page-templates/suscribirse.php',
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
	} elseif ( 'mi-cuenta' === $route ) {
		$title['title'] = __( 'Mi cuenta', 'afectivalab' );
	} elseif ( 'mis-hijos' === $route ) {
		$title['title'] = __( 'Mis hijos', 'afectivalab' );
	} elseif ( 'panel' === $route ) {
		$title['title'] = __( 'Mi panel', 'afectivalab' );
	} elseif ( 'certificado' === $route ) {
		$title['title'] = __( 'Certificado', 'afectivalab' );
	} elseif ( 'mundos' === $route ) {
		$title['title'] = __( 'Explora por mundo temático', 'afectivalab' );
	} elseif ( 'nosotros' === $route ) {
		$title['title'] = __( 'Nosotros', 'afectivalab' );
	} elseif ( 'privacidad' === $route ) {
		$title['title'] = __( 'Política de privacidad', 'afectivalab' );
	} elseif ( 'terminos' === $route ) {
		$title['title'] = __( 'Términos de uso', 'afectivalab' );
	} elseif ( 'contacto' === $route ) {
		$title['title'] = __( 'Escríbenos', 'afectivalab' );
	} elseif ( 'suscribirse' === $route ) {
		$title['title'] = __( 'Suscríbete', 'afectivalab' );
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
 * clave de la opción para forzar otro flush (v11 sumó /renovar — ya no
 * existe, se quitó al migrar el cobro a Paid Memberships Pro + PayPal, que
 * maneja la renovación solo; no hace falta un flush nuevo solo por sacar
 * una ruta. Antes, v10 sumó /suscribirse, v9 /nosotros, /privacidad,
 * /terminos y /contacto, v8 /mundos, v7 /certificado, v6 /panel, v5
 * /mis-hijos, v4 los tipos de contenido de cursos y microclases con sus
 * taxonomías, v3 /mi-cuenta y v2 /recuperar y /restablecer).
 */
function afectivalab_maybe_flush_rewrites() {
	if ( ! get_option( 'afectivalab_rewrites_flushed_v11' ) ) {
		afectivalab_register_rewrites();
		flush_rewrite_rules();
		update_option( 'afectivalab_rewrites_flushed_v11', 1 );
	}
}
add_action( 'init', 'afectivalab_maybe_flush_rewrites', 20 );
