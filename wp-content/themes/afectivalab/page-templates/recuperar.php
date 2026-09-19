<?php
/**
 * Ruta /recuperar — pedir el link de restablecimiento de contraseña.
 * Servida vía inc/routes.php (template_include), no es una Página del
 * escritorio.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/' ) );
	exit;
}

$afectivalab_forgot = afectivalab_handle_forgot_password();

get_header();
?>

<main class="auth-page">
	<div class="auth-visual reveal">
		<?php afectivalab_icon( 'doodle-leaf', 'hero__doodle hero__doodle--leaf-1' ); ?>
		<?php afectivalab_icon( 'doodle-heart', 'hero__doodle hero__doodle--heart' ); ?>
		<img
			src="<?php echo esc_url( get_theme_file_uri( 'assets/images/hero-familia.webp' ) ); ?>"
			alt="<?php esc_attr_e( 'Familia sonriendo', 'afectivalab' ); ?>"
			width="500"
			height="500"
		>
		<h2><?php esc_html_e( 'No te preocupes, pasa todo el tiempo', 'afectivalab' ); ?></h2>
		<p><?php esc_html_e( 'Te ayudamos a volver a entrar en un par de minutos.', 'afectivalab' ); ?></p>
	</div>

	<div class="auth-form-wrap reveal">
		<div class="auth-form-card">
			<a class="auth-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
				<?php afectivalab_icon( 'logo-color' ); ?>
			</a>

			<?php if ( $afectivalab_forgot['sent'] ) : ?>

				<h1><?php esc_html_e( 'Revisa tu correo', 'afectivalab' ); ?></h1>
				<p><?php esc_html_e( 'Si ese correo tiene una cuenta con nosotros, te acabamos de enviar un link para elegir una nueva contraseña.', 'afectivalab' ); ?></p>

				<p class="auth-switch">
					<a href="<?php echo esc_url( home_url( '/ingresar' ) ); ?>"><?php esc_html_e( 'Volver a ingresar', 'afectivalab' ); ?></a>
				</p>

			<?php else : ?>

				<h1><?php esc_html_e( '¿Olvidaste tu contraseña?', 'afectivalab' ); ?></h1>
				<p><?php esc_html_e( 'Ingresa tu correo y te mandamos un link para restablecerla.', 'afectivalab' ); ?></p>

				<?php if ( ! empty( $afectivalab_forgot['errors'] ) ) : ?>
					<div class="form-alert" role="alert">
						<ul>
							<?php foreach ( $afectivalab_forgot['errors'] as $error ) : ?>
								<li><?php echo esc_html( $error ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<form method="post" novalidate data-validate>
					<?php wp_nonce_field( 'afectivalab_recuperar', 'afectivalab_recuperar_nonce' ); ?>

					<div class="form-field">
						<label for="email"><?php esc_html_e( 'Correo electrónico', 'afectivalab' ); ?></label>
						<div class="form-input">
							<span class="form-input__icon"><?php afectivalab_icon( 'mail' ); ?></span>
							<input
								type="email"
								id="email"
								name="email"
								placeholder="tu@correo.com"
								value="<?php echo esc_attr( $afectivalab_forgot['values']['email'] ); ?>"
								required
								data-validate-field="email"
							>
						</div>
						<span class="form-field__hint"></span>
					</div>

					<button type="submit" name="afectivalab_recuperar_submit" value="1" class="btn btn-primary form-submit">
						<?php esc_html_e( 'Enviar link', 'afectivalab' ); ?>
						<?php afectivalab_icon( 'arrow-right' ); ?>
					</button>
				</form>

				<p class="auth-switch">
					<a href="<?php echo esc_url( home_url( '/ingresar' ) ); ?>"><?php esc_html_e( 'Volver a ingresar', 'afectivalab' ); ?></a>
				</p>

			<?php endif; ?>
		</div>
	</div>
</main>

<?php get_footer(); ?>
