<?php
/**
 * Styles and scripts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function afectivalab_assets() {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'afectivalab-fonts',
		'https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito+Sans:wght@400;600;700&family=Caveat:wght@600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'afectivalab-tokens', get_theme_file_uri( 'assets/css/tokens.css' ), array(), $theme_version );
	wp_enqueue_style( 'afectivalab-base', get_theme_file_uri( 'assets/css/base.css' ), array( 'afectivalab-tokens' ), $theme_version );
	wp_enqueue_style( 'afectivalab-home', get_theme_file_uri( 'assets/css/home.css' ), array( 'afectivalab-base' ), $theme_version );

	wp_enqueue_script( 'afectivalab-main', get_theme_file_uri( 'assets/js/main.js' ), array(), $theme_version, true );
}
add_action( 'wp_enqueue_scripts', 'afectivalab_assets' );
