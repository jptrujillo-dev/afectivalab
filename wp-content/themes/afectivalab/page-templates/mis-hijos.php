<?php
/**
 * Ruta /mis-hijos — servida vía inc/routes.php (template_include), no es una
 * Página del escritorio. Solo para usuarios logueados.
 *
 * Es también el primer paso después de registrarse: sin al menos un hijo no
 * se puede armar ninguna ruta, así que el registro termina aquí.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/ingresar' ) );
	exit;
}

$afectivalab_estado = afectivalab_handle_hijo_forms();
$afectivalab_hijos  = afectivalab_get_hijos();
$afectivalab_ejes   = afectivalab_ejes();
$afectivalab_meses  = afectivalab_meses();
$afectivalab_valores = $afectivalab_estado['valores'];
$afectivalab_editando = $afectivalab_estado['editando'];

get_header();
?>

<main class="account-page">
	<div class="container">
		<div class="hijos-page">

			<header class="hijos-page__head reveal">
				<h1><?php esc_html_e( 'Mis hijos', 'afectivalab' ); ?></h1>
				<p class="hijos-page__lead">
					<?php esc_html_e( 'Cada hijo tiene su propia ruta, armada según su edad y lo que más te preocupa hoy. Puedes agregar todos los que quieras.', 'afectivalab' ); ?>
				</p>
			</header>

			<?php if ( $afectivalab_estado['notice'] ) : ?>
				<div class="form-alert form-alert--success">
					<?php echo esc_html( $afectivalab_estado['notice'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $afectivalab_estado['errors'] ) ) : ?>
				<div class="form-alert" role="alert">
					<ul>
						<?php foreach ( $afectivalab_estado['errors'] as $afectivalab_error ) : ?>
							<li><?php echo esc_html( $afectivalab_error ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( $afectivalab_hijos ) : ?>
				<ul class="hijos-list reveal-stagger">
					<?php
					foreach ( $afectivalab_hijos as $afectivalab_hijo ) :
						$afectivalab_edad  = afectivalab_hijo_edad( $afectivalab_hijo->ID );
						$afectivalab_etapa = afectivalab_hijo_etapa( $afectivalab_hijo->ID );
						$afectivalab_preoc = afectivalab_hijo_preocupaciones( $afectivalab_hijo->ID );
						$afectivalab_inicial = mb_strtoupper( mb_substr( trim( $afectivalab_hijo->post_title ), 0, 1 ) );
						?>
						<li class="hijo-card">
							<span class="user-avatar user-avatar--initial hijo-card__avatar" aria-hidden="true"><?php echo esc_html( $afectivalab_inicial ); ?></span>

							<div class="hijo-card__body">
								<h2 class="hijo-card__name"><?php echo esc_html( $afectivalab_hijo->post_title ); ?></h2>

								<p class="hijo-card__meta">
									<?php
									if ( null !== $afectivalab_edad ) {
										printf(
											/* translators: %d: edad en años. */
											esc_html( _n( '%d año', '%d años', $afectivalab_edad, 'afectivalab' ) ),
											absint( $afectivalab_edad )
										);
									}

									if ( $afectivalab_etapa ) {
										echo ' <span class="hijo-card__sep" aria-hidden="true">&middot;</span> ';
										echo esc_html( $afectivalab_etapa->name );
									}
									?>
								</p>

								<?php if ( ! $afectivalab_etapa ) : ?>
									<p class="hijo-card__aviso">
										<?php esc_html_e( 'Nuestro contenido empieza a los 3 años. Te avisaremos apenas haya una ruta para su edad.', 'afectivalab' ); ?>
									</p>
								<?php endif; ?>

								<?php if ( $afectivalab_preoc ) : ?>
									<ul class="hijo-card__temas">
										<?php foreach ( $afectivalab_preoc as $afectivalab_slug ) : ?>
											<?php if ( isset( $afectivalab_ejes[ $afectivalab_slug ] ) ) : ?>
												<li class="hijo-card__tema">
													<?php afectivalab_icon( 'eje-' . $afectivalab_slug ); ?>
													<?php echo esc_html( $afectivalab_ejes[ $afectivalab_slug ]['nombre'] ); ?>
												</li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul>
								<?php else : ?>
									<p class="hijo-card__aviso">
										<?php esc_html_e( 'Sin temas marcados todavía: su ruta va a seguir el orden recomendado para su edad.', 'afectivalab' ); ?>
									</p>
								<?php endif; ?>
							</div>

							<div class="hijo-card__actions">
								<a class="hijo-card__edit" href="<?php echo esc_url( add_query_arg( 'editar', $afectivalab_hijo->ID, home_url( '/mis-hijos' ) ) . '#formulario' ); ?>">
									<?php esc_html_e( 'Editar', 'afectivalab' ); ?>
								</a>

								<form method="post" class="hijo-card__delete">
									<?php wp_nonce_field( 'afectivalab_hijo', 'afectivalab_hijo_nonce' ); ?>
									<button
										type="submit"
										name="afectivalab_hijo_eliminar"
										value="<?php echo esc_attr( $afectivalab_hijo->ID ); ?>"
										class="hijo-card__delete-button"
										data-confirm="<?php echo esc_attr( sprintf( __( '¿Quitar el perfil de %s?', 'afectivalab' ), $afectivalab_hijo->post_title ) ); ?>"
									>
										<?php esc_html_e( 'Quitar', 'afectivalab' ); ?>
									</button>
								</form>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<div class="hijos-empty reveal">
					<?php afectivalab_icon( 'paso-elige-edad', 'hijos-empty__icon' ); ?>
					<h2><?php esc_html_e( 'Todavía no agregaste a nadie', 'afectivalab' ); ?></h2>
					<p><?php esc_html_e( 'Agrega a tu primer hijo o hija para que podamos armar su ruta.', 'afectivalab' ); ?></p>
				</div>
			<?php endif; ?>

			<section class="hijos-form-card reveal" id="formulario">
				<h2>
					<?php
					echo $afectivalab_editando
						? esc_html__( 'Editar perfil', 'afectivalab' )
						: esc_html__( 'Agregar un hijo o hija', 'afectivalab' );
					?>
				</h2>

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

		</div>
	</div>
</main>

<?php get_footer(); ?>
