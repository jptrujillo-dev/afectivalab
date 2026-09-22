<?php
/**
 * Ruta /panel — la pantalla a la que llega el padre al entrar.
 *
 * Según el concepto, no es un catálogo: es un saludo, el hijo del que estamos
 * hablando, qué le toca esta semana y cómo va. El catálogo completo queda
 * para la exploración libre por mundos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/ingresar' ) );
	exit;
}

// Quien produce el contenido no es una familia: mostrarle "agrega a tu hijo"
// no tiene sentido. Ve su propio panel, salvo que pida expresamente la vista
// de familia (útil para probar la plataforma como la usa el padre).
$afectivalab_equipo = afectivalab_es_del_equipo()
	&& ( ! isset( $_GET['vista'] ) || 'familia' !== $_GET['vista'] );

// Las acciones del panel (guardar, eliminar, cambiar rol) se resuelven antes
// de imprimir nada: si salen bien redirigen, y así recargar no las repite.
$afectivalab_estado = $afectivalab_equipo
	? afectivalab_panel_handle()
	: array( 'errors' => array(), 'valores' => array() );

$afectivalab_user  = wp_get_current_user();
$afectivalab_hijos = afectivalab_get_hijos();
$afectivalab_hijo  = afectivalab_hijo_activo();

$afectivalab_edad  = $afectivalab_hijo ? afectivalab_hijo_edad( $afectivalab_hijo->ID ) : null;
$afectivalab_etapa = $afectivalab_hijo ? afectivalab_hijo_etapa( $afectivalab_hijo->ID ) : null;
$afectivalab_ruta  = $afectivalab_hijo ? afectivalab_ruta_del_hijo( $afectivalab_hijo->ID ) : array();
$afectivalab_actual = $afectivalab_hijo ? afectivalab_curso_actual( $afectivalab_hijo->ID ) : null;
$afectivalab_habilidades = $afectivalab_hijo ? afectivalab_habilidades_del_hijo( $afectivalab_hijo->ID ) : array();

get_header();
?>

<main class="panel-page">
	<div class="container">

		<?php if ( $afectivalab_equipo ) : ?>

			<?php get_template_part( 'template-parts/panel-equipo', null, array( 'estado' => $afectivalab_estado ) ); ?>

		<?php else : ?>

		<header class="panel-saludo reveal">
			<div class="panel-saludo__texto">
				<h1>
					<?php
					printf(
						/* translators: 1: saludo según la hora, 2: nombre del padre. */
						esc_html__( '%1$s, %2$s', 'afectivalab' ),
						esc_html( afectivalab_saludo() ),
						esc_html( $afectivalab_user->display_name )
					);
					?>
				</h1>

				<?php if ( $afectivalab_hijo && null !== $afectivalab_edad ) : ?>
					<p class="panel-saludo__lead">
						<?php
						printf(
							/* translators: 1: nombre del hijo, 2: edad en años. */
							esc_html__( 'Esta semana con %1$s, de %2$d años.', 'afectivalab' ),
							esc_html( $afectivalab_hijo->post_title ),
							absint( $afectivalab_edad )
						);
						?>
					</p>
				<?php endif; ?>
			</div>

			<?php // Las monedas son de la cuenta, no del hijo: se ganan resolviendo misiones (ver inc/misiones.php). ?>
			<span class="monedas-chip">
				<?php afectivalab_icon( 'juego-monedas' ); ?>
				<?php echo esc_html( afectivalab_padre_monedas( $afectivalab_user->ID ) ); ?>
			</span>
		</header>

		<?php if ( afectivalab_mostrar_onboarding() ) : ?>
			<?php get_template_part( 'template-parts/onboarding' ); ?>
		<?php endif; ?>

		<?php if ( ! $afectivalab_hijos ) : ?>

			<div class="panel-vacio reveal">
				<?php afectivalab_icon( 'juego-mapa-desbloqueable', 'panel-vacio__icon' ); ?>
				<h2><?php esc_html_e( 'Empecemos por conocer a tu hijo', 'afectivalab' ); ?></h2>
				<p><?php esc_html_e( 'Con su edad y lo que más te preocupa armamos su ruta de acompañamiento.', 'afectivalab' ); ?></p>
				<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/mis-hijos' ) ); ?>">
					<?php esc_html_e( 'Agregar a mi hijo', 'afectivalab' ); ?>
					<?php afectivalab_icon( 'arrow-right' ); ?>
				</a>
			</div>

		<?php else : ?>

			<?php if ( count( $afectivalab_hijos ) > 1 ) : ?>
				<nav class="hijo-switch reveal" aria-label="<?php esc_attr_e( 'Elegir hijo', 'afectivalab' ); ?>">
					<?php foreach ( $afectivalab_hijos as $afectivalab_uno ) : ?>
						<?php $afectivalab_activo = $afectivalab_hijo && $afectivalab_uno->ID === $afectivalab_hijo->ID; ?>
						<a
							class="hijo-switch__item <?php echo $afectivalab_activo ? 'is-activo' : ''; ?>"
							href="<?php echo esc_url( add_query_arg( 'hijo', $afectivalab_uno->ID, home_url( '/panel' ) ) ); ?>"
							<?php echo $afectivalab_activo ? 'aria-current="true"' : ''; ?>
						>
							<span class="user-avatar user-avatar--initial hijo-switch__avatar" aria-hidden="true">
								<?php echo esc_html( mb_strtoupper( mb_substr( trim( $afectivalab_uno->post_title ), 0, 1 ) ) ); ?>
							</span>
							<?php echo esc_html( $afectivalab_uno->post_title ); ?>
						</a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>

			<?php if ( ! $afectivalab_etapa ) : ?>

				<div class="panel-vacio reveal">
					<?php afectivalab_icon( 'juego-racha-semanal', 'panel-vacio__icon' ); ?>
					<h2><?php esc_html_e( 'Todavía falta un poquito', 'afectivalab' ); ?></h2>
					<p>
						<?php
						printf(
							/* translators: %s: nombre del hijo. */
							esc_html__( 'Nuestro acompañamiento empieza a los 3 años. Te avisaremos apenas haya una ruta para %s.', 'afectivalab' ),
							esc_html( $afectivalab_hijo->post_title )
						);
						?>
					</p>
				</div>

			<?php elseif ( ! $afectivalab_ruta ) : ?>

				<div class="panel-vacio reveal">
					<?php afectivalab_icon( 'juego-mapa-desbloqueable', 'panel-vacio__icon' ); ?>
					<h2><?php esc_html_e( 'Estamos preparando su ruta', 'afectivalab' ); ?></h2>
					<p>
						<?php
						printf(
							/* translators: %s: nombre de la etapa. */
							esc_html__( 'Los cursos para la etapa "%s" están en camino. Te avisaremos apenas el primero esté listo.', 'afectivalab' ),
							esc_html( $afectivalab_etapa->name )
						);
						?>
					</p>
				</div>

			<?php else : ?>

				<?php if ( $afectivalab_actual ) : ?>
					<?php
					$afectivalab_curso   = $afectivalab_actual['curso'];
					$afectivalab_eje     = $afectivalab_actual['eje'];
					$afectivalab_avance  = $afectivalab_actual['progreso'];
					$afectivalab_empezado = $afectivalab_avance['hechas'] > 0;
					?>
					<section class="panel-destacado reveal">
						<span class="panel-destacado__etiqueta">
							<?php
							$afectivalab_empezado
								? esc_html_e( 'Continúa donde lo dejaron', 'afectivalab' )
								: esc_html_e( 'Te recomendamos empezar por aquí', 'afectivalab' );
							?>
						</span>

						<div class="panel-destacado__cuerpo">
							<?php if ( $afectivalab_eje ) : ?>
								<span class="panel-destacado__icono">
									<?php afectivalab_icon( 'eje-' . $afectivalab_eje->slug ); ?>
								</span>
							<?php endif; ?>

							<div class="panel-destacado__texto">
								<h2><?php echo esc_html( $afectivalab_curso->post_title ); ?></h2>

								<?php if ( $afectivalab_actual['prioritario'] ) : ?>
									<p class="panel-destacado__razon">
										<?php
										printf(
											/* translators: 1: nombre del hijo, 2: nombre del eje temático. */
											esc_html__( 'Porque marcaste "%2$s" como uno de los temas que más te preocupan con %1$s.', 'afectivalab' ),
											esc_html( $afectivalab_hijo->post_title ),
											esc_html( $afectivalab_eje ? $afectivalab_eje->name : '' )
										);
										?>
									</p>
								<?php elseif ( $afectivalab_etapa ) : ?>
									<p class="panel-destacado__razon">
										<?php
										printf(
											/* translators: 1: nombre del hijo, 2: nombre de la etapa. */
											esc_html__( 'Porque %1$s está en la etapa de %2$s, donde este tema suele aparecer.', 'afectivalab' ),
											esc_html( $afectivalab_hijo->post_title ),
											esc_html( mb_strtolower( $afectivalab_etapa->name ) )
										);
										?>
									</p>
								<?php endif; ?>

								<?php if ( $afectivalab_avance['total'] ) : ?>
									<div class="progreso-bar">
										<span class="progreso-bar__fill" style="width: <?php echo esc_attr( $afectivalab_avance['porcentaje'] ); ?>%"></span>
									</div>
									<span class="panel-destacado__avance">
										<?php
										printf(
											/* translators: 1: clases hechas, 2: total de clases. */
											esc_html__( '%1$d de %2$d clases', 'afectivalab' ),
											absint( $afectivalab_avance['hechas'] ),
											absint( $afectivalab_avance['total'] )
										);
										?>
									</span>
								<?php endif; ?>

								<a class="btn btn-primary" href="<?php echo esc_url( get_permalink( $afectivalab_curso ) ); ?>">
									<?php
									$afectivalab_empezado
										? esc_html_e( 'Continuar', 'afectivalab' )
										: esc_html_e( 'Empezar curso', 'afectivalab' );
									?>
									<?php afectivalab_icon( 'arrow-right' ); ?>
								</a>
							</div>
						</div>
					</section>
				<?php else : ?>
					<section class="panel-destacado panel-destacado--completo reveal">
						<?php afectivalab_icon( 'juego-certificado', 'panel-destacado__icono-grande' ); ?>
						<h2><?php esc_html_e( '¡Completaron toda la ruta!', 'afectivalab' ); ?></h2>
						<p>
							<?php
							printf(
								/* translators: 1: nombre del hijo, 2: nombre de la etapa. */
								esc_html__( 'Terminaron todos los cursos disponibles para %1$s en la etapa de %2$s. Te avisaremos cuando agreguemos más.', 'afectivalab' ),
								esc_html( $afectivalab_hijo->post_title ),
								esc_html( mb_strtolower( $afectivalab_etapa->name ) )
							);
							?>
						</p>
					</section>
				<?php endif; ?>

				<section class="panel-ruta">
					<h2 class="panel-seccion__titulo">
						<?php
						printf(
							/* translators: %s: nombre del hijo. */
							esc_html__( 'La ruta de %s', 'afectivalab' ),
							esc_html( $afectivalab_hijo->post_title )
						);
						?>
					</h2>

					<ul class="ruta-cursos reveal-stagger">
						<?php foreach ( $afectivalab_ruta as $afectivalab_paso ) : ?>
							<?php
							$afectivalab_p     = $afectivalab_paso['progreso'];
							$afectivalab_estado = $afectivalab_p['completo'] ? 'hecho' : ( $afectivalab_p['hechas'] ? 'empezado' : 'pendiente' );
							?>
							<li class="ruta-curso is-<?php echo esc_attr( $afectivalab_estado ); ?>">
								<a href="<?php echo esc_url( get_permalink( $afectivalab_paso['curso'] ) ); ?>">
									<span class="ruta-curso__icono">
										<?php
										if ( $afectivalab_p['completo'] ) {
											afectivalab_icon( 'check' );
										} elseif ( $afectivalab_paso['eje'] ) {
											afectivalab_icon( 'eje-' . $afectivalab_paso['eje']->slug );
										} else {
											afectivalab_icon( 'star' );
										}
										?>
									</span>

									<span class="ruta-curso__texto">
										<strong><?php echo esc_html( $afectivalab_paso['curso']->post_title ); ?></strong>
										<span class="ruta-curso__meta">
											<?php if ( $afectivalab_p['total'] ) : ?>
												<?php
												printf(
													/* translators: 1: clases hechas, 2: total de clases. */
													esc_html__( '%1$d de %2$d clases', 'afectivalab' ),
													absint( $afectivalab_p['hechas'] ),
													absint( $afectivalab_p['total'] )
												);
												?>
											<?php else : ?>
												<?php esc_html_e( 'En preparación', 'afectivalab' ); ?>
											<?php endif; ?>
										</span>
									</span>

									<?php if ( $afectivalab_paso['prioritario'] ) : ?>
										<span class="ruta-curso__chip"><?php esc_html_e( 'Te preocupa', 'afectivalab' ); ?></span>
									<?php endif; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>

				<?php if ( $afectivalab_habilidades ) : ?>
					<section class="panel-habilidades">
						<h2 class="panel-seccion__titulo"><?php esc_html_e( 'Habilidades adquiridas', 'afectivalab' ); ?></h2>

						<ul class="habilidades-list reveal-stagger">
							<?php foreach ( $afectivalab_habilidades as $afectivalab_h ) : ?>
								<li class="habilidad-card">
									<?php afectivalab_icon( 'juego-insignia', 'habilidad-card__icon' ); ?>
									<strong><?php echo esc_html( $afectivalab_h['habilidad'] ); ?></strong>
									<span><?php echo esc_html( $afectivalab_h['curso']->post_title ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

			<?php endif; ?>

		<?php endif; ?>

			<?php if ( afectivalab_es_del_equipo() ) : ?>
				<p class="equipo-cambio">
					<a href="<?php echo esc_url( home_url( '/panel' ) ); ?>">
						<?php esc_html_e( 'Volver a mi panel de contenido', 'afectivalab' ); ?>
					</a>
				</p>
			<?php endif; ?>

		<?php endif; ?>

	</div>
</main>

<?php get_footer(); ?>
