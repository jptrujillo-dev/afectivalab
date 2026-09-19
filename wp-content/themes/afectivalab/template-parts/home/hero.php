<?php
/**
 * Home: hero.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="hero">
	<div class="container hero__inner">
		<div class="hero__content">
			<span class="hero__badge"><?php esc_html_e( 'Acompañamiento para la crianza', 'afectivalab' ); ?></span>

			<h1>
				<?php esc_html_e( 'Hoy padres más preparados,', 'afectivalab' ); ?>
				<span class="highlight"><?php esc_html_e( 'mañana hijos más felices', 'afectivalab' ); ?></span>
			</h1>

			<p class="hero__lead">
				<?php esc_html_e( 'Una plataforma con rutas de aprendizaje para acompañar a madres y padres en cada etapa de la crianza de sus hijos, de 3 a 17 años.', 'afectivalab' ); ?>
			</p>

			<div class="hero__actions">
				<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/registro' ) ); ?>">
					<?php esc_html_e( 'Comenzar ahora', 'afectivalab' ); ?>
					<?php afectivalab_icon( 'arrow-right' ); ?>
				</a>
				<a class="btn btn-secondary" href="#como-funciona">
					<?php afectivalab_icon( 'feature-video' ); ?>
					<?php esc_html_e( 'Ver cómo funciona', 'afectivalab' ); ?>
				</a>
			</div>

			<ul class="hero__trust">
				<li>
					<?php afectivalab_icon( 'check' ); ?>
					<span><?php esc_html_e( 'Contenido basado en evidencia', 'afectivalab' ); ?></span>
				</li>
				<li>
					<?php afectivalab_icon( 'check' ); ?>
					<span><?php esc_html_e( 'Rutas para cada etapa de desarrollo', 'afectivalab' ); ?></span>
				</li>
				<li>
					<?php afectivalab_icon( 'check' ); ?>
					<span><?php esc_html_e( 'Herramientas prácticas para la vida real', 'afectivalab' ); ?></span>
				</li>
			</ul>
		</div>

		<div class="hero__visual">
			<span class="hero__blob" aria-hidden="true"></span>

			<?php afectivalab_icon( 'doodle-leaf', 'hero__doodle hero__doodle--leaf-1' ); ?>
			<?php afectivalab_icon( 'doodle-leaf', 'hero__doodle hero__doodle--leaf-2' ); ?>
			<?php afectivalab_icon( 'doodle-heart', 'hero__doodle hero__doodle--heart' ); ?>
			<span class="hero__caption hero__caption--leaf"><?php esc_html_e( 'Pequeños pasos, grandes logros', 'afectivalab' ); ?></span>

			<!--
				Placeholder de la ilustración de familia: mientras no tengamos el arte
				ilustrado (ver docs/prompts-iconos-ia.md, sección 5.1), se usa una
				composición abstracta de 3 formas (sin caras) que ocupa el mismo
				espacio. Cuando llegue la ilustración real, este bloque se reemplaza
				por una sola <img>.
			-->
			<div class="hero__illustration" aria-hidden="true">
				<span class="hero__illustration-shape hero__illustration-shape--a"></span>
				<span class="hero__illustration-shape hero__illustration-shape--b"></span>
				<span class="hero__illustration-shape hero__illustration-shape--c"></span>
			</div>

			<div class="hero__note hero__note--top"><?php esc_html_e( 'Familias que aprenden también avanzan', 'afectivalab' ); ?></div>

			<div class="app-card">
				<div class="app-card__header">
					<?php afectivalab_icon( 'icon-color', 'icon-badge' ); ?>
					<div>
						<strong><?php esc_html_e( 'Hola, familia', 'afectivalab' ); ?></strong>
						<span><?php esc_html_e( 'Seguimos aprendiendo juntos', 'afectivalab' ); ?></span>
					</div>
				</div>

				<div class="app-card__pills">
					<span class="pill">3–5</span>
					<span class="pill is-active">6–8</span>
					<span class="pill">9–11</span>
					<span class="pill">12–14</span>
					<span class="pill">15–17</span>
				</div>

				<div class="app-card__route">
					<div class="route-node">
						<span class="route-node__dot"><?php afectivalab_icon( 'check' ); ?></span>
						<span><?php esc_html_e( 'Comunicación y confianza', 'afectivalab' ); ?></span>
					</div>
					<div class="route-node is-current">
						<span class="route-node__dot"><?php afectivalab_icon( 'star' ); ?></span>
						<span><?php esc_html_e( 'Autoestima y seguridad', 'afectivalab' ); ?></span>
					</div>
					<div class="route-node is-locked">
						<span class="route-node__dot"><?php afectivalab_icon( 'lock' ); ?></span>
						<span><?php esc_html_e( 'Bullying: prevenir y actuar', 'afectivalab' ); ?></span>
					</div>
				</div>
			</div>

			<div class="hero__note hero__note--bottom"><?php esc_html_e( 'Una infancia acompañada cambia el mundo', 'afectivalab' ); ?></div>
		</div>
	</div>
</section>
