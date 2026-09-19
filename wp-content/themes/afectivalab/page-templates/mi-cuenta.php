<?php
/**
 * Ruta /mi-cuenta — servida vía inc/routes.php (template_include), no es
 * una Página del escritorio. Solo para usuarios logueados.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/ingresar' ) );
	exit;
}

$afectivalab_current_user = wp_get_current_user();
$afectivalab_avatar       = afectivalab_handle_avatar_upload();

get_header();
?>

<main class="account-page">
	<div class="container">
		<div class="account-card reveal">
			<h1><?php esc_html_e( 'Mi cuenta', 'afectivalab' ); ?></h1>
			<p class="account-card__lead"><?php esc_html_e( 'Así te van a ver en Afectivalab.', 'afectivalab' ); ?></p>

			<?php if ( $afectivalab_avatar['success'] ) : ?>
				<div class="form-alert form-alert--success">
					<?php esc_html_e( 'Tu foto de perfil se actualizó.', 'afectivalab' ); ?>
				</div>
			<?php elseif ( ! empty( $afectivalab_avatar['errors'] ) ) : ?>
				<div class="form-alert" role="alert">
					<ul>
						<?php foreach ( $afectivalab_avatar['errors'] as $error ) : ?>
							<li><?php echo esc_html( $error ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<form method="post" enctype="multipart/form-data" data-avatar-form>
				<?php wp_nonce_field( 'afectivalab_avatar', 'afectivalab_avatar_nonce' ); ?>

				<div class="avatar-picker">
					<div class="avatar-picker__preview" data-avatar-preview>
						<?php echo afectivalab_get_avatar_html( $afectivalab_current_user, 96 ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado dentro del helper. ?>
					</div>

					<label class="avatar-picker__button">
						<?php afectivalab_icon( 'camera' ); ?>
						<?php esc_html_e( 'Cambiar foto', 'afectivalab' ); ?>
						<input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" data-avatar-input hidden>
					</label>
					<p class="avatar-picker__hint"><?php esc_html_e( 'JPG, PNG o WEBP. Máximo 3MB.', 'afectivalab' ); ?></p>
				</div>

				<button type="submit" name="afectivalab_avatar_submit" value="1" class="btn btn-primary form-submit" data-avatar-submit>
					<?php esc_html_e( 'Guardar foto', 'afectivalab' ); ?>
				</button>
			</form>

			<div class="account-card__info">
				<div class="form-field">
					<span class="account-card__label"><?php esc_html_e( 'Nombre', 'afectivalab' ); ?></span>
					<span><?php echo esc_html( $afectivalab_current_user->display_name ); ?></span>
				</div>
				<div class="form-field">
					<span class="account-card__label"><?php esc_html_e( 'Correo electrónico', 'afectivalab' ); ?></span>
					<span><?php echo esc_html( $afectivalab_current_user->user_email ); ?></span>
				</div>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>
