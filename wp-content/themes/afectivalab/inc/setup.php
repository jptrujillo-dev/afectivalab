<?php
/**
 * Theme setup: supports, menus, image sizes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function afectivalab_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'automatic-feed-links' );

	register_nav_menus(
		array(
			'primary' => __( 'Menú principal', 'afectivalab' ),
			'footer'  => __( 'Menú de pie de página', 'afectivalab' ),
		)
	);

}
add_action( 'after_setup_theme', 'afectivalab_setup' );

/**
 * El proyecto no usa emojis/símbolos en ninguna parte de la UI (regla de marca);
 * se desactivan los scripts de conversión de emoji de WordPress para no cargarlos de más.
 */
function afectivalab_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'afectivalab_disable_emojis' );

/**
 * Favicon del theme (brand/afectivalab-v3/favicon). Se omite si en algún
 * momento se configura un "Site Icon" propio desde el Personalizador, para
 * no duplicar etiquetas.
 */
function afectivalab_favicon() {
	if ( has_site_icon() ) {
		return;
	}
	?>
	<link rel="icon" href="<?php echo esc_url( get_theme_file_uri( 'assets/favicon/favicon.ico' ) ); ?>" sizes="any">
	<link rel="icon" type="image/svg+xml" href="<?php echo esc_url( get_theme_file_uri( 'assets/favicon/favicon.svg' ) ); ?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( get_theme_file_uri( 'assets/favicon/icon-32.png' ) ); ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( get_theme_file_uri( 'assets/favicon/icon-16.png' ) ); ?>">
	<link rel="apple-touch-icon" href="<?php echo esc_url( get_theme_file_uri( 'assets/favicon/icon-180.png' ) ); ?>">
	<?php
}
add_action( 'wp_head', 'afectivalab_favicon', 1 );
