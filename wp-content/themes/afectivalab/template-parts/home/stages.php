<?php
/**
 * Home: explora por etapas.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_stages = array(
	array(
		'image' => 'etapa-3-5.webp',
		'range' => __( '3 – 5 años', 'afectivalab' ),
		'title' => __( 'Primera infancia', 'afectivalab' ),
		'desc'  => __( 'Vínculo, emociones básicas, límites y autonomía.', 'afectivalab' ),
	),
	array(
		'image' => 'etapa-6-8.webp',
		'range' => __( '6 – 8 años', 'afectivalab' ),
		'title' => __( 'Niñez inicial', 'afectivalab' ),
		'desc'  => __( 'Autoestima, amistades, bullying inicial y pantallas.', 'afectivalab' ),
	),
	array(
		'image' => 'etapa-9-11.webp',
		'range' => __( '9 – 11 años', 'afectivalab' ),
		'title' => __( 'Niñez media', 'afectivalab' ),
		'desc'  => __( 'Comunicación, presión social y cambios emocionales.', 'afectivalab' ),
	),
	array(
		'image' => 'etapa-12-14.webp',
		'range' => __( '12 – 14 años', 'afectivalab' ),
		'title' => __( 'Adolescencia inicial', 'afectivalab' ),
		'desc'  => __( 'Identidad, redes sociales y educación sexual.', 'afectivalab' ),
	),
	array(
		'image' => 'etapa-15-17.webp',
		'range' => __( '15 – 17 años', 'afectivalab' ),
		'title' => __( 'Adolescencia media/tardía', 'afectivalab' ),
		'desc'  => __( 'Autonomía, relaciones y proyecto de vida.', 'afectivalab' ),
	),
);
?>
<section class="stages" id="etapas">
	<div class="container">
		<div class="section-head">
			<span class="eyebrow"><?php esc_html_e( 'Explora por etapas', 'afectivalab' ); ?></span>
			<h2><?php esc_html_e( 'Cada etapa trae nuevos retos y grandes oportunidades', 'afectivalab' ); ?></h2>
		</div>

		<div class="stages__grid">
			<?php foreach ( $afectivalab_stages as $stage ) : ?>
				<div class="stage-card">
					<div class="stage-card__portrait">
						<img
							src="<?php echo esc_url( get_theme_file_uri( 'assets/images/' . $stage['image'] ) ); ?>"
							alt=""
							width="200"
							height="200"
							loading="lazy"
						>
					</div>
					<span class="stage-range"><?php echo esc_html( $stage['range'] ); ?></span>
					<h3><?php echo esc_html( $stage['title'] ); ?></h3>
					<p><?php echo esc_html( $stage['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
