<?php
/**
 * Home: explora por etapas.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_stages = array(
	array(
		'icon'  => 'etapa-1',
		'range' => __( '3 – 5 años', 'afectivalab' ),
		'title' => __( 'Primera infancia', 'afectivalab' ),
		'desc'  => __( 'Vínculo, emociones básicas, límites y autonomía.', 'afectivalab' ),
	),
	array(
		'icon'  => 'etapa-2',
		'range' => __( '6 – 8 años', 'afectivalab' ),
		'title' => __( 'Niñez inicial', 'afectivalab' ),
		'desc'  => __( 'Autoestima, amistades, bullying inicial y pantallas.', 'afectivalab' ),
	),
	array(
		'icon'  => 'etapa-3',
		'range' => __( '9 – 11 años', 'afectivalab' ),
		'title' => __( 'Niñez media', 'afectivalab' ),
		'desc'  => __( 'Comunicación, presión social y cambios emocionales.', 'afectivalab' ),
	),
	array(
		'icon'  => 'etapa-4',
		'range' => __( '12 – 14 años', 'afectivalab' ),
		'title' => __( 'Adolescencia inicial', 'afectivalab' ),
		'desc'  => __( 'Identidad, redes sociales y educación sexual.', 'afectivalab' ),
	),
	array(
		'icon'  => 'etapa-5',
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
					<div class="stage-card__icon"><?php afectivalab_icon( $stage['icon'] ); ?></div>
					<span class="stage-range"><?php echo esc_html( $stage['range'] ); ?></span>
					<h3><?php echo esc_html( $stage['title'] ); ?></h3>
					<p><?php echo esc_html( $stage['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
