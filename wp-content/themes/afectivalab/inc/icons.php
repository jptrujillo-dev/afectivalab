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

/**
 * Botón de mostrar/ocultar contraseña para un campo de un formulario.
 * Los dos íconos van ambos en el HTML; el CSS/JS solo alterna cuál se ve
 * (ver .form-input__toggle en auth.css y data-password-toggle en auth.js),
 * así el botón funciona igual si JS no llega a cargar (el input se queda
 * como password, que es lo seguro).
 *
 * @param string $for El id del <input> que controla.
 */
function afectivalab_password_toggle( $for ) {
	printf(
		'<button type="button" class="form-input__toggle" data-password-toggle="%1$s" data-label-show="%2$s" data-label-hide="%3$s" aria-label="%2$s"><span class="icon-show">%4$s</span><span class="icon-hide">%5$s</span></button>',
		esc_attr( $for ),
		esc_attr__( 'Mostrar contraseña', 'afectivalab' ),
		esc_attr__( 'Ocultar contraseña', 'afectivalab' ),
		afectivalab_get_icon( 'eye' ), // phpcs:ignore WordPress.Security.EscapeOutput -- SVG confiable del propio theme.
		afectivalab_get_icon( 'eye-off' ) // phpcs:ignore WordPress.Security.EscapeOutput -- SVG confiable del propio theme.
	);
}

/**
 * Medidor de fortaleza de contraseña (Baja/Media/Alta). Lo llena y muestra
 * auth.js mientras la persona escribe en el campo indicado; si JS no corre,
 * el <div> se queda vacío y no estorba (la validación real sigue siendo el
 * mínimo de 8 caracteres del servidor, esto es solo una ayuda visual).
 *
 * @param string $for El id del <input> de contraseña que mide.
 */
function afectivalab_password_strength_meter( $for ) {
	printf(
		'<div class="password-strength" data-password-strength="%1$s"><div class="password-strength__track"><span class="password-strength__fill"></span></div><span class="password-strength__label"></span></div>',
		esc_attr( $for )
	);
}
