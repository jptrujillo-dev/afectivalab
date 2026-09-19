<?php
/**
 * Ruta /registro — servida vía inc/routes.php (template_include), no es
 * una Página del escritorio.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/' ) );
	exit;
}

$afectivalab_reg = afectivalab_handle_registration();

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
		<h2><?php esc_html_e( 'Únete a las familias que ya están dando el siguiente paso', 'afectivalab' ); ?></h2>
		<p><?php esc_html_e( 'Rutas de aprendizaje para cada etapa, casos reales y misiones para aplicar en casa.', 'afectivalab' ); ?></p>
	</div>

	<div class="auth-form-wrap reveal">
		<div class="auth-form-card">
			<a class="auth-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
				<?php afectivalab_icon( 'logo-color' ); ?>
			</a>

			<h1><?php esc_html_e( 'Crea tu cuenta', 'afectivalab' ); ?></h1>
			<p><?php esc_html_e( 'Es gratis y toma menos de un minuto.', 'afectivalab' ); ?></p>

			<?php if ( ! empty( $afectivalab_reg['errors'] ) ) : ?>
				<div class="form-alert" role="alert">
					<ul>
						<?php foreach ( $afectivalab_reg['errors'] as $error ) : ?>
							<li><?php echo esc_html( $error ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<form method="post" novalidate>
				<?php wp_nonce_field( 'afectivalab_registro', 'afectivalab_registro_nonce' ); ?>

				<div class="form-field">
					<label for="nombre"><?php esc_html_e( 'Nombre', 'afectivalab' ); ?></label>
					<div class="form-input">
						<span class="form-input__icon"><?php afectivalab_icon( 'user' ); ?></span>
						<input
							type="text"
							id="nombre"
							name="nombre"
							placeholder="<?php esc_attr_e( 'María González', 'afectivalab' ); ?>"
							value="<?php echo esc_attr( $afectivalab_reg['values']['nombre'] ); ?>"
							required
						>
					</div>
				</div>

				<div class="form-field">
					<label for="email"><?php esc_html_e( 'Correo electrónico', 'afectivalab' ); ?></label>
					<div class="form-input">
						<span class="form-input__icon"><?php afectivalab_icon( 'mail' ); ?></span>
						<input
							type="email"
							id="email"
							name="email"
							placeholder="tu@correo.com"
							value="<?php echo esc_attr( $afectivalab_reg['values']['email'] ); ?>"
							required
						>
					</div>
				</div>

				<div class="form-field">
					<label for="password"><?php esc_html_e( 'Contraseña', 'afectivalab' ); ?></label>
					<div class="form-input">
						<span class="form-input__icon"><?php afectivalab_icon( 'lock' ); ?></span>
						<input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8">
						<?php afectivalab_password_toggle( 'password' ); ?>
					</div>
				</div>

				<div class="form-field">
					<label for="password2"><?php esc_html_e( 'Confirmar contraseña', 'afectivalab' ); ?></label>
					<div class="form-input">
						<span class="form-input__icon"><?php afectivalab_icon( 'lock' ); ?></span>
						<input type="password" id="password2" name="password2" placeholder="Repite tu contraseña" required minlength="8">
						<?php afectivalab_password_toggle( 'password2' ); ?>
					</div>
				</div>

				<label class="form-check">
					<input type="checkbox" name="acepta_terminos" value="1" required>
					<span>
						<?php
						printf(
							/* translators: 1: opening link tag for terms, 2: closing link tag, 3: opening link tag for privacy, 4: closing link tag. */
							esc_html__( 'Acepto los %1$sTérminos%2$s y la %3$sPolítica de Privacidad%4$s.', 'afectivalab' ),
							'<a href="' . esc_url( home_url( '/terminos' ) ) . '">',
							'</a>',
							'<a href="' . esc_url( home_url( '/privacidad' ) ) . '">',
							'</a>'
						);
						?>
					</span>
				</label>

				<button type="submit" name="afectivalab_registro_submit" value="1" class="btn btn-primary form-submit">
					<?php esc_html_e( 'Crear mi cuenta', 'afectivalab' ); ?>
					<?php afectivalab_icon( 'arrow-right' ); ?>
				</button>
			</form>

			<p class="auth-switch">
				<?php esc_html_e( '¿Ya tienes cuenta?', 'afectivalab' ); ?>
				<a href="<?php echo esc_url( home_url( '/ingresar' ) ); ?>"><?php esc_html_e( 'Ingresa aquí', 'afectivalab' ); ?></a>
			</p>
		</div>
	</div>
</main>

<?php get_footer(); ?>
