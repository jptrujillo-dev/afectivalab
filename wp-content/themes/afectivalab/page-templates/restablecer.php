<?php
/**
 * Ruta /restablecer — el link del correo de /recuperar llega acá con
 * ?login=...&key=.... Servida vía inc/routes.php (template_include), no es
 * una Página del escritorio.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/' ) );
	exit;
}

$afectivalab_reset_user = afectivalab_get_reset_password_user();
$afectivalab_reset      = $afectivalab_reset_user ? afectivalab_handle_reset_password( $afectivalab_reset_user ) : array( 'errors' => array() );

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
		<h2><?php esc_html_e( 'Ya casi, un último paso', 'afectivalab' ); ?></h2>
		<p><?php esc_html_e( 'Elige una contraseña nueva y sigues justo donde ibas.', 'afectivalab' ); ?></p>
	</div>

	<div class="auth-form-wrap reveal">
		<div class="auth-form-card">
			<a class="auth-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
				<?php afectivalab_icon( 'logo-color' ); ?>
			</a>

			<?php if ( ! $afectivalab_reset_user ) : ?>

				<h1><?php esc_html_e( 'Este link ya no es válido', 'afectivalab' ); ?></h1>
				<p><?php esc_html_e( 'Puede que ya lo hayas usado o que haya vencido. Pide uno nuevo.', 'afectivalab' ); ?></p>

				<a class="btn btn-primary form-submit" href="<?php echo esc_url( home_url( '/recuperar' ) ); ?>">
					<?php esc_html_e( 'Pedir un nuevo link', 'afectivalab' ); ?>
					<?php afectivalab_icon( 'arrow-right' ); ?>
				</a>

			<?php else : ?>

				<h1><?php esc_html_e( 'Elige tu nueva contraseña', 'afectivalab' ); ?></h1>
				<p><?php esc_html_e( 'Que sea algo que no uses en otro lado.', 'afectivalab' ); ?></p>

				<?php if ( ! empty( $afectivalab_reset['errors'] ) ) : ?>
					<div class="form-alert" role="alert">
						<ul>
							<?php foreach ( $afectivalab_reset['errors'] as $error ) : ?>
								<li><?php echo esc_html( $error ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<form method="post" novalidate data-validate>
					<?php wp_nonce_field( 'afectivalab_restablecer', 'afectivalab_restablecer_nonce' ); ?>

					<div class="form-field">
						<label for="password"><?php esc_html_e( 'Nueva contraseña', 'afectivalab' ); ?></label>
						<div class="form-input">
							<span class="form-input__icon"><?php afectivalab_icon( 'lock' ); ?></span>
							<input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8" data-validate-field="password">
							<?php afectivalab_password_toggle( 'password' ); ?>
						</div>
						<span class="form-field__hint"></span>
						<?php afectivalab_password_strength_meter( 'password' ); ?>
					</div>

					<div class="form-field">
						<label for="password2"><?php esc_html_e( 'Confirmar contraseña', 'afectivalab' ); ?></label>
						<div class="form-input">
							<span class="form-input__icon"><?php afectivalab_icon( 'lock' ); ?></span>
							<input type="password" id="password2" name="password2" placeholder="Repite tu contraseña" required minlength="8" data-validate-field="password-match" data-match="password">
							<?php afectivalab_password_toggle( 'password2' ); ?>
						</div>
						<span class="form-field__hint"></span>
					</div>

					<button type="submit" name="afectivalab_restablecer_submit" value="1" class="btn btn-primary form-submit">
						<?php esc_html_e( 'Guardar nueva contraseña', 'afectivalab' ); ?>
						<?php afectivalab_icon( 'arrow-right' ); ?>
					</button>
				</form>

			<?php endif; ?>
		</div>
	</div>
</main>

<?php get_footer(); ?>
