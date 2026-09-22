<?php
/**
 * El formulario de alta y edición de un perfil de hijo.
 *
 * Vive aparte porque page-templates/mis-hijos.php lo coloca en dos sitios
 * distintos: cuando la familia todavía no agregó a nadie va arriba del todo
 * (si no, el formulario queda debajo de la guía y hay que adivinar que hay
 * que bajar), y cuando ya hay perfiles va al final, después de la lista.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_editando = $args['editando'] ?? 0;
$afectivalab_valores  = $args['valores'] ?? array();
$afectivalab_primero  = ! empty( $args['primero'] );
$afectivalab_ejes     = afectivalab_ejes();
$afectivalab_meses    = afectivalab_meses();
?>

			<section class="hijos-form-card reveal" id="formulario">
				<h2>
					<?php
					if ( $afectivalab_editando ) {
						esc_html_e( 'Editar perfil', 'afectivalab' );
					} elseif ( $afectivalab_primero ) {
						esc_html_e( 'Empecemos: cuéntanos de tu hijo o hija', 'afectivalab' );
					} else {
						esc_html_e( 'Agregar un hijo o hija', 'afectivalab' );
					}
					?>
				</h2>

				<?php if ( $afectivalab_primero && ! $afectivalab_editando ) : ?>
					<p class="hijos-form-card__lead">
						<?php esc_html_e( 'Con su nombre y su fecha de nacimiento armamos su ruta. Toma menos de un minuto.', 'afectivalab' ); ?>
					</p>
				<?php endif; ?>

				<form method="post" action="<?php echo esc_url( home_url( '/mis-hijos' ) ); ?>#formulario">
					<?php wp_nonce_field( 'afectivalab_hijo', 'afectivalab_hijo_nonce' ); ?>
					<input type="hidden" name="afectivalab_hijo_id" value="<?php echo esc_attr( $afectivalab_editando ); ?>">

					<div class="form-field">
						<label for="hijo-nombre"><?php esc_html_e( '¿Cómo se llama?', 'afectivalab' ); ?></label>
						<div class="form-input">
							<?php afectivalab_icon( 'user', 'form-input__icon' ); ?>
							<input
								type="text"
								id="hijo-nombre"
								name="afectivalab_hijo_nombre"
								value="<?php echo esc_attr( $afectivalab_valores['nombre'] ); ?>"
								maxlength="60"
								required
							>
						</div>
					</div>

					<fieldset class="form-fieldset">
						<legend><?php esc_html_e( '¿Cuándo nació?', 'afectivalab' ); ?></legend>

						<div class="form-row-split">
							<div class="form-field">
								<label for="hijo-mes"><?php esc_html_e( 'Mes', 'afectivalab' ); ?></label>
								<div class="form-input">
									<select id="hijo-mes" name="afectivalab_hijo_mes" required>
										<option value=""><?php esc_html_e( 'Elige el mes', 'afectivalab' ); ?></option>
										<?php foreach ( $afectivalab_meses as $afectivalab_num => $afectivalab_mes ) : ?>
											<option value="<?php echo esc_attr( $afectivalab_num ); ?>" <?php selected( $afectivalab_valores['mes'], $afectivalab_num ); ?>>
												<?php echo esc_html( $afectivalab_mes ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>

							<div class="form-field">
								<label for="hijo-anio"><?php esc_html_e( 'Año', 'afectivalab' ); ?></label>
								<div class="form-input">
									<select id="hijo-anio" name="afectivalab_hijo_anio" required>
										<option value=""><?php esc_html_e( 'Elige el año', 'afectivalab' ); ?></option>
										<?php for ( $afectivalab_anio = (int) current_time( 'Y' ); $afectivalab_anio >= afectivalab_hijo_anio_minimo(); $afectivalab_anio-- ) : ?>
											<option value="<?php echo esc_attr( $afectivalab_anio ); ?>" <?php selected( $afectivalab_valores['anio'], $afectivalab_anio ); ?>>
												<?php echo esc_html( $afectivalab_anio ); ?>
											</option>
										<?php endfor; ?>
									</select>
								</div>
							</div>
						</div>

						<p class="form-hint">
							<?php esc_html_e( 'No pedimos el día. Con el mes y el año alcanza para ir ajustando su ruta a medida que crece.', 'afectivalab' ); ?>
						</p>
					</fieldset>

					<fieldset class="form-fieldset">
						<legend><?php esc_html_e( '¿Qué te preocupa hoy?', 'afectivalab' ); ?></legend>
						<p class="form-hint">
							<?php esc_html_e( 'Elige los temas que quieres trabajar primero. Puedes cambiarlos cuando quieras, y si no eliges ninguno seguimos el orden recomendado para su edad.', 'afectivalab' ); ?>
						</p>

						<ul class="temas-picker">
							<?php foreach ( $afectivalab_ejes as $afectivalab_slug => $afectivalab_eje ) : ?>
								<li>
									<label class="tema-option">
										<input
											type="checkbox"
											name="afectivalab_hijo_preocupaciones[]"
											value="<?php echo esc_attr( $afectivalab_slug ); ?>"
											<?php checked( in_array( $afectivalab_slug, $afectivalab_valores['preocupaciones'], true ) ); ?>
										>
										<span class="tema-option__icon"><?php afectivalab_icon( 'eje-' . $afectivalab_slug ); ?></span>
										<span class="tema-option__text">
											<span class="tema-option__name"><?php echo esc_html( $afectivalab_eje['nombre'] ); ?></span>
											<span class="tema-option__desc"><?php echo esc_html( $afectivalab_eje['resumen'] ); ?></span>
										</span>
									</label>
								</li>
							<?php endforeach; ?>
						</ul>
					</fieldset>

					<div class="hijos-form-card__actions">
						<button type="submit" name="afectivalab_hijo_guardar" value="1" class="btn btn-primary">
							<?php
							echo $afectivalab_editando
								? esc_html__( 'Guardar cambios', 'afectivalab' )
								: esc_html__( 'Agregar', 'afectivalab' );
							?>
						</button>

						<?php if ( $afectivalab_editando ) : ?>
							<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/mis-hijos' ) ); ?>">
								<?php esc_html_e( 'Cancelar', 'afectivalab' ); ?>
							</a>
						<?php endif; ?>
					</div>
				</form>
			</section>
