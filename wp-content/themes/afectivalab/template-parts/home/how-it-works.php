<?php
/**
 * Home: cómo funciona.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_steps = array(
	array(
		'icon'  => 'paso-elige-edad',
		'title' => __( 'Elige la edad de tu hijo', 'afectivalab' ),
		'desc'  => __( 'Selecciona la etapa que corresponde a su desarrollo.', 'afectivalab' ),
	),
	array(
		'icon'  => 'paso-recibe-ruta',
		'title' => __( 'Recibe una ruta personalizada', 'afectivalab' ),
		'desc'  => __( 'Obtén un plan de aprendizaje con los temas clave para esa etapa.', 'afectivalab' ),
	),
	array(
		'icon'  => 'paso-aplica-casa',
		'title' => __( 'Aprende y aplica en casa', 'afectivalab' ),
		'desc'  => __( 'Con video clases, casos reales y misiones en familia.', 'afectivalab' ),
	),
);
?>
<section class="how-it-works" id="como-funciona">
	<div class="container">
		<div class="section-head center">
			<span class="eyebrow"><?php esc_html_e( '¿Cómo funciona?', 'afectivalab' ); ?></span>
			<h2><?php esc_html_e( 'En solo tres pasos, empieza a acompañar mejor a tu familia', 'afectivalab' ); ?></h2>
		</div>

		<div class="steps">
			<?php foreach ( $afectivalab_steps as $index => $step ) : ?>
				<div class="step">
					<span class="step__number"><?php echo esc_html( $index + 1 ); ?></span>
					<div class="step__icon"><?php afectivalab_icon( $step['icon'] ); ?></div>
					<h3><?php echo esc_html( $step['title'] ); ?></h3>
					<p><?php echo esc_html( $step['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
