<?php
/**
 * Ruta /ingresar — servida vía inc/routes.php (template_include), no es
 * una Página del escritorio.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/' ) );
	exit;
}

$afectivalab_login = afectivalab_handle_login();

get_header();
?>

<main class="auth-page">
	<div class="auth-visual reveal">
		<?php afectivalab_icon( 'doodle-leaf', 'hero__doodle hero__doodle--leaf-2' ); ?>
		<?php afectivalab_icon( 'doodle-heart', 'hero__doodle hero__doodle--heart' ); ?>
		<img
			src="<?php echo esc_url( get_theme_file_uri( 'assets/images/hero-familia.webp' ) ); ?>"
			alt="<?php esc_attr_e( 'Familia sonriendo', 'afectivalab' ); ?>"
			width="500"
			height="500"
		>
		<h2><?php esc_html_e( 'Qué bueno tenerte de vuelta', 'afectivalab' ); ?></h2>
		<p><?php esc_html_e( 'Sigue justo donde te quedaste con tu ruta y tus misiones.', 'afectivalab' ); ?></p>
	</div>

	<div class="auth-form-wrap reveal">
		<div class="auth-form-card">
			<a class="auth-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
				<?php afectivalab_icon( 'logo-color' ); ?>
			</a>

			<h1><?php esc_html_e( 'Ingresa a tu cuenta', 'afectivalab' ); ?></h1>
			<p><?php esc_html_e( 'Seguimos aprendiendo juntos.', 'afectivalab' ); ?></p>

			<?php if ( isset( $_GET['reset'] ) && 'ok' === $_GET['reset'] ) : ?>
				<div class="form-alert form-alert--success">
					<?php esc_html_e( 'Tu contraseña se actualizó. Ya puedes ingresar con la nueva.', 'afectivalab' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $afectivalab_login['errors'] ) ) : ?>
				<div class="form-alert" role="alert">
					<ul>
						<?php foreach ( $afectivalab_login['errors'] as $error ) : ?>
							<li><?php echo esc_html( $error ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<form method="post" novalidate data-validate>
				<?php wp_nonce_field( 'afectivalab_login', 'afectivalab_login_nonce' ); ?>

				<div class="form-field">
					<label for="email"><?php esc_html_e( 'Correo electrónico', 'afectivalab' ); ?></label>
					<div class="form-input">
						<span class="form-input__icon"><?php afectivalab_icon( 'mail' ); ?></span>
						<input
							type="email"
							id="email"
							name="email"
							placeholder="tu@correo.com"
							value="<?php echo esc_attr( $afectivalab_login['values']['email'] ); ?>"
							required
							data-validate-field="email"
						>
					</div>
					<span class="form-field__hint"></span>
				</div>

				<div class="form-field">
					<label for="password"><?php esc_html_e( 'Contraseña', 'afectivalab' ); ?></label>
					<div class="form-input">
						<span class="form-input__icon"><?php afectivalab_icon( 'lock' ); ?></span>
						<input type="password" id="password" name="password" placeholder="••••••••" required>
						<?php afectivalab_password_toggle( 'password' ); ?>
					</div>
				</div>

				<div class="form-row-between">
					<label class="form-check" style="margin-bottom: 0;">
						<input type="checkbox" name="recordarme" value="1">
						<span><?php esc_html_e( 'Recordarme', 'afectivalab' ); ?></span>
					</label>
					<a href="<?php echo esc_url( home_url( '/recuperar' ) ); ?>"><?php esc_html_e( '¿Olvidaste tu contraseña?', 'afectivalab' ); ?></a>
				</div>

				<button type="submit" name="afectivalab_login_submit" value="1" class="btn btn-primary form-submit">
					<?php esc_html_e( 'Ingresar', 'afectivalab' ); ?>
					<?php afectivalab_icon( 'arrow-right' ); ?>
				</button>
			</form>

			<p class="auth-switch">
				<?php esc_html_e( '¿No tienes cuenta?', 'afectivalab' ); ?>
				<a href="<?php echo esc_url( home_url( '/registro' ) ); ?>"><?php esc_html_e( 'Regístrate gratis', 'afectivalab' ); ?></a>
			</p>
		</div>
	</div>
</main>

<?php get_footer(); ?>
