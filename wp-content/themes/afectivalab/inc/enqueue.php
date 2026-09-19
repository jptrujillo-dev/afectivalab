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

	if ( in_array( get_query_var( 'afectivalab_route' ), array( 'registro', 'ingresar' ), true ) ) {
		wp_enqueue_style( 'afectivalab-auth', get_theme_file_uri( 'assets/css/auth.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/auth.css' ) );
	}

	wp_enqueue_script( 'afectivalab-main', get_theme_file_uri( 'assets/js/main.js' ), array(), afectivalab_asset_version( 'assets/js/main.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'afectivalab_assets' );
