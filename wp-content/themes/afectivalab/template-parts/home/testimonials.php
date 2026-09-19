<?php
/**
 * Home: testimonios.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_testimonials = array(
	array(
		'quote'  => __( 'Ahora sé cómo hablar con mi hijo en momentos difíciles. Me siento más segura y tranquila.', 'afectivalab' ),
		'name'   => 'María G.',
		'avatar' => 'avatar-maria.webp',
	),
	array(
		'quote'  => __( 'La ruta por edades me ayuda mucho. Los videos son claros y muy prácticos. Totalmente recomendado.', 'afectivalab' ),
		'name'   => 'Carlos R.',
		'avatar' => 'avatar-carlos.webp',
	),
	array(
		'quote'  => __( 'No estamos solos en este camino. Aquí encuentro herramientas reales para mi día a día.', 'afectivalab' ),
		'name'   => 'Ana L.',
		'avatar' => 'avatar-ana.webp',
	),
);
?>
<section class="testimonials">
	<div class="container">
		<div class="section-head reveal">
			<span class="eyebrow"><?php esc_html_e( 'Lo que dicen otras familias', 'afectivalab' ); ?></span>
			<h2><?php esc_html_e( 'Historias reales que inspiran', 'afectivalab' ); ?></h2>
		</div>

		<div class="testimonials__grid reveal-stagger">
			<?php foreach ( $afectivalab_testimonials as $testimonial ) : ?>
				<div class="testimonial-card">
					<div class="testimonial-card__stars">
						<?php for ( $i = 0; $i < 5; $i++ ) : ?>
							<?php afectivalab_icon( 'star' ); ?>
						<?php endfor; ?>
					</div>
					<p>&ldquo;<?php echo esc_html( $testimonial['quote'] ); ?>&rdquo;</p>
					<div class="testimonial-card__author">
						<img
							class="testimonial-card__avatar"
							src="<?php echo esc_url( get_theme_file_uri( 'assets/images/' . $testimonial['avatar'] ) ); ?>"
							alt=""
							width="72"
							height="72"
							loading="lazy"
						>
						<strong><?php echo esc_html( $testimonial['name'] ); ?></strong>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
