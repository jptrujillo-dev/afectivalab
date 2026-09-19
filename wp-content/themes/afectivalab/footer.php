<?php
/**
 * Footer del sitio.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	<footer class="site-footer">
		<div class="container">
			<div class="footer-grid">
				<div class="footer-brand">
					<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
						<?php afectivalab_icon( 'logo-white' ); ?>
					</a>
					<p><?php esc_html_e( 'Conocimiento hoy. Bienestar mañana.', 'afectivalab' ); ?></p>
				</div>

				<div class="footer-col">
					<h3><?php esc_html_e( 'Plataforma', 'afectivalab' ); ?></h3>
					<ul>
						<li><a href="<?php echo esc_url( home_url( '/#como-funciona' ) ); ?>"><?php esc_html_e( 'Cómo funciona', 'afectivalab' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/#etapas' ) ); ?>"><?php esc_html_e( 'Rutas por edades', 'afectivalab' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/#casos-practicos' ) ); ?>"><?php esc_html_e( 'Casos prácticos', 'afectivalab' ); ?></a></li>
					</ul>
				</div>

				<div class="footer-col">
					<h3><?php esc_html_e( 'Empresa', 'afectivalab' ); ?></h3>
					<ul>
						<li><a href="<?php echo esc_url( home_url( '/nosotros' ) ); ?>"><?php esc_html_e( 'Nosotros', 'afectivalab' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/privacidad' ) ); ?>"><?php esc_html_e( 'Privacidad', 'afectivalab' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/terminos' ) ); ?>"><?php esc_html_e( 'Términos', 'afectivalab' ); ?></a></li>
					</ul>
				</div>

				<div class="footer-col">
					<h3><?php esc_html_e( 'Contacto', 'afectivalab' ); ?></h3>
					<ul>
						<li><a href="<?php echo esc_url( home_url( '/contacto' ) ); ?>"><?php esc_html_e( 'Escríbenos', 'afectivalab' ); ?></a></li>
					</ul>
				</div>
			</div>

			<div class="footer-bottom">
				<small>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Todos los derechos reservados.', 'afectivalab' ); ?></small>
				<div class="social-links">
					<a href="#" aria-label="YouTube"><?php afectivalab_icon( 'social-youtube' ); ?></a>
					<a href="#" aria-label="Instagram"><?php afectivalab_icon( 'social-instagram' ); ?></a>
					<a href="#" aria-label="Facebook"><?php afectivalab_icon( 'social-facebook' ); ?></a>
				</div>
			</div>
		</div>
	</footer>

<?php wp_footer(); ?>
</body>
</html>
