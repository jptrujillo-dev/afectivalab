<?php
/**
 * Home: CTA final.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section>
	<div class="container">
		<div class="final-cta">
			<div>
				<h2><?php esc_html_e( 'Criar también se aprende', 'afectivalab' ); ?></h2>
				<p><?php esc_html_e( 'Hoy puedes dar un paso hacia una familia más fuerte.', 'afectivalab' ); ?></p>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/registro' ) ); ?>">
				<?php esc_html_e( 'Quiero empezar', 'afectivalab' ); ?>
				<?php afectivalab_icon( 'arrow-right' ); ?>
			</a>
		</div>
	</div>
</section>
