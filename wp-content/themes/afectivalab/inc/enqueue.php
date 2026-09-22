<?php
/**
 * Styles and scripts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Versión de un asset del theme basada en su fecha de modificación.
 *
 * Evita el problema clásico de cachear un CSS/JS viejo: en vez de depender
 * de que alguien recuerde subir el número de versión del theme a mano cada
 * vez que se toca un archivo, el ?ver= cambia solo apenas se guarda el
 * archivo — el navegador lo vuelve a pedir automáticamente.
 */
function afectivalab_asset_version( $relative_path ) {
	$path = get_theme_file_path( $relative_path );
	return file_exists( $path ) ? filemtime( $path ) : wp_get_theme()->get( 'Version' );
}

function afectivalab_assets() {
	wp_enqueue_style(
		'afectivalab-fonts',
		'https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito+Sans:wght@400;600;700&family=Caveat:wght@600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'afectivalab-tokens', get_theme_file_uri( 'assets/css/tokens.css' ), array(), afectivalab_asset_version( 'assets/css/tokens.css' ) );
	wp_enqueue_style( 'afectivalab-base', get_theme_file_uri( 'assets/css/base.css' ), array( 'afectivalab-tokens' ), afectivalab_asset_version( 'assets/css/base.css' ) );

	if ( is_front_page() ) {
		wp_enqueue_style( 'afectivalab-home', get_theme_file_uri( 'assets/css/home.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/home.css' ) );
	}

	$afectivalab_auth_routes = array( 'registro', 'ingresar', 'recuperar', 'restablecer' );

	if ( in_array( get_query_var( 'afectivalab_route' ), $afectivalab_auth_routes, true ) ) {
		wp_enqueue_style( 'afectivalab-auth', get_theme_file_uri( 'assets/css/auth.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/auth.css' ) );
		wp_enqueue_script( 'afectivalab-auth', get_theme_file_uri( 'assets/js/auth.js' ), array(), afectivalab_asset_version( 'assets/js/auth.js' ), true );
	}

	if ( 'mi-cuenta' === get_query_var( 'afectivalab_route' ) ) {
		wp_enqueue_style( 'afectivalab-cuenta', get_theme_file_uri( 'assets/css/cuenta.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/cuenta.css' ) );
		wp_enqueue_script( 'afectivalab-cuenta', get_theme_file_uri( 'assets/js/cuenta.js' ), array(), afectivalab_asset_version( 'assets/js/cuenta.js' ), true );
	}

	// El panel y las pantallas de curso/microclase comparten hoja: son la
	// misma parte del producto, la que ve la familia.
	if ( 'panel' === get_query_var( 'afectivalab_route' ) || is_singular( array( AFECTIVALAB_CPT_CURSO, AFECTIVALAB_CPT_CLASE ) ) ) {
		wp_enqueue_style( 'afectivalab-plataforma', get_theme_file_uri( 'assets/css/plataforma.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/plataforma.css' ) );
	}

	if ( 'mis-hijos' === get_query_var( 'afectivalab_route' ) ) {
		// cuenta.css además de hijos.css: de ahí sale .account-page, el
		// envoltorio de fondo y padding que comparten las dos páginas.
		wp_enqueue_style( 'afectivalab-cuenta', get_theme_file_uri( 'assets/css/cuenta.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/cuenta.css' ) );
		wp_enqueue_style( 'afectivalab-hijos', get_theme_file_uri( 'assets/css/hijos.css' ), array( 'afectivalab-cuenta' ), afectivalab_asset_version( 'assets/css/hijos.css' ) );
		wp_enqueue_script( 'afectivalab-hijos', get_theme_file_uri( 'assets/js/hijos.js' ), array(), afectivalab_asset_version( 'assets/js/hijos.js' ), true );
	}

	wp_enqueue_script( 'afectivalab-main', get_theme_file_uri( 'assets/js/main.js' ), array(), afectivalab_asset_version( 'assets/js/main.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'afectivalab_assets' );
