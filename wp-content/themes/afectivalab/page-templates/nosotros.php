<?php
/**
 * Ruta /nosotros — servida vía inc/routes.php (template_include).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="legal-page">
	<div class="container legal-page__narrow">

		<header class="legal-hero reveal">
			<h1><?php esc_html_e( 'Acompañamos a quienes acompañan', 'afectivalab' ); ?></h1>
			<p><?php esc_html_e( 'Afectivalab nació de una idea simple: los padres necesitan tanto acompañamiento como los hijos. Una plataforma que camina junto a las familias en cada etapa del crecimiento, de los 3 a los 17 años.', 'afectivalab' ); ?></p>
		</header>

		<div class="legal-card reveal">
			<h2><?php esc_html_e( 'Qué nos mueve', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'No creamos contenido para que el niño lo consuma solo: creamos herramientas para que el padre o la madre sepan qué hacer en el momento en que hace falta. Autoestima, bullying, pantallas, sexualidad, límites, amistades — los temas que de verdad aparecen en la crianza, explicados sin tecnicismos y acompañados de acciones concretas, no solo de teoría.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( 'Cómo lo hacemos', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Cada familia tiene una ruta propia, armada según la edad de cada hijo y lo que más le preocupa a cada padre. El contenido se organiza en microclases cortas, con misiones que se hacen en la vida real (no solo en la pantalla) y casos prácticos que muestran cómo actuar frente a una situación concreta, con la razón detrás de cada decisión.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( 'Para quién es', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Para madres y padres con hijos de 3 a 17 años que quieren estar un paso adelante, no reaccionando después de que algo ya pasó. No reemplazamos el acompañamiento profesional cuando hace falta — lo complementamos con herramientas para el día a día.', 'afectivalab' ); ?></p>
		</div>

		<div class="legal-card legal-card--cta reveal">
			<div>
				<h2><?php esc_html_e( '¿Quieres empezar?', 'afectivalab' ); ?></h2>
				<p><?php esc_html_e( 'Crea tu cuenta y arma la ruta de tu hijo en un par de minutos.', 'afectivalab' ); ?></p>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/registro' ) ); ?>">
				<?php esc_html_e( 'Empieza gratis', 'afectivalab' ); ?>
				<?php afectivalab_icon( 'arrow-right' ); ?>
			</a>
		</div>

	</div>
</main>

<?php get_footer(); ?>
