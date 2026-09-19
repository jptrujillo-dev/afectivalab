<?php
/**
 * Menú de respaldo mientras no se configura un menú real en Apariencia > Menús.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function afectivalab_default_menu_items() {
	$home = home_url( '/' );

	return array(
		array( 'label' => __( 'Inicio', 'afectivalab' ), 'url' => $home ),
		array( 'label' => __( 'Cómo funciona', 'afectivalab' ), 'url' => $home . '#como-funciona' ),
		array( 'label' => __( 'Rutas por edades', 'afectivalab' ), 'url' => $home . '#etapas' ),
		array( 'label' => __( 'Casos prácticos', 'afectivalab' ), 'url' => $home . '#casos-practicos' ),
	);
}

function afectivalab_default_primary_menu() {
	echo '<ul>';
	foreach ( afectivalab_default_menu_items() as $item ) {
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}

function afectivalab_default_primary_menu_links() {
	foreach ( afectivalab_default_menu_items() as $item ) {
		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
}
