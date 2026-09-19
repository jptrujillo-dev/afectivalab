<?php
/**
 * Home: testimonios.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_testimonials = array(
	array(
		'quote'   => __( 'Ahora sé cómo hablar con mi hijo en momentos difíciles. Me siento más segura y tranquila.', 'afectivalab' ),
		'name'    => 'María G.',
		'initial' => 'M',
	),
	array(
		'quote'   => __( 'La ruta por edades me ayuda mucho. Los videos son claros y muy prácticos. Totalmente recomendado.', 'afectivalab' ),
		'name'    => 'Carlos R.',
		'initial' => 'C',
	),
	array(
		'quote'   => __( 'No estamos solos en este camino. Aquí encuentro herramientas reales para mi día a día.', 'afectivalab' ),
		'name'    => 'Ana L.',
		'initial' => 'A',
	),
);
?>
<section class="testimonials">
	<div class="container">
		<div class="section-head">
			<span class="eyebrow"><?php esc_html_e( 'Lo que dicen otras familias', 'afectivalab' ); ?></span>
			<h2><?php esc_html_e( 'Historias reales que inspiran', 'afectivalab' ); ?></h2>
		</div>

		<div class="testimonials__grid">
			<?php foreach ( $afectivalab_testimonials as $testimonial ) : ?>
				<div class="testimonial-card">
					<div class="testimonial-card__stars">
						<?php for ( $i = 0; $i < 5; $i++ ) : ?>
							<?php afectivalab_icon( 'star' ); ?>
						<?php endfor; ?>
					</div>
					<p>&ldquo;<?php echo esc_html( $testimonial['quote'] ); ?>&rdquo;</p>
					<div class="testimonial-card__author">
						<span class="testimonial-card__avatar" aria-hidden="true"><?php echo esc_html( $testimonial['initial'] ); ?></span>
						<strong><?php echo esc_html( $testimonial['name'] ); ?></strong>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
