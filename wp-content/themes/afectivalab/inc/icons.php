<?php
/**
 * Inline SVG icon helper.
 *
 * Regla del proyecto: no se usan emojis ni símbolos de texto en la UI.
 * Todo ícono se sirve como SVG desde assets/icons/ a través de esta función,
 * nunca como carácter unicode suelto en las plantillas.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Devuelve el markup de un ícono SVG del theme.
 *
 * @param string $name  Nombre del archivo sin extensión (ej. "check", "logo-color").
 * @param string $class Clases CSS adicionales para el <svg>.
 * @return string Markup SVG, o cadena vacía si no existe.
 */
function afectivalab_get_icon( $name, $class = '' ) {
	static $cache = array();

	$name = sanitize_file_name( $name );

	if ( isset( $cache[ $name ] ) ) {
		$svg = $cache[ $name ];
	} else {
		$path = get_theme_file_path( 'assets/icons/' . $name . '.svg' );
		$svg  = file_exists( $path ) ? file_get_contents( $path ) : '';
		$cache[ $name ] = $svg;
	}

	if ( '' === $svg ) {
		return '';
	}

	if ( '' !== $class ) {
		$svg = preg_replace( '/<svg /', '<svg class="' . esc_attr( $class ) . '" ', $svg, 1 );
	}

	return $svg;
}

/**
 * Imprime un ícono SVG directamente.
 */
function afectivalab_icon( $name, $class = '' ) {
	echo afectivalab_get_icon( $name, $class ); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG confiable del propio theme.
}
