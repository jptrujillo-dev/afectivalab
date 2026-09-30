<?php
/**
 * Ruta /suscribirse — servida vía inc/routes.php (template_include). A la
 * que redirige afectivalab_requiere_suscripcion() cuando una cuenta sin
 * suscripción activa intenta entrar a /panel o /mis-hijos.
 *
 * El pago en sí lo maneja Paid Memberships Pro + su pasarela de PayPal
 * (ver inc/suscripciones.php) — el botón de acá solo lleva al checkout real
 * de PMPro para el nivel de Afectivalab, no hay ningún formulario de tarjeta
 * en esta página (PayPal siempre redirige a su propio sitio para eso).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/ingresar' ) );
	exit;
}

// El equipo de contenido no tiene nada que hacer acá — no son clientes.
if ( afectivalab_es_del_equipo() ) {
	wp_safe_redirect( home_url( '/panel' ) );
	exit;
}

// Ya está activa: no tiene sentido mostrarle el paywall.
if ( afectivalab_usuario_tiene_suscripcion_activa() ) {
	wp_safe_redirect( home_url( '/panel' ) );
	exit;
}

$afectivalab_precio          = afectivalab_mp_precio_local();
$afectivalab_pmpro_disponible = function_exists( 'pmpro_url' ) && get_option( 'pmpro_checkout_page_id' );
$afectivalab_checkout_url    = $afectivalab_pmpro_disponible
	? pmpro_url( 'checkout', 'level=' . AFECTIVALAB_PMPRO_LEVEL_ID )
	: '';

get_header();
?>

<main class="suscripcion-page">
	<div class="container suscripcion-page__narrow">

		<header class="suscripcion-hero reveal">
			<h1><?php esc_html_e( 'Suscríbete a Afectivalab', 'afectivalab' ); ?></h1>
			<p><?php esc_html_e( 'Acceso completo para toda la familia: rutas de contenido, misiones, casos prácticos y certificados para hasta 5 hijos.', 'afectivalab' ); ?></p>
		</header>

		<div class="suscripcion-card reveal">
			<span class="suscripcion-card__precio">
				<?php
				printf(
					/* translators: 1: precio en la moneda local, 2: moneda. */
					esc_html__( '%1$s %2$s', 'afectivalab' ),
					esc_html( number_format_i18n( $afectivalab_precio, 2 ) ),
					esc_html( AFECTIVALAB_MP_MONEDA )
				);
				?>
				<span class="suscripcion-card__periodo"><?php esc_html_e( '/ mes', 'afectivalab' ); ?></span>
			</span>

			<p class="suscripcion-card__referencia">
				<?php
				printf(
					/* translators: %d: precio de referencia en dólares. */
					esc_html__( 'Se cobra en %d USD (PayPal no admite pago en soles). Precio de referencia al tipo de cambio del día.', 'afectivalab' ),
					absint( AFECTIVALAB_MP_PRECIO_USD )
				);
				?>
			</p>

			<ul class="suscripcion-card__incluye">
				<li><?php afectivalab_icon( 'check' ); ?><?php esc_html_e( 'Hasta 5 perfiles de hijo', 'afectivalab' ); ?></li>
				<li><?php afectivalab_icon( 'check' ); ?><?php esc_html_e( 'Ruta personalizada por edad e intereses', 'afectivalab' ); ?></li>
				<li><?php afectivalab_icon( 'check' ); ?><?php esc_html_e( 'Misiones, casos prácticos y certificados', 'afectivalab' ); ?></li>
				<li><?php afectivalab_icon( 'check' ); ?><?php esc_html_e( 'Cancela cuando quieras', 'afectivalab' ); ?></li>
			</ul>

			<?php if ( ! $afectivalab_pmpro_disponible ) : ?>
				<p class="suscripcion-card__aviso">
					<?php esc_html_e( 'Las suscripciones todavía no están configuradas. Vuelve más tarde.', 'afectivalab' ); ?>
				</p>
			<?php else : ?>
				<a href="<?php echo esc_url( $afectivalab_checkout_url ); ?>" class="btn btn-primary btn-lg form-submit" data-suscribirse-boton>
					<?php esc_html_e( 'Suscribirme con PayPal', 'afectivalab' ); ?>
					<?php afectivalab_icon( 'arrow-right' ); ?>
				</a>
			<?php endif; ?>

			<p class="suscripcion-card__nota">
				<?php afectivalab_icon( 'lock' ); ?>
				<?php esc_html_e( 'El pago se procesa de forma segura con PayPal.', 'afectivalab' ); ?>
			</p>
		</div>

	</div>
</main>

<?php get_footer(); ?>
