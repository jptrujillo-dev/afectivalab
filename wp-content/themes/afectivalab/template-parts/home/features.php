<?php
/**
 * Home: franja de características.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_features = array(
	array(
		'icon'  => 'feature-rutas',
		'title' => __( 'Rutas por edades', 'afectivalab' ),
		'desc'  => __( 'Contenido adaptado a cada etapa del desarrollo, de 3 a 17 años.', 'afectivalab' ),
	),
	array(
		'icon'  => 'feature-video',
		'title' => __( 'Video clases cortas', 'afectivalab' ),
		'desc'  => __( 'Aprende a tu ritmo, cuando quieras, en sesiones de pocos minutos.', 'afectivalab' ),
	),
	array(
		'icon'  => 'feature-casos',
		'title' => __( 'Casos reales', 'afectivalab' ),
		'desc'  => __( 'Situaciones de la vida diaria con soluciones prácticas paso a paso.', 'afectivalab' ),
	),
	array(
		'icon'  => 'feature-misiones',
		'title' => __( 'Misiones en familia', 'afectivalab' ),
		'desc'  => __( 'Pequeños retos para aplicar en casa, con recompensas por completarlos.', 'afectivalab' ),
	),
);
?>
<section class="features">
	<div class="container">
		<div class="features__grid reveal-stagger">
			<?php foreach ( $afectivalab_features as $feature ) : ?>
				<div class="feature-card">
					<div class="feature-card__icon"><?php afectivalab_icon( $feature['icon'] ); ?></div>
					<h3><?php echo esc_html( $feature['title'] ); ?></h3>
					<p><?php echo esc_html( $feature['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
