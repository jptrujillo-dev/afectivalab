<?php
/**
 * Ruta /mis-hijos — servida vía inc/routes.php (template_include), no es una
 * Página del escritorio. Solo para usuarios logueados.
 *
 * Es también el primer paso después de registrarse: sin al menos un hijo no
 * se puede armar ninguna ruta, así que el registro termina aquí.
 *
 * Tiene dos vistas (pantallas 4 y 6 de la referencia Stitch, ver
 * docs/rediseno-stitch.md): la lista de perfiles con el formulario para
 * agregar uno, y — con ?editar=ID — la edición de un perfil a dos columnas.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/ingresar' ) );
	exit;
}

afectivalab_requiere_suscripcion();

$afectivalab_estado   = afectivalab_handle_hijo_forms();
$afectivalab_hijos    = afectivalab_get_hijos();
$afectivalab_ejes     = afectivalab_ejes();
$afectivalab_valores  = $afectivalab_estado['valores'];
$afectivalab_editando = $afectivalab_estado['editando'];
$afectivalab_hijo_ed  = $afectivalab_editando ? afectivalab_get_hijo_propio( $afectivalab_editando ) : null;
$afectivalab_maximo   = 5; // Mismo límite que afectivalab_handle_hijo_forms().

get_header();
?>

<main class="account-page">
	<div class="container">
		<div class="hijos-page<?php echo $afectivalab_hijo_ed ? ' hijos-page--editar' : ''; ?>">

			<?php if ( $afectivalab_hijo_ed ) : ?>
				<?php $afectivalab_resumen_ed = afectivalab_hijo_resumen( $afectivalab_hijo_ed->ID ); ?>

				<div class="hijos-top">
					<a class="hijos-top__volver" href="<?php echo esc_url( home_url( '/mis-hijos' ) ); ?>">
						<span class="hijos-top__flecha" aria-hidden="true"><?php afectivalab_icon( 'arrow-right' ); ?></span>
						<?php esc_html_e( 'Volver a Mis hijos', 'afectivalab' ); ?>
					</a>
					<nav class="hijos-top__migas" aria-label="<?php esc_attr_e( 'Dónde estás', 'afectivalab' ); ?>">
						<a href="<?php echo esc_url( home_url( '/mis-hijos' ) ); ?>"><?php esc_html_e( 'Mis hijos', 'afectivalab' ); ?></a>
						<span aria-hidden="true">›</span>
						<span><?php esc_html_e( 'Editar perfil', 'afectivalab' ); ?></span>
						<span aria-hidden="true">›</span>
						<strong><?php echo esc_html( $afectivalab_hijo_ed->post_title ); ?></strong>
					</nav>
				</div>

				<header class="hijos-page__head reveal">
					<span class="hijos-page__pill"><?php afectivalab_icon( 'user' ); ?><?php esc_html_e( 'Editar perfil', 'afectivalab' ); ?></span>
					<h1>
						<?php
						printf(
							/* translators: %s: nombre del hijo. */
							esc_html__( 'Editar perfil de %s', 'afectivalab' ),
							esc_html( $afectivalab_hijo_ed->post_title )
						);
						?>
					</h1>
					<p class="hijos-page__lead">
						<?php esc_html_e( 'Actualiza sus datos y los temas que quieres trabajar primero: su ruta se reordena sola.', 'afectivalab' ); ?>
					</p>
				</header>
			<?php else : ?>
				<header class="hijos-page__head hijos-page__head--lista reveal">
					<div>
						<span class="hijos-page__pill"><?php afectivalab_icon( 'user' ); ?><?php esc_html_e( 'Tu familia', 'afectivalab' ); ?></span>
						<h1><?php esc_html_e( 'Mis hijos', 'afectivalab' ); ?></h1>
						<p class="hijos-page__lead">
							<?php esc_html_e( 'Cada hijo tiene su propia ruta, armada según su edad y lo que más te preocupa hoy. Puedes agregar hasta 5 perfiles.', 'afectivalab' ); ?>
						</p>
					</div>
					<?php if ( $afectivalab_hijos && count( $afectivalab_hijos ) < $afectivalab_maximo ) : ?>
						<a class="btn btn-primary hijos-page__agregar" href="#formulario">
							<?php esc_html_e( '+ Agregar hijo o hija', 'afectivalab' ); ?>
						</a>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<?php
			// Los avisos van primero: cuentan lo que la persona acaba de
			// hacer, así que tienen que verse antes que nada.
			?>
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

			<?php if ( $afectivalab_hijo_ed ) : ?>

				<div class="hijo-editar">
					<aside class="hijo-editar__aside">
						<section class="hijo-editar__card hijo-editar__identidad">
							<span class="user-avatar user-avatar--initial hijo-editar__avatar" aria-hidden="true"><?php echo esc_html( mb_strtoupper( mb_substr( trim( $afectivalab_hijo_ed->post_title ), 0, 1 ) ) ); ?></span>
							<h2><?php echo esc_html( $afectivalab_hijo_ed->post_title ); ?></h2>
							<p>
								<?php
								if ( null !== $afectivalab_resumen_ed['edad'] ) {
									printf(
										/* translators: %d: edad en años. */
										esc_html( _n( '%d año', '%d años', $afectivalab_resumen_ed['edad'], 'afectivalab' ) ),
										absint( $afectivalab_resumen_ed['edad'] )
									);
								}
								if ( $afectivalab_resumen_ed['etapa'] ) {
									echo ' · ' . esc_html( $afectivalab_resumen_ed['etapa']->name );
								}
								?>
							</p>
						</section>

						<section class="hijo-editar__card">
							<h3 class="hijo-editar__subtitulo">
								<?php
								printf(
									/* translators: %s: nombre del hijo. */
									esc_html__( 'Logros de %s', 'afectivalab' ),
									esc_html( $afectivalab_hijo_ed->post_title )
								);
								?>
							</h3>
							<ul class="hijo-stats">
								<li class="hijo-stats__item">
									<?php afectivalab_icon( 'star' ); ?>
									<strong><?php echo esc_html( $afectivalab_resumen_ed['estrellas'] ); ?></strong>
									<span><?php esc_html_e( 'Clases terminadas', 'afectivalab' ); ?></span>
								</li>
								<li class="hijo-stats__item">
									<?php afectivalab_icon( 'juego-insignia' ); ?>
									<strong><?php echo esc_html( $afectivalab_resumen_ed['insignias'] ); ?></strong>
									<span><?php echo esc_html( _n( 'Insignia', 'Insignias', $afectivalab_resumen_ed['insignias'], 'afectivalab' ) ); ?></span>
								</li>
							</ul>

							<?php if ( $afectivalab_resumen_ed['actual'] ) : ?>
								<div class="hijo-curso">
									<div class="hijo-curso__fila">
										<span><?php esc_html_e( 'Curso en marcha', 'afectivalab' ); ?></span>
										<strong>
											<?php
											printf(
												/* translators: %d: porcentaje completado. */
												esc_html__( '%d%%', 'afectivalab' ),
												absint( $afectivalab_resumen_ed['actual']['progreso']['porcentaje'] )
											);
											?>
										</strong>
									</div>
									<p class="hijo-curso__titulo"><?php echo esc_html( $afectivalab_resumen_ed['actual']['curso']->post_title ); ?></p>
									<div class="progreso-bar">
										<span class="progreso-bar__fill" style="width: <?php echo esc_attr( $afectivalab_resumen_ed['actual']['progreso']['porcentaje'] ); ?>%"></span>
									</div>
								</div>
							<?php endif; ?>
						</section>
					</aside>

					<div class="hijo-editar__main">
						<?php get_template_part( 'template-parts/hijo-form', null, array( 'editando' => $afectivalab_editando, 'valores' => $afectivalab_valores, 'primero' => false ) ); ?>

						<section class="hijo-baja">
							<div>
								<h3><?php esc_html_e( 'Quitar este perfil', 'afectivalab' ); ?></h3>
								<p>
									<?php
									printf(
										/* translators: %s: nombre del hijo. */
										esc_html__( 'Si %s ya no va a seguir su ruta, puedes quitar su perfil. Su avance no se borra de inmediato: queda en la papelera por si fue un error.', 'afectivalab' ),
										esc_html( $afectivalab_hijo_ed->post_title )
									);
									?>
								</p>
							</div>
							<form method="post" action="<?php echo esc_url( home_url( '/mis-hijos' ) ); ?>">
								<?php wp_nonce_field( 'afectivalab_hijo', 'afectivalab_hijo_nonce' ); ?>
								<button
									type="submit"
									name="afectivalab_hijo_eliminar"
									value="<?php echo esc_attr( $afectivalab_hijo_ed->ID ); ?>"
									class="btn hijo-baja__boton"
									data-confirm="<?php echo esc_attr( sprintf( __( '¿Quitar el perfil de %s?', 'afectivalab' ), $afectivalab_hijo_ed->post_title ) ); ?>"
									data-confirm-boton="<?php esc_attr_e( 'Sí, quitar', 'afectivalab' ); ?>"
								>
									<?php
									printf(
										/* translators: %s: nombre del hijo. */
										esc_html__( 'Quitar el perfil de %s', 'afectivalab' ),
										esc_html( $afectivalab_hijo_ed->post_title )
									);
									?>
								</button>
							</form>
						</section>
					</div>
				</div>

			<?php else : ?>

				<?php
				// Con perfiles ya creados, la guía va aquí. Sin ninguno, va
				// después del formulario: lo primero que tiene que ver quien
				// llega es dónde escribir el nombre de su hijo, no un texto que
				// empuja el formulario fuera de la pantalla.
				?>
				<?php if ( $afectivalab_hijos && afectivalab_mostrar_onboarding() ) : ?>
					<?php get_template_part( 'template-parts/onboarding' ); ?>
				<?php endif; ?>

				<?php if ( $afectivalab_hijos ) : ?>
					<ul class="hijos-list reveal-stagger">
						<?php
						foreach ( $afectivalab_hijos as $afectivalab_hijo ) :
							$afectivalab_resumen = afectivalab_hijo_resumen( $afectivalab_hijo->ID );
							$afectivalab_edad    = $afectivalab_resumen['edad'];
							$afectivalab_etapa   = $afectivalab_resumen['etapa'];
							$afectivalab_preoc   = afectivalab_hijo_preocupaciones( $afectivalab_hijo->ID );
							$afectivalab_inicial = mb_strtoupper( mb_substr( trim( $afectivalab_hijo->post_title ), 0, 1 ) );
							$afectivalab_editar  = add_query_arg( 'editar', $afectivalab_hijo->ID, home_url( '/mis-hijos' ) );
							?>
							<li class="hijo-card">
								<div class="hijo-card__head">
									<span class="user-avatar user-avatar--initial hijo-card__avatar" aria-hidden="true"><?php echo esc_html( $afectivalab_inicial ); ?></span>

									<div class="hijo-card__identidad">
										<div class="hijo-card__nombre-fila">
											<h2 class="hijo-card__name"><?php echo esc_html( $afectivalab_hijo->post_title ); ?></h2>
											<span class="hijo-card__estado<?php echo $afectivalab_etapa ? '' : ' is-espera'; ?>">
												<?php
												$afectivalab_etapa
													? esc_html_e( 'Ruta activa', 'afectivalab' )
													: esc_html_e( 'En espera', 'afectivalab' );
												?>
											</span>
										</div>

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
									</div>
								</div>

								<?php if ( ! $afectivalab_etapa ) : ?>
									<p class="hijo-card__aviso">
										<?php esc_html_e( 'Nuestro contenido empieza a los 3 años. Te avisaremos apenas haya una ruta para su edad.', 'afectivalab' ); ?>
									</p>
								<?php elseif ( $afectivalab_resumen['actual'] ) : ?>
									<div class="hijo-curso">
										<div class="hijo-curso__fila">
											<span><?php echo esc_html( $afectivalab_resumen['actual']['curso']->post_title ); ?></span>
											<strong>
												<?php
												printf(
													/* translators: %d: porcentaje completado. */
													esc_html__( '%d%% completado', 'afectivalab' ),
													absint( $afectivalab_resumen['actual']['progreso']['porcentaje'] )
												);
												?>
											</strong>
										</div>
										<div class="progreso-bar">
											<span class="progreso-bar__fill" style="width: <?php echo esc_attr( $afectivalab_resumen['actual']['progreso']['porcentaje'] ); ?>%"></span>
										</div>
									</div>
								<?php elseif ( $afectivalab_resumen['cursos'] ) : ?>
									<p class="hijo-card__aviso hijo-card__aviso--logro">
										<?php esc_html_e( '¡Terminó todos los cursos de su etapa!', 'afectivalab' ); ?>
									</p>
								<?php endif; ?>

								<span class="hijo-card__rotulo"><?php esc_html_e( 'Temas que te preocupan', 'afectivalab' ); ?></span>
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

								<ul class="hijo-stats">
									<li class="hijo-stats__item">
										<?php afectivalab_icon( 'star' ); ?>
										<strong><?php echo esc_html( $afectivalab_resumen['estrellas'] ); ?></strong>
										<span><?php esc_html_e( 'Clases terminadas', 'afectivalab' ); ?></span>
									</li>
									<li class="hijo-stats__item">
										<?php afectivalab_icon( 'juego-insignia' ); ?>
										<strong><?php echo esc_html( $afectivalab_resumen['insignias'] ); ?></strong>
										<span><?php echo esc_html( _n( 'Insignia', 'Insignias', $afectivalab_resumen['insignias'], 'afectivalab' ) ); ?></span>
									</li>
									<li class="hijo-stats__item">
										<?php afectivalab_icon( 'juego-mapa-desbloqueable' ); ?>
										<strong><?php echo esc_html( $afectivalab_resumen['cursos'] ); ?></strong>
										<span><?php echo esc_html( _n( 'Curso en su ruta', 'Cursos en su ruta', $afectivalab_resumen['cursos'], 'afectivalab' ) ); ?></span>
									</li>
								</ul>

								<div class="hijo-card__actions">
									<?php if ( $afectivalab_etapa ) : ?>
										<a class="btn btn-primary" href="<?php echo esc_url( add_query_arg( 'hijo', $afectivalab_hijo->ID, home_url( '/panel' ) ) ); ?>">
											<?php esc_html_e( 'Ver su ruta', 'afectivalab' ); ?>
											<?php afectivalab_icon( 'arrow-right' ); ?>
										</a>
									<?php endif; ?>

									<a class="btn btn-secondary hijo-card__edit" href="<?php echo esc_url( $afectivalab_editar ); ?>">
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
											data-confirm-boton="<?php esc_attr_e( 'Sí, quitar', 'afectivalab' ); ?>"
										>
											<?php esc_html_e( 'Quitar', 'afectivalab' ); ?>
										</button>
									</form>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>

					<?php if ( count( $afectivalab_hijos ) < $afectivalab_maximo ) : ?>
						<?php get_template_part( 'template-parts/hijo-form', null, array( 'editando' => 0, 'valores' => $afectivalab_valores, 'primero' => false ) ); ?>
					<?php else : ?>
						<p class="hijos-maximo">
							<?php esc_html_e( 'Ya tienes 5 perfiles, el máximo por cuenta. Si necesitas cambiar uno, edítalo o quítalo.', 'afectivalab' ); ?>
						</p>
					<?php endif; ?>

				<?php else : ?>

					<?php // Sin ningún perfil todavía: el formulario es lo primero, y la guía queda debajo. ?>
					<?php get_template_part( 'template-parts/hijo-form', null, array( 'editando' => 0, 'valores' => $afectivalab_valores, 'primero' => true ) ); ?>

					<?php if ( afectivalab_mostrar_onboarding() ) : ?>
						<?php get_template_part( 'template-parts/onboarding' ); ?>
					<?php endif; ?>

				<?php endif; ?>

			<?php endif; ?>

		</div>
	</div>
</main>

<?php get_footer(); ?>
