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

$afectivalab_current_user     = wp_get_current_user();
$afectivalab_avatar           = afectivalab_handle_avatar_upload();
$afectivalab_perfil_msg       = afectivalab_handle_perfil_form();
$afectivalab_suscripcion_msg  = afectivalab_handle_suscripcion_cancelar();
$afectivalab_preferencias_msg = afectivalab_handle_preferencias_form();

// El formulario de perfil pudo haber cambiado el nombre/correo recién —
// releer el usuario para no mostrar los valores viejos más abajo.
if ( $afectivalab_perfil_msg['success'] ) {
	$afectivalab_current_user = wp_get_current_user();
}

get_header();
?>

<main class="account-page">
	<div class="container">
		<div class="account-hero reveal">
			<h1><?php esc_html_e( 'Mi cuenta', 'afectivalab' ); ?></h1>
			<p><?php esc_html_e( 'Tus datos, tu suscripción y el progreso real de tu familia en Afectivalab.', 'afectivalab' ); ?></p>
		</div>

		<div class="account-layout">
			<div class="account-layout__principal">
				<div class="account-card reveal">
					<h2><?php esc_html_e( 'Mi perfil', 'afectivalab' ); ?></h2>
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

					<div class="form-alert" role="alert" data-avatar-error-cliente hidden></div>
					<div class="form-alert form-alert--success" role="status" data-avatar-exito hidden></div>

					<form method="post" enctype="multipart/form-data" data-avatar-form>
						<?php wp_nonce_field( 'afectivalab_avatar', 'afectivalab_avatar_nonce' ); ?>

						<div class="avatar-picker">
							<div class="avatar-picker__aro">
								<div class="avatar-picker__preview" data-avatar-preview>
									<?php echo afectivalab_get_avatar_html( $afectivalab_current_user, 96 ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado dentro del helper. ?>
								</div>
							</div>

							<div class="avatar-picker__acciones">
								<label class="avatar-picker__button">
									<?php afectivalab_icon( 'camera' ); ?>
									<?php esc_html_e( 'Cambiar foto', 'afectivalab' ); ?>
									<input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" data-avatar-input hidden>
								</label>
								<button type="submit" name="afectivalab_avatar_submit" value="1" class="btn btn-primary avatar-picker__guardar" data-avatar-submit>
									<?php afectivalab_icon( 'check' ); ?>
									<?php esc_html_e( 'Guardar', 'afectivalab' ); ?>
								</button>
							</div>
							<p class="avatar-picker__hint"><?php esc_html_e( 'JPG, PNG o WEBP. Máximo 1MB.', 'afectivalab' ); ?></p>
						</div>
					</form>

					<?php if ( $afectivalab_perfil_msg['success'] ) : ?>
						<div class="form-alert form-alert--success">
							<?php esc_html_e( 'Tus datos se actualizaron.', 'afectivalab' ); ?>
						</div>
					<?php elseif ( ! empty( $afectivalab_perfil_msg['errors'] ) ) : ?>
						<div class="form-alert" role="alert">
							<ul>
								<?php foreach ( $afectivalab_perfil_msg['errors'] as $afectivalab_error_perfil ) : ?>
									<li><?php echo esc_html( $afectivalab_error_perfil ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>

					<form method="post" class="account-form-perfil">
						<?php wp_nonce_field( 'afectivalab_perfil', 'afectivalab_perfil_nonce' ); ?>

						<div class="form-field">
							<label for="afectivalab-perfil-nombre"><?php esc_html_e( 'Nombre', 'afectivalab' ); ?></label>
							<div class="form-input">
								<span class="form-input__icon"><?php afectivalab_icon( 'user' ); ?></span>
								<input type="text" id="afectivalab-perfil-nombre" name="nombre" value="<?php echo esc_attr( $afectivalab_current_user->display_name ); ?>" required>
							</div>
						</div>
						<div class="form-field">
							<label for="afectivalab-perfil-correo"><?php esc_html_e( 'Correo electrónico', 'afectivalab' ); ?></label>
							<div class="form-input">
								<span class="form-input__icon"><?php afectivalab_icon( 'mail' ); ?></span>
								<input type="email" id="afectivalab-perfil-correo" name="correo" value="<?php echo esc_attr( $afectivalab_current_user->user_email ); ?>" required>
							</div>
						</div>

						<button type="submit" name="afectivalab_perfil_submit" value="1" class="btn btn-secondary form-submit">
							<?php esc_html_e( 'Guardar datos', 'afectivalab' ); ?>
						</button>
					</form>

					<div class="account-card__info">
						<div class="form-field">
							<span class="account-card__label"><?php esc_html_e( 'Contraseña', 'afectivalab' ); ?></span>
							<button type="button" class="account-cambiar-contrasena" data-password-abrir>
								<?php esc_html_e( 'Cambiar contraseña', 'afectivalab' ); ?>
							</button>
						</div>
						<div class="form-field">
							<span class="account-card__label"><?php esc_html_e( 'Rol en Afectivalab', 'afectivalab' ); ?></span>
							<span><?php echo esc_html( afectivalab_es_del_equipo() ? __( 'Equipo Afectivalab', 'afectivalab' ) : __( 'Padre/Madre', 'afectivalab' ) ); ?></span>
						</div>
					</div>
				</div>

				<?php // El equipo de contenido no tiene relación de cobro — no son clientes. ?>
				<?php if ( ! afectivalab_es_del_equipo() ) : ?>
					<?php $afectivalab_suscripcion = afectivalab_suscripcion_resumen( $afectivalab_current_user->ID ); ?>

					<div class="account-card reveal">
						<div class="account-suscripcion__titulo">
							<span class="account-suscripcion__icono"><?php afectivalab_icon( 'check' ); ?></span>
							<div>
								<h2><?php esc_html_e( 'Tu suscripción', 'afectivalab' ); ?></h2>
								<p class="account-suscripcion__estado is-<?php echo esc_attr( $afectivalab_suscripcion['status'] ); ?>">
									<?php echo esc_html( $afectivalab_suscripcion['label'] ); ?>
								</p>
							</div>
						</div>

						<?php if ( $afectivalab_suscripcion_msg['notice'] ) : ?>
							<div class="form-alert form-alert--success">
								<?php echo esc_html( $afectivalab_suscripcion_msg['notice'] ); ?>
							</div>
						<?php elseif ( ! empty( $afectivalab_suscripcion_msg['errors'] ) ) : ?>
							<div class="form-alert" role="alert">
								<ul>
									<?php foreach ( $afectivalab_suscripcion_msg['errors'] as $afectivalab_error ) : ?>
										<li><?php echo esc_html( $afectivalab_error ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<?php if ( $afectivalab_suscripcion['nivel_nombre'] ) : ?>
							<div class="account-suscripcion__plan">
								<span class="account-suscripcion__plan-nombre"><?php echo esc_html( $afectivalab_suscripcion['nivel_nombre'] ); ?></span>
								<?php if ( $afectivalab_suscripcion['nivel_precio'] ) : ?>
									<span class="account-suscripcion__plan-precio">
										<?php
										echo esc_html(
											$afectivalab_suscripcion['nivel_periodo']
												/* translators: 1: precio, 2: período de cobro (ej. "Mes"). */
												? sprintf( __( '%1$s / %2$s', 'afectivalab' ), $afectivalab_suscripcion['nivel_precio'], $afectivalab_suscripcion['nivel_periodo'] )
												: $afectivalab_suscripcion['nivel_precio']
										);
										?>
									</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( $afectivalab_suscripcion['proximo_cobro'] ) : ?>
							<p class="account-suscripcion__tarjeta">
								<?php
								printf(
									/* translators: %s: fecha del próximo cobro (o hasta cuándo sigue el acceso, si ya canceló). */
									esc_html__( '%s: %s', 'afectivalab' ),
									$afectivalab_suscripcion['puede_cancelar']
										? esc_html__( 'Próximo cobro', 'afectivalab' )
										: esc_html__( 'Tienes acceso hasta', 'afectivalab' ),
									esc_html( date_i18n( 'j \d\e F \d\e Y', $afectivalab_suscripcion['proximo_cobro'] ) )
								);
								?>
							</p>
						<?php endif; ?>

						<?php if ( $afectivalab_suscripcion['activa'] ) : ?>
							<?php
							// Mismo texto real que ya usa /suscribirse — no se inventa
							// contenido nuevo, se repite lo que el plan de verdad incluye.
							?>
							<ul class="account-suscripcion__incluye">
								<li><?php afectivalab_icon( 'check' ); ?><?php esc_html_e( 'Hasta 5 perfiles de hijo', 'afectivalab' ); ?></li>
								<li><?php afectivalab_icon( 'check' ); ?><?php esc_html_e( 'Ruta personalizada por edad e intereses', 'afectivalab' ); ?></li>
								<li><?php afectivalab_icon( 'check' ); ?><?php esc_html_e( 'Misiones, casos prácticos y certificados', 'afectivalab' ); ?></li>
							</ul>
						<?php endif; ?>

						<?php if ( $afectivalab_suscripcion['puede_cancelar'] ) : ?>
							<form method="post">
								<?php wp_nonce_field( 'afectivalab_suscripcion_cancelar', 'afectivalab_suscripcion_nonce' ); ?>
								<button
									type="submit"
									name="afectivalab_suscripcion_cancelar"
									value="1"
									class="btn btn-secondary"
									data-confirm="<?php esc_attr_e( '¿Cancelar la renovación automática? Sigues teniendo acceso hasta el final del período que ya pagaste, pero no te vamos a volver a cobrar.', 'afectivalab' ); ?>"
								>
									<?php esc_html_e( 'Cancelar renovación automática', 'afectivalab' ); ?>
								</button>
							</form>
						<?php elseif ( 'ninguna' === $afectivalab_suscripcion['status'] ) : ?>
							<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/suscribirse' ) ); ?>">
								<?php esc_html_e( 'Suscribirme', 'afectivalab' ); ?>
								<?php afectivalab_icon( 'arrow-right' ); ?>
							</a>
						<?php endif; ?>
					</div>

					<?php $afectivalab_pedidos = afectivalab_suscripcion_pedidos( $afectivalab_current_user->ID ); ?>
					<?php if ( ! empty( $afectivalab_pedidos ) ) : ?>
						<div class="account-card reveal" id="pedidos">
							<h2><?php esc_html_e( 'Tus pedidos', 'afectivalab' ); ?></h2>

							<ul class="account-pedidos<?php echo count( $afectivalab_pedidos ) > 5 ? ' account-pedidos--scroll' : ''; ?>">
								<?php foreach ( $afectivalab_pedidos as $afectivalab_pedido ) : ?>
									<li class="account-pedidos__item">
										<div class="account-pedidos__info">
											<span class="account-pedidos__nivel"><?php echo esc_html( $afectivalab_pedido['nivel'] ); ?></span>
											<span class="account-pedidos__fecha"><?php echo esc_html( date_i18n( 'j \d\e F \d\e Y', $afectivalab_pedido['fecha'] ) ); ?></span>
										</div>
										<span class="account-pedidos__total"><?php echo esc_html( $afectivalab_pedido['total'] ); ?></span>
										<span class="account-pedidos__estado is-<?php echo esc_attr( $afectivalab_pedido['estado_clase'] ); ?>"><?php echo esc_html( $afectivalab_pedido['estado'] ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>

			<?php if ( ! afectivalab_es_del_equipo() ) : ?>
				<?php $afectivalab_impacto = afectivalab_padre_resumen_impacto( $afectivalab_current_user->ID ); ?>
				<div class="account-layout__secundaria">
					<div class="account-card reveal">
						<h2><?php esc_html_e( 'Impacto en familia', 'afectivalab' ); ?></h2>

						<div class="account-stats">
							<div class="account-stats__item">
								<span class="account-stats__icono"><?php afectivalab_icon( 'juego-monedas' ); ?></span>
								<span class="account-stats__valor"><?php echo esc_html( $afectivalab_impacto['monedas'] ); ?></span>
								<span class="account-stats__label"><?php esc_html_e( 'Monedas', 'afectivalab' ); ?></span>
							</div>
							<div class="account-stats__item">
								<span class="account-stats__icono"><?php afectivalab_icon( 'juego-experiencia' ); ?></span>
								<span class="account-stats__valor"><?php echo esc_html( $afectivalab_impacto['xp'] ); ?></span>
								<span class="account-stats__label"><?php esc_html_e( 'Experiencia', 'afectivalab' ); ?></span>
							</div>
							<div class="account-stats__item">
								<span class="account-stats__icono"><?php afectivalab_icon( 'leccion-video-principal' ); ?></span>
								<span class="account-stats__valor"><?php echo esc_html( $afectivalab_impacto['microclases'] ); ?></span>
								<span class="account-stats__label"><?php esc_html_e( 'Microclases', 'afectivalab' ); ?></span>
							</div>
							<div class="account-stats__item">
								<span class="account-stats__icono"><?php afectivalab_icon( 'user' ); ?></span>
								<span class="account-stats__valor"><?php echo esc_html( count( $afectivalab_impacto['hijos'] ) ); ?></span>
								<span class="account-stats__label"><?php esc_html_e( 'Hijos en ruta', 'afectivalab' ); ?></span>
							</div>
						</div>

						<?php if ( ! empty( $afectivalab_impacto['hijos'] ) ) : ?>
							<div class="account-hijos-vinculados">
								<span class="account-hijos-vinculados__label"><?php esc_html_e( 'Rutas vinculadas:', 'afectivalab' ); ?></span>
								<?php foreach ( $afectivalab_impacto['hijos'] as $afectivalab_hijo_item ) : ?>
									<span class="account-hijos-vinculados__chip">
										<?php echo esc_html( $afectivalab_hijo_item['nombre'] ); ?>
										<?php if ( null !== $afectivalab_hijo_item['edad'] ) : ?>
											(<?php echo esc_html( $afectivalab_hijo_item['edad'] ); ?> <?php esc_html_e( 'años', 'afectivalab' ); ?>)
										<?php endif; ?>
									</span>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<p class="account-card__vacio">
								<?php esc_html_e( 'Todavía no agregaste ningún hijo.', 'afectivalab' ); ?>
								<a href="<?php echo esc_url( home_url( '/mis-hijos' ) ); ?>"><?php esc_html_e( 'Agregar ahora', 'afectivalab' ); ?></a>
							</p>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $afectivalab_impacto['insignias'] ) ) : ?>
						<div class="account-card reveal">
							<div class="account-insignias__titulo">
								<div>
									<h2><?php esc_html_e( 'Insignias desbloqueadas', 'afectivalab' ); ?></h2>
									<p class="account-card__lead"><?php esc_html_e( 'Habilidades que tus hijos ya practicaron con vos.', 'afectivalab' ); ?></p>
								</div>
								<?php if ( count( $afectivalab_impacto['insignias'] ) > 3 ) : ?>
									<div class="account-insignias__flechas">
										<button type="button" class="account-insignias__flecha account-insignias__flecha--prev" data-insignias-prev aria-label="<?php esc_attr_e( 'Ver insignias anteriores', 'afectivalab' ); ?>">
											<?php afectivalab_icon( 'chevron-down' ); ?>
										</button>
										<button type="button" class="account-insignias__flecha account-insignias__flecha--next" data-insignias-next aria-label="<?php esc_attr_e( 'Ver más insignias', 'afectivalab' ); ?>">
											<?php afectivalab_icon( 'chevron-down' ); ?>
										</button>
									</div>
								<?php endif; ?>
							</div>

							<ul class="account-insignias" data-insignias-lista>
								<?php foreach ( $afectivalab_impacto['insignias'] as $afectivalab_insignia ) : ?>
									<li class="account-insignias__item">
										<span class="account-insignias__icono"><?php afectivalab_icon( 'insignia' ); ?></span>
										<span class="account-insignias__nombre"><?php echo esc_html( $afectivalab_insignia['nombre'] ); ?></span>
										<span class="account-insignias__hijo"><?php echo esc_html( $afectivalab_insignia['hijo'] ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>

					<?php $afectivalab_preferencias = afectivalab_preferencias_notificacion( $afectivalab_current_user->ID ); ?>
					<div class="account-card reveal">
						<h2><?php esc_html_e( 'Preferencias de notificación', 'afectivalab' ); ?></h2>
						<p class="account-card__lead"><?php esc_html_e( 'Elige qué avisos querés recibir. Por ahora solo guardamos tu elección — el envío real todavía no está activo.', 'afectivalab' ); ?></p>

						<?php if ( $afectivalab_preferencias_msg['success'] ) : ?>
							<div class="form-alert form-alert--success">
								<?php esc_html_e( 'Tus preferencias se guardaron.', 'afectivalab' ); ?>
							</div>
						<?php endif; ?>

						<form method="post">
							<?php wp_nonce_field( 'afectivalab_preferencias', 'afectivalab_preferencias_nonce' ); ?>

							<label class="account-preferencia">
								<span>
									<strong><?php esc_html_e( 'Nuevas misiones y contenido', 'afectivalab' ); ?></strong>
									<small><?php esc_html_e( 'Cuando se publique una microclase o misión nueva para la edad de tus hijos.', 'afectivalab' ); ?></small>
								</span>
								<input type="checkbox" name="pref_nuevas_misiones" value="1" <?php checked( $afectivalab_preferencias['nuevas_misiones'] ); ?>>
							</label>

							<label class="account-preferencia">
								<span>
									<strong><?php esc_html_e( 'Resumen de progreso', 'afectivalab' ); ?></strong>
									<small><?php esc_html_e( 'Un repaso de las microclases y misiones que fueron completando.', 'afectivalab' ); ?></small>
								</span>
								<input type="checkbox" name="pref_resumen_progreso" value="1" <?php checked( $afectivalab_preferencias['resumen_progreso'] ); ?>>
							</label>

							<button type="submit" name="afectivalab_preferencias_submit" value="1" class="btn btn-secondary form-submit">
								<?php esc_html_e( 'Guardar preferencias', 'afectivalab' ); ?>
							</button>
						</form>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</main>

<div class="account-password-modal" data-password-modal hidden>
	<div class="account-password-modal__caja" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Cambiar contraseña', 'afectivalab' ); ?>">
		<button type="button" class="account-password-modal__cerrar" data-password-cerrar aria-label="<?php esc_attr_e( 'Cerrar', 'afectivalab' ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 5L19 19M19 5L5 19"/></svg>
		</button>

		<h2><?php esc_html_e( 'Cambiar contraseña', 'afectivalab' ); ?></h2>

		<div class="form-alert" data-password-error hidden></div>
		<div class="form-alert form-alert--success" data-password-exito hidden></div>

		<form data-password-form>
			<?php wp_nonce_field( 'afectivalab_cambiar_password', 'afectivalab_password_nonce' ); ?>

			<div class="form-field">
				<label for="afectivalab-password-actual"><?php esc_html_e( 'Contraseña actual', 'afectivalab' ); ?></label>
				<div class="form-input">
					<input type="password" id="afectivalab-password-actual" name="password_actual" autocomplete="current-password" required>
					<?php afectivalab_password_toggle( 'afectivalab-password-actual' ); ?>
				</div>
			</div>
			<div class="form-field" data-password-campo-nueva>
				<label for="afectivalab-password-nueva"><?php esc_html_e( 'Contraseña nueva', 'afectivalab' ); ?></label>
				<div class="form-input">
					<input type="password" id="afectivalab-password-nueva" name="password_nueva" autocomplete="new-password" minlength="8" required>
					<?php afectivalab_password_toggle( 'afectivalab-password-nueva' ); ?>
				</div>
				<span class="form-field__hint" data-password-hint-nueva></span>
				<?php afectivalab_password_strength_meter( 'afectivalab-password-nueva' ); ?>
			</div>
			<div class="form-field" data-password-campo-repetir>
				<label for="afectivalab-password-nueva-2"><?php esc_html_e( 'Repetir contraseña nueva', 'afectivalab' ); ?></label>
				<div class="form-input">
					<input type="password" id="afectivalab-password-nueva-2" name="password_nueva_2" autocomplete="new-password" minlength="8" required>
					<?php afectivalab_password_toggle( 'afectivalab-password-nueva-2' ); ?>
				</div>
				<span class="form-field__hint" data-password-hint-repetir></span>
			</div>

			<button type="submit" class="btn btn-primary form-submit" data-password-submit>
				<?php esc_html_e( 'Actualizar contraseña', 'afectivalab' ); ?>
			</button>
		</form>
	</div>
</div>

<?php get_footer(); ?>
