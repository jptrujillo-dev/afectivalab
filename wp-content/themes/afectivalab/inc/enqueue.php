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
		'https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito+Sans:wght@400;500;600;700&family=Caveat:wght@600;700&display=swap',
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

		// Para el login por AJAX. Si esto no llega (por ejemplo si algo
		// bloquea admin-ajax.php), auth.js no intercepta nada y el
		// formulario se envía como siempre.
		wp_localize_script(
			'afectivalab-auth',
			'afectivalabAuth',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'entrando' => __( 'Entrando…', 'afectivalab' ),
			)
		);
	}

	if ( 'mi-cuenta' === get_query_var( 'afectivalab_route' ) ) {
		wp_enqueue_style( 'afectivalab-cuenta', get_theme_file_uri( 'assets/css/cuenta.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/cuenta.css' ) );
		wp_enqueue_script( 'afectivalab-cuenta', get_theme_file_uri( 'assets/js/cuenta.js' ), array(), afectivalab_asset_version( 'assets/js/cuenta.js' ), true );

		// Para el modal de "Cambiar contraseña" (afectivalab_ajax_cambiar_password
		// en inc/account.php) — mismo patrón que afectivalabAuth en el login.
		// El botón ya muestra un spinner genérico (iniciarLoaderFormularios()
		// en cuenta.js), no hace falta duplicar un texto de "cargando" acá.
		wp_localize_script(
			'afectivalab-cuenta',
			'afectivalabCuenta',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	// El panel y las pantallas de curso/microclase comparten hoja: son la
	// misma parte del producto, la que ve la familia.
	// /mis-hijos también la carga: de ahí salen la guía de primeros pasos y
	// la barra de progreso que esa guía usa.
	if ( in_array( get_query_var( 'afectivalab_route' ), array( 'panel', 'mis-hijos' ), true ) || is_singular( array( AFECTIVALAB_CPT_CURSO, AFECTIVALAB_CPT_CLASE ) ) ) {
		wp_enqueue_style( 'afectivalab-plataforma', get_theme_file_uri( 'assets/css/plataforma.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/plataforma.css' ) );
	}

	// Los formularios de curso y microclase del panel.
	if ( 'panel' === get_query_var( 'afectivalab_route' ) ) {
		wp_enqueue_script( 'afectivalab-panel', get_theme_file_uri( 'assets/js/panel.js' ), array(), afectivalab_asset_version( 'assets/js/panel.js' ), true );

		// El editor con formato (negrita, cursiva, listas) de los campos de
		// descripción. Se pide explícitamente porque wp_editor() está pensado
		// para el escritorio: en el front no siempre carga sus scripts solo.
		if ( afectivalab_es_del_equipo() ) {
			wp_enqueue_editor();
		}

		// Panel del equipo: su propia hoja y todo lo que va sin recargar
		// (guardar, publicar, eliminar, ordenar, roles, modales). Misma
		// condición que page-templates/panel.php para mostrarlo.
		if ( afectivalab_es_del_equipo() && ( ! isset( $_GET['vista'] ) || 'familia' !== $_GET['vista'] ) ) {
			wp_enqueue_style( 'afectivalab-equipo', get_theme_file_uri( 'assets/css/equipo.css' ), array( 'afectivalab-plataforma' ), afectivalab_asset_version( 'assets/css/equipo.css' ) );
			wp_enqueue_script( 'afectivalab-equipo', get_theme_file_uri( 'assets/js/equipo.js' ), array(), afectivalab_asset_version( 'assets/js/equipo.js' ), true );
			wp_localize_script(
				'afectivalab-equipo',
				'afectivalabEquipo',
				array(
					'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
					'maxImagen'  => (int) wp_max_upload_size(),
					'textos'     => array(
						'red'         => __( 'No pudimos conectar. Revisa tu conexión e intenta de nuevo.', 'afectivalab' ),
						'guardando'   => __( 'Guardando…', 'afectivalab' ),
						'sinGuardar'  => __( 'Cambios sin guardar', 'afectivalab' ),
						'guardado'    => __( 'Guardado hace un momento', 'afectivalab' ),
						'guardar'     => __( 'Guardar cambios', 'afectivalab' ),
						'salir'       => __( 'Tienes cambios sin guardar. ¿Salir igual?', 'afectivalab' ),
						'imagenTipo'  => __( 'La imagen debe ser JPG, PNG o WEBP.', 'afectivalab' ),
						'imagenPeso'  => __( 'La imagen pesa %1$s MB y el máximo es %2$s MB.', 'afectivalab' ),
						'cambiar'     => __( 'Cambiar imagen', 'afectivalab' ),
						'elegir'      => __( 'Elegir imagen', 'afectivalab' ),
						'caracteres'  => __( '%1$d / %2$d', 'afectivalab' ),
					),
				)
			);
		}
	}

	// Caso práctico, misión y "marcar como vista" por AJAX (ver
	// inc/clase-ajax.php). Si esto no llega, los formularios se envían
	// normal y los procesa la plantilla.
	if ( is_singular( AFECTIVALAB_CPT_CLASE ) ) {
		wp_enqueue_script( 'afectivalab-clase', get_theme_file_uri( 'assets/js/clase.js' ), array(), afectivalab_asset_version( 'assets/js/clase.js' ), true );
		wp_localize_script(
			'afectivalab-clase',
			'afectivalabClase',
			array(
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'errorRed'    => __( 'No pudimos conectar. Revisa tu conexión e intenta de nuevo.', 'afectivalab' ),
				'errorTipo'   => __( 'La foto debe ser JPG, PNG o WEBP.', 'afectivalab' ),
				'errorPeso'   => __( 'La foto pesa %s MB. El máximo es 3 MB.', 'afectivalab' ),
				'errorFalta'  => __( 'Sube una foto para completar esta misión.', 'afectivalab' ),
				'cambiarFoto' => __( 'Toca para cambiar la foto', 'afectivalab' ),
				'maxBytes'    => 3 * MB_IN_BYTES,
			)
		);
	}

	if ( 'mis-hijos' === get_query_var( 'afectivalab_route' ) ) {
		// cuenta.css además de hijos.css: de ahí sale .account-page, el
		// envoltorio de fondo y padding que comparten las dos páginas.
		wp_enqueue_style( 'afectivalab-cuenta', get_theme_file_uri( 'assets/css/cuenta.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/cuenta.css' ) );
		wp_enqueue_style( 'afectivalab-hijos', get_theme_file_uri( 'assets/css/hijos.css' ), array( 'afectivalab-cuenta' ), afectivalab_asset_version( 'assets/css/hijos.css' ) );

		// Contador de temas elegidos y spinner al guardar.
		wp_enqueue_script( 'afectivalab-hijos', get_theme_file_uri( 'assets/js/hijos.js' ), array(), afectivalab_asset_version( 'assets/js/hijos.js' ), true );
	}

	if ( 'mundos' === get_query_var( 'afectivalab_route' ) ) {
		wp_enqueue_style( 'afectivalab-mundos', get_theme_file_uri( 'assets/css/mundos.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/mundos.css' ) );

		// Filtro por edad dentro de un mundo, sin recargar.
		wp_enqueue_script( 'afectivalab-mundos', get_theme_file_uri( 'assets/js/mundos.js' ), array(), afectivalab_asset_version( 'assets/js/mundos.js' ), true );
	}

	// También en las páginas de Paid Memberships Pro (checkout, cuenta, etc.)
	// — son páginas de WordPress normales, ya heredan el header/footer del
	// theme solas, esto solo les suma el estilo del precio/formulario.
	if ( 'suscribirse' === get_query_var( 'afectivalab_route' ) || afectivalab_es_pagina_pmpro() ) {
		wp_enqueue_style( 'afectivalab-suscripcion', get_theme_file_uri( 'assets/css/suscripcion.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/suscripcion.css' ) );
	}

	// Overlay de "redirigiendo al pago" al tocar "Suscribirme" — el link en
	// sí navega normal, esto solo le da una señal visual mientras carga la
	// página siguiente (ver assets/js/suscribirse.js).
	if ( 'suscribirse' === get_query_var( 'afectivalab_route' ) ) {
		wp_enqueue_script(
			'afectivalab-suscribirse',
			get_theme_file_uri( 'assets/js/suscribirse.js' ),
			array(),
			afectivalab_asset_version( 'assets/js/suscribirse.js' ),
			true
		);
		wp_localize_script(
			'afectivalab-suscribirse',
			'afectivalabSuscribirse',
			array( 'mensaje' => __( 'Redirigiendo al pago…', 'afectivalab' ) )
		);
	}

	// Solo en el checkout (no en cuenta/niveles/etc.): salta directo al botón
	// real de PayPal en vez de obligar a un segundo clic — ver
	// assets/js/pmpro-checkout.js. Depende a propósito del script propio de
	// PMPro ('pmpro_checkout', el que registra includes/scripts.php): ese
	// script agrega, dentro de jQuery(document).ready(), un campo oculto
	// (`javascriptok`) que el servidor exige para no marcar el envío como
	// sospechoso — si el nuestro disparara el clic antes de que ese campo
	// exista, PMPro rechaza el checkout con "There are JavaScript errors on
	// the page" (visto en producción). La dependencia solo garantiza el
	// orden de carga; el propio script espera el mismo evento de "documento
	// listo" para terminar de alinearse con el de PMPro.
	if ( function_exists( 'pmpro_is_checkout' ) && pmpro_is_checkout() ) {
		wp_enqueue_script(
			'afectivalab-pmpro-checkout',
			get_theme_file_uri( 'assets/js/pmpro-checkout.js' ),
			array( 'pmpro_checkout' ),
			afectivalab_asset_version( 'assets/js/pmpro-checkout.js' ),
			true
		);
		wp_localize_script(
			'afectivalab-pmpro-checkout',
			'afectivalabPmproCheckout',
			array( 'mensaje' => __( 'Confirmando tu plan, te llevamos a PayPal…', 'afectivalab' ) )
		);
	}

	// Solo en la confirmación: abre el recibo (ya renderizado, oculto) en un
	// modal al tocar "Ver pedido" en vez de mostrarlo siempre debajo — ver
	// paid-memberships-pro/pages/confirmation.php (la copia de esta
	// plantilla en el theme) y assets/js/pmpro-confirmacion.js.
	if ( function_exists( 'afectivalab_es_pagina_confirmacion_pmpro' ) && afectivalab_es_pagina_confirmacion_pmpro() ) {
		wp_enqueue_script(
			'afectivalab-pmpro-confirmacion',
			get_theme_file_uri( 'assets/js/pmpro-confirmacion.js' ),
			array(),
			afectivalab_asset_version( 'assets/js/pmpro-confirmacion.js' ),
			true
		);
	}

	if ( in_array( get_query_var( 'afectivalab_route' ), array( 'nosotros', 'privacidad', 'terminos', 'contacto' ), true ) ) {
		wp_enqueue_style( 'afectivalab-legal', get_theme_file_uri( 'assets/css/legal.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/legal.css' ) );
	}

	if ( 'contacto' === get_query_var( 'afectivalab_route' ) ) {
		wp_enqueue_script( 'afectivalab-auth', get_theme_file_uri( 'assets/js/auth.js' ), array(), afectivalab_asset_version( 'assets/js/auth.js' ), true );
	}

	if ( 'certificado' === get_query_var( 'afectivalab_route' ) ) {
		wp_enqueue_style( 'afectivalab-certificado', get_theme_file_uri( 'assets/css/certificado.css' ), array( 'afectivalab-base' ), afectivalab_asset_version( 'assets/css/certificado.css' ) );
		wp_enqueue_script( 'afectivalab-certificado', get_theme_file_uri( 'assets/js/certificado.js' ), array(), afectivalab_asset_version( 'assets/js/certificado.js' ), true );
	}

	wp_enqueue_script( 'afectivalab-main', get_theme_file_uri( 'assets/js/main.js' ), array(), afectivalab_asset_version( 'assets/js/main.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'afectivalab_assets' );
