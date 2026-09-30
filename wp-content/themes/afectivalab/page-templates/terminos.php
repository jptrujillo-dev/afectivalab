<?php
/**
 * Ruta /terminos — servida vía inc/routes.php (template_include).
 *
 * BORRADOR, mismo criterio que privacidad.php: describe el servicio tal como
 * existe hoy (sin suscripciones todavía, ver docs/roadmap.md bloque 0) y
 * queda pendiente de revisión legal antes de darlo por definitivo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="legal-page">
	<div class="container legal-page__narrow">

		<header class="legal-hero reveal">
			<h1><?php esc_html_e( 'Términos de uso', 'afectivalab' ); ?></h1>
			<p><?php esc_html_e( 'Las condiciones para usar Afectivalab.', 'afectivalab' ); ?></p>
		</header>

		<div class="legal-borrador reveal">
			<strong><?php esc_html_e( 'Borrador pendiente de revisión legal.', 'afectivalab' ); ?></strong>
			<p>
				<?php esc_html_e( 'Este texto describe el servicio tal como funciona hoy, pero todavía no fue revisado por un abogado. No lo tomes como la versión definitiva hasta que se confirme.', 'afectivalab' ); ?>
			</p>
		</div>

		<div class="legal-card legal-content reveal">

			<h2><?php esc_html_e( '1. Qué es Afectivalab', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Afectivalab es una plataforma de acompañamiento para madres, padres y tutores, con rutas de contenido, misiones y casos prácticos organizados según la edad de cada hijo. No es un servicio de atención psicológica ni reemplaza la consulta con un profesional cuando la situación lo requiere.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( '2. Quién puede crear una cuenta', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'La cuenta la crea y administra un adulto, como padre, madre o tutor legal de los hijos cuyos perfiles agrega. Los menores no crean su propia cuenta.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( '3. Tu cuenta', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Eres responsable de mantener tu contraseña segura y de la actividad que ocurra desde tu cuenta. Avísanos si crees que alguien más accedió a ella sin tu permiso.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( '4. Uso del contenido', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'El contenido de los cursos, microclases, casos prácticos e ilustraciones es de Afectivalab. Puedes usarlo para acompañar a tu familia; no está permitido copiarlo, redistribuirlo o publicarlo en otro sitio sin autorización.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( '5. Lo que subes tú', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Algunas misiones piden subir una foto como evidencia de haberla realizado. Esa foto queda asociada al perfil del hijo dentro de tu cuenta, visible solo para ti — no se publica ni se comparte fuera de la plataforma.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( '6. Costo del servicio', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Hoy el acceso a la plataforma no tiene costo. Si en el futuro se suma un plan de suscripción, esta página se actualizará antes de que entre en vigencia, con el detalle de precios y qué incluye cada plan.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( '7. Qué no está permitido', 'afectivalab' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'Crear perfiles de hijos que no son tuyos ni están bajo tu tutela.', 'afectivalab' ); ?></li>
				<li><?php esc_html_e( 'Intentar acceder a la cuenta de otra familia.', 'afectivalab' ); ?></li>
				<li><?php esc_html_e( 'Subir contenido ofensivo, falso o que no corresponda a la misión que se pide.', 'afectivalab' ); ?></li>
			</ul>

			<h2><?php esc_html_e( '8. Cambios y baja de la cuenta', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Puedes dejar de usar Afectivalab cuando quieras. Para eliminar tu cuenta y los datos asociados, escríbenos desde Contacto.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( '9. Contacto', 'afectivalab' ); ?></h2>
			<p>
				<?php
				printf(
					/* translators: %s: enlace a la página de contacto. */
					esc_html__( 'Ante cualquier duda sobre estos términos, escríbenos desde %s.', 'afectivalab' ),
					'<a href="' . esc_url( home_url( '/contacto' ) ) . '">' . esc_html__( 'Contacto', 'afectivalab' ) . '</a>'
				);
				?>
			</p>

		</div>

	</div>
</main>

<?php get_footer(); ?>
