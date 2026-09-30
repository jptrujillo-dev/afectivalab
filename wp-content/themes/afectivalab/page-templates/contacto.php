<?php
/**
 * Ruta /contacto — servida vía inc/routes.php (template_include). A
 * diferencia del resto del sitio, no pide sesión: cualquier visitante puede
 * escribir. Ver inc/contacto.php para el envío y la defensa contra spam.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_contacto = afectivalab_handle_contacto_form();

get_header();
?>

<main class="legal-page">
	<div class="container legal-page__narrow">

		<header class="legal-hero reveal">
			<h1><?php esc_html_e( 'Escríbenos', 'afectivalab' ); ?></h1>
			<p><?php esc_html_e( '¿Tienes una pregunta sobre Afectivalab, tu cuenta o el contenido? Cuéntanos y te respondemos por correo.', 'afectivalab' ); ?></p>
		</header>

		<div class="legal-card reveal">

			<?php if ( $afectivalab_contacto['enviado'] ) : ?>

				<div class="form-alert form-alert--success">
					<?php esc_html_e( 'Recibimos tu mensaje. Te respondemos a la brevedad por correo.', 'afectivalab' ); ?>
				</div>

			<?php else : ?>

				<?php if ( ! empty( $afectivalab_contacto['errors'] ) ) : ?>
					<div class="form-alert" role="alert">
						<ul>
							<?php foreach ( $afectivalab_contacto['errors'] as $afectivalab_error ) : ?>
								<li><?php echo esc_html( $afectivalab_error ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<form method="post" novalidate data-validate>
					<?php wp_nonce_field( 'afectivalab_contacto', 'afectivalab_contacto_nonce' ); ?>

					<p class="legal-form__trampa" aria-hidden="true">
						<label for="afectivalab_contacto_empresa">Empresa</label>
						<input type="text" id="afectivalab_contacto_empresa" name="afectivalab_contacto_empresa" tabindex="-1" autocomplete="off">
					</p>

					<div class="form-field">
						<label for="nombre"><?php esc_html_e( 'Tu nombre', 'afectivalab' ); ?></label>
						<div class="form-input">
							<input
								type="text"
								id="nombre"
								name="nombre"
								value="<?php echo esc_attr( $afectivalab_contacto['valores']['nombre'] ); ?>"
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
								value="<?php echo esc_attr( $afectivalab_contacto['valores']['email'] ); ?>"
								required
								data-validate-field="email"
							>
						</div>
					</div>

					<div class="form-field">
						<label for="mensaje"><?php esc_html_e( 'Mensaje', 'afectivalab' ); ?></label>
						<div class="form-input">
							<textarea id="mensaje" name="mensaje" rows="5" required><?php echo esc_textarea( $afectivalab_contacto['valores']['mensaje'] ); ?></textarea>
						</div>
					</div>

					<button type="submit" name="afectivalab_contacto_submit" value="1" class="btn btn-primary form-submit">
						<?php esc_html_e( 'Enviar mensaje', 'afectivalab' ); ?>
						<?php afectivalab_icon( 'arrow-right' ); ?>
					</button>
				</form>

			<?php endif; ?>

		</div>

	</div>
</main>

<?php get_footer(); ?>
