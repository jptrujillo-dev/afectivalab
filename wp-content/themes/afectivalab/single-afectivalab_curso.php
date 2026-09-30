<?php
/**
 * Un curso: su ruta de microclases como camino, no como lista.
 *
 * Si no hay sesión, el curso igual se ve (sirve de vitrina), pero el camino
 * aparece cerrado con una invitación a crear la cuenta.
 *
 * Diseño: pantalla 2 de la referencia Stitch (docs/rediseno-stitch.md). De
 * esa referencia solo se toma la forma; todo lo que muestra sale de datos
 * reales del curso y del hijo (nada de racha, estrellas ni cofres).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

the_post();

$afectivalab_curso_id = get_the_ID();
$afectivalab_eje      = afectivalab_eje_del_curso( $afectivalab_curso_id );
$afectivalab_etapas   = get_the_terms( $afectivalab_curso_id, AFECTIVALAB_TAX_ETAPA );
$afectivalab_etapa    = ( $afectivalab_etapas && ! is_wp_error( $afectivalab_etapas ) ) ? reset( $afectivalab_etapas ) : null;
$afectivalab_habilidad = get_post_meta( $afectivalab_curso_id, '_afectivalab_habilidad', true );

// Con sesión pero sin suscripción activa (y sin ser del equipo), se trata
// igual que a un visitante anónimo: el camino se ve todo bloqueado, sin
// progreso — el curso sigue siendo vitrina pública, no se redirige (ver
// single-afectivalab_clase.php, que sí corta al entrar a una clase real).
$afectivalab_puede_ver_progreso = is_user_logged_in()
	&& ( afectivalab_es_del_equipo() || afectivalab_usuario_tiene_suscripcion_activa() );

$afectivalab_hijo     = $afectivalab_puede_ver_progreso ? afectivalab_hijo_activo() : null;
$afectivalab_progreso = $afectivalab_hijo ? afectivalab_progreso_curso( $afectivalab_hijo->ID, $afectivalab_curso_id ) : null;
$afectivalab_completo = $afectivalab_progreso && $afectivalab_progreso['completo'];

// Sin hijo activo no hay progreso que mostrar, pero el camino se dibuja igual
// con todas las clases en gris para que se vea de qué trata el curso.
$afectivalab_nodos = $afectivalab_hijo
	? afectivalab_ruta_del_curso( $afectivalab_hijo->ID, $afectivalab_curso_id )
	: array_map(
		function ( $clase, $indice ) {
			return array(
				'clase'  => $clase,
				'estado' => 'bloqueada',
				'numero' => $indice + 1,
			);
		},
		afectivalab_clases_del_curso( $afectivalab_curso_id ),
		array_keys( afectivalab_clases_del_curso( $afectivalab_curso_id ) )
	);

$afectivalab_total_clases = count( $afectivalab_nodos );
$afectivalab_edad_hijo    = $afectivalab_hijo ? afectivalab_hijo_edad( $afectivalab_hijo->ID ) : null;

get_header();
?>

<main class="curso-page">
	<div class="container">

		<div class="curso-top">
			<a class="curso-top__volver" href="<?php echo esc_url( home_url( is_user_logged_in() ? '/panel' : '/' ) ); ?>">
				<span class="curso-top__flecha" aria-hidden="true"><?php afectivalab_icon( 'arrow-right' ); ?></span>
				<?php
				is_user_logged_in()
					? esc_html_e( 'Volver a mi ruta', 'afectivalab' )
					: esc_html_e( 'Volver al inicio', 'afectivalab' );
				?>
			</a>

			<?php if ( $afectivalab_hijo ) : ?>
				<span class="curso-top__hijo">
					<span class="curso-top__inicial" aria-hidden="true"><?php echo esc_html( mb_strtoupper( mb_substr( $afectivalab_hijo->post_title, 0, 1 ) ) ); ?></span>
					<span>
						<?php esc_html_e( 'Viendo la ruta de', 'afectivalab' ); ?>
						<strong><?php echo esc_html( $afectivalab_hijo->post_title ); ?></strong>
						<?php if ( null !== $afectivalab_edad_hijo ) : ?>
							·
							<?php
							printf(
								/* translators: %d: edad en años. */
								esc_html( _n( '%d año', '%d años', $afectivalab_edad_hijo, 'afectivalab' ) ),
								absint( $afectivalab_edad_hijo )
							);
							?>
						<?php endif; ?>
					</span>
				</span>
			<?php endif; ?>
		</div>

		<header class="curso-hero">
			<div class="curso-hero__texto">
				<ul class="curso-hero__chips">
					<?php if ( $afectivalab_eje ) : ?>
						<li class="curso-chip curso-chip--eje">
							<?php afectivalab_icon( 'eje-' . $afectivalab_eje->slug ); ?>
							<?php echo esc_html( $afectivalab_eje->name ); ?>
						</li>
					<?php endif; ?>
					<?php if ( $afectivalab_total_clases ) : ?>
						<li class="curso-chip">
							<?php afectivalab_icon( 'leccion-video-principal' ); ?>
							<?php
							printf(
								/* translators: %d: cantidad de microclases. */
								esc_html( _n( '%d microclase', '%d microclases', $afectivalab_total_clases, 'afectivalab' ) ),
								absint( $afectivalab_total_clases )
							);
							?>
						</li>
					<?php endif; ?>
					<?php if ( $afectivalab_habilidad ) : ?>
						<li class="curso-chip">
							<?php afectivalab_icon( 'juego-insignia' ); ?>
							<?php echo esc_html( $afectivalab_habilidad ); ?>
						</li>
					<?php endif; ?>
				</ul>

				<h1 class="curso-hero__title"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="curso-hero__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( $afectivalab_progreso && $afectivalab_progreso['total'] ) : ?>
				<div class="curso-hero__progreso">
					<div class="curso-hero__progreso-fila">
						<p class="curso-hero__progreso-texto">
							<?php
							printf(
								/* translators: 1: nombre del hijo, 2: clases hechas, 3: total. */
								esc_html__( '%1$s va %2$d de %3$d microclases', 'afectivalab' ),
								esc_html( $afectivalab_hijo->post_title ),
								absint( $afectivalab_progreso['hechas'] ),
								absint( $afectivalab_progreso['total'] )
							);
							?>
						</p>
						<strong class="curso-hero__porcentaje">
							<?php
							printf(
								/* translators: %d: porcentaje completado. */
								esc_html__( '%d%% completado', 'afectivalab' ),
								absint( $afectivalab_progreso['porcentaje'] )
							);
							?>
						</strong>
					</div>
					<div class="progreso-bar">
						<span class="progreso-bar__fill" style="width: <?php echo esc_attr( $afectivalab_progreso['porcentaje'] ); ?>%"></span>
					</div>
				</div>
			<?php endif; ?>
		</header>

		<?php if ( ! $afectivalab_nodos ) : ?>
			<div class="curso-vacio">
				<?php afectivalab_icon( 'juego-mapa-desbloqueable', 'curso-vacio__icon' ); ?>
				<h2><?php esc_html_e( 'Este curso todavía se está preparando', 'afectivalab' ); ?></h2>
				<p><?php esc_html_e( 'Sus microclases aparecerán aquí apenas estén listas.', 'afectivalab' ); ?></p>
			</div>
		<?php else : ?>

			<?php if ( ! $afectivalab_hijo ) : ?>
				<div class="curso-invitacion">
					<?php afectivalab_icon( 'juego-racha-semanal', 'curso-invitacion__icon' ); ?>
					<?php if ( is_user_logged_in() && ! $afectivalab_puede_ver_progreso ) : ?>
						<div>
							<strong><?php esc_html_e( 'Reactiva tu suscripción', 'afectivalab' ); ?></strong>
							<p><?php esc_html_e( 'Tu progreso y el de tus hijos sigue guardado tal como lo dejaron.', 'afectivalab' ); ?></p>
						</div>
						<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/suscribirse' ) ); ?>">
							<?php esc_html_e( 'Reactivar', 'afectivalab' ); ?>
							<?php afectivalab_icon( 'arrow-right' ); ?>
						</a>
					<?php else : ?>
						<div>
							<strong><?php esc_html_e( 'Crea tu cuenta para empezar', 'afectivalab' ); ?></strong>
							<p>
								<?php
								is_user_logged_in()
									? esc_html_e( 'Agrega el perfil de tu hijo o hija y armamos su ruta.', 'afectivalab' )
									: esc_html_e( 'Guardamos por dónde vas y te vamos proponiendo el siguiente paso.', 'afectivalab' );
								?>
							</p>
						</div>
						<a class="btn btn-primary" href="<?php echo esc_url( home_url( is_user_logged_in() ? '/mis-hijos' : '/registro' ) ); ?>">
							<?php
							is_user_logged_in()
								? esc_html_e( 'Agregar hijo', 'afectivalab' )
								: esc_html_e( 'Empieza gratis', 'afectivalab' );
							?>
							<?php afectivalab_icon( 'arrow-right' ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $afectivalab_completo ) : ?>
				<div class="curso-logro">
					<?php afectivalab_icon( 'juego-insignia', 'curso-logro__icon' ); ?>
					<h2><?php esc_html_e( '¡Curso completo!', 'afectivalab' ); ?></h2>
					<p>
						<?php
						printf(
							/* translators: 1: nombre del hijo, 2: habilidad adquirida. */
							esc_html__( 'Terminaste el acompañamiento de %1$s en %2$s.', 'afectivalab' ),
							esc_html( $afectivalab_hijo->post_title ),
							esc_html( $afectivalab_habilidad ? $afectivalab_habilidad : get_the_title() )
						);
						?>
					</p>
					<div class="curso-logro__acciones">
						<a class="btn btn-primary" href="<?php echo esc_url( afectivalab_certificado_url( $afectivalab_hijo->ID, $afectivalab_curso_id ) ); ?>">
							<?php afectivalab_icon( 'juego-certificado' ); ?>
							<?php esc_html_e( 'Descargar certificado', 'afectivalab' ); ?>
						</a>
						<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/panel' ) ); ?>">
							<?php esc_html_e( 'Ver mi avance', 'afectivalab' ); ?>
							<?php afectivalab_icon( 'arrow-right' ); ?>
						</a>
					</div>
				</div>
			<?php endif; ?>

		<?php endif; ?>

		<div class="curso-layout<?php echo $afectivalab_nodos ? '' : ' curso-layout--solo'; ?>">

			<?php if ( $afectivalab_nodos ) : ?>
				<section class="curso-mapa" aria-labelledby="curso-mapa-titulo">
					<h2 class="curso-mapa__pill" id="curso-mapa-titulo">
						<?php afectivalab_icon( 'juego-mapa-desbloqueable' ); ?>
						<?php
						echo esc_html(
							$afectivalab_etapa
								/* translators: %s: nombre de la etapa. */
								? sprintf( __( 'Mapa de aprendizaje · %s', 'afectivalab' ), $afectivalab_etapa->name )
								: __( 'Mapa de aprendizaje', 'afectivalab' )
						);
						?>
					</h2>

					<ol class="camino" role="list">
						<?php foreach ( $afectivalab_nodos as $afectivalab_nodo ) : ?>
							<?php
							$afectivalab_clase    = $afectivalab_nodo['clase'];
							$afectivalab_estado   = $afectivalab_nodo['estado'];
							$afectivalab_duracion = (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_duracion', true );
							$afectivalab_resumen  = trim( (string) $afectivalab_clase->post_excerpt );
							$afectivalab_mision   = afectivalab_clase_mision( $afectivalab_clase->ID );
							$afectivalab_url      = get_permalink( $afectivalab_clase );

							$afectivalab_marca = array(
								'hecha'     => 'check',
								'actual'    => 'star',
								'bloqueada' => 'lock',
							)[ $afectivalab_estado ];

							$afectivalab_titulo = sprintf(
								/* translators: 1: número de la microclase, 2: título. */
								__( 'Clase %1$d: %2$s', 'afectivalab' ),
								absint( $afectivalab_nodo['numero'] ),
								$afectivalab_clase->post_title
							);
							?>
							<li class="camino__paso is-<?php echo esc_attr( $afectivalab_estado ); ?>">
								<?php if ( 'bloqueada' === $afectivalab_estado ) : ?>
									<span class="camino__nodo" aria-hidden="true">
										<?php afectivalab_icon( $afectivalab_marca, 'camino__marca' ); ?>
									</span>
								<?php else : ?>
									<a class="camino__nodo" href="<?php echo esc_url( $afectivalab_url ); ?>" tabindex="-1" aria-hidden="true">
										<?php if ( 'actual' === $afectivalab_estado ) : ?>
											<span class="camino__ahora"><?php esc_html_e( '¡Ahora!', 'afectivalab' ); ?></span>
										<?php endif; ?>
										<?php afectivalab_icon( $afectivalab_marca, 'camino__marca' ); ?>
									</a>
								<?php endif; ?>

								<div class="camino__info">
									<div class="camino__cabecera">
										<?php if ( 'actual' === $afectivalab_estado ) : ?>
											<span class="camino__turno"><?php esc_html_e( '¡Es tu turno!', 'afectivalab' ); ?></span>
										<?php else : ?>
											<span class="camino__estado">
												<?php
												'hecha' === $afectivalab_estado
													? esc_html_e( 'Completada', 'afectivalab' )
													: esc_html_e( 'Bloqueada', 'afectivalab' );
												?>
											</span>
										<?php endif; ?>

										<?php if ( $afectivalab_duracion ) : ?>
											<span class="camino__meta">
												<?php
												printf(
													/* translators: %d: duración en minutos. */
													esc_html__( '%d min', 'afectivalab' ),
													absint( $afectivalab_duracion )
												);
												?>
											</span>
										<?php endif; ?>
									</div>

									<strong class="camino__titulo">
										<?php if ( 'hecha' === $afectivalab_estado ) : ?>
											<a href="<?php echo esc_url( $afectivalab_url ); ?>"><?php echo esc_html( $afectivalab_titulo ); ?></a>
										<?php else : ?>
											<?php echo esc_html( $afectivalab_titulo ); ?>
										<?php endif; ?>
									</strong>

									<?php if ( $afectivalab_resumen ) : ?>
										<p class="camino__resumen"><?php echo esc_html( $afectivalab_resumen ); ?></p>
									<?php endif; ?>

									<?php if ( $afectivalab_mision && 'actual' !== $afectivalab_estado ) : ?>
										<span class="camino__mision">
											<?php afectivalab_icon( $afectivalab_mision['icono'] ); ?>
											<?php echo esc_html( $afectivalab_mision['nombre'] ); ?>
											<em>
												<?php
												printf(
													/* translators: %d: monedas de la misión. */
													esc_html__( '+%d monedas', 'afectivalab' ),
													absint( $afectivalab_mision['recompensa'] )
												);
												?>
											</em>
										</span>
									<?php endif; ?>

									<?php if ( 'actual' === $afectivalab_estado ) : ?>
										<a class="btn btn-primary camino__comenzar" href="<?php echo esc_url( $afectivalab_url ); ?>">
											<?php
											if ( $afectivalab_mision ) {
												printf(
													/* translators: %d: monedas de la misión. */
													esc_html__( 'Comenzar clase (+%d monedas)', 'afectivalab' ),
													absint( $afectivalab_mision['recompensa'] )
												);
											} else {
												esc_html_e( 'Comenzar clase', 'afectivalab' );
											}
											?>
											<?php afectivalab_icon( 'arrow-right' ); ?>
										</a>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>

						<li class="camino__paso camino__paso--meta <?php echo $afectivalab_completo ? 'is-hecha' : 'is-bloqueada'; ?>">
							<div class="camino__info camino__info--meta">
								<span class="camino__meta-pill">
									<?php afectivalab_icon( 'check' ); ?>
									<?php esc_html_e( 'Meta final del curso', 'afectivalab' ); ?>
								</span>
								<span class="camino__nodo camino__nodo--meta" aria-hidden="true">
									<?php afectivalab_icon( 'juego-certificado', 'camino__marca' ); ?>
								</span>
								<strong class="camino__titulo">
									<?php
									echo esc_html(
										$afectivalab_habilidad
											/* translators: %s: habilidad que otorga el curso. */
											? sprintf( __( 'Insignia %s & Certificado', 'afectivalab' ), $afectivalab_habilidad )
											: __( 'Insignia & Certificado', 'afectivalab' )
									);
									?>
								</strong>
								<p class="camino__resumen">
									<?php
									$afectivalab_hijo
										? printf(
											/* translators: 1: cantidad de microclases, 2: nombre del hijo. */
											esc_html( _n( 'Al completar la %1$d microclase, %2$s recibe su insignia y su certificado.', 'Al completar las %1$d microclases, %2$s recibe su insignia y su certificado.', $afectivalab_total_clases, 'afectivalab' ) ),
											absint( $afectivalab_total_clases ),
											esc_html( $afectivalab_hijo->post_title )
										)
										: printf(
											/* translators: %d: cantidad de microclases. */
											esc_html( _n( 'Al completar la %d microclase se obtiene la insignia y el certificado.', 'Al completar las %d microclases se obtiene la insignia y el certificado.', $afectivalab_total_clases, 'afectivalab' ) ),
											absint( $afectivalab_total_clases )
										);
									?>
								</p>
								<div class="camino__certificado">
									<?php afectivalab_icon( 'juego-certificado' ); ?>
									<span>
										<strong>
											<?php
											echo esc_html(
												$afectivalab_hijo
													/* translators: %s: nombre del hijo. */
													? sprintf( __( 'Certificado para %s', 'afectivalab' ), $afectivalab_hijo->post_title )
													: __( 'Certificado del curso', 'afectivalab' )
											);
											?>
										</strong>
										<small><?php esc_html_e( 'Personalizado con su nombre', 'afectivalab' ); ?></small>
									</span>
									<?php if ( $afectivalab_completo ) : ?>
										<a class="camino__certificado-estado camino__certificado-estado--listo" href="<?php echo esc_url( afectivalab_certificado_url( $afectivalab_hijo->ID, $afectivalab_curso_id ) ); ?>">
											<?php esc_html_e( 'Descargar', 'afectivalab' ); ?>
										</a>
									<?php else : ?>
										<span class="camino__certificado-estado"><?php esc_html_e( 'Bloqueado', 'afectivalab' ); ?></span>
									<?php endif; ?>
								</div>
							</div>
						</li>
					</ol>
				</section>
			<?php endif; ?>

			<aside class="curso-aside">
				<?php if ( $afectivalab_nodos ) : ?>
					<section class="curso-card">
						<div class="curso-card__cabecera">
							<h2><?php afectivalab_icon( 'juego-insignia' ); ?><?php esc_html_e( 'Recompensas del curso', 'afectivalab' ); ?></h2>
							<span class="curso-card__cuenta">
								<?php
								printf(
									/* translators: %d: recompensas ya obtenidas, de 2. */
									esc_html__( '%d/2 listas', 'afectivalab' ),
									$afectivalab_completo ? 2 : 0
								);
								?>
							</span>
						</div>
						<ul class="curso-recompensas">
							<li class="curso-recompensa<?php echo $afectivalab_completo ? ' is-lista' : ''; ?>">
								<span class="curso-recompensa__icono"><?php afectivalab_icon( 'juego-insignia' ); ?></span>
								<span class="curso-recompensa__texto">
									<strong>
										<?php
										echo esc_html(
											$afectivalab_habilidad
												/* translators: %s: habilidad que otorga el curso. */
												? sprintf( __( 'Insignia "%s"', 'afectivalab' ), $afectivalab_habilidad )
												: __( 'Insignia del curso', 'afectivalab' )
										);
										?>
									</strong>
									<small><?php esc_html_e( 'Se suma a sus habilidades adquiridas', 'afectivalab' ); ?></small>
								</span>
								<span class="curso-recompensa__estado"><?php $afectivalab_completo ? afectivalab_icon( 'check' ) : esc_html_e( 'Meta', 'afectivalab' ); ?></span>
							</li>
							<li class="curso-recompensa<?php echo $afectivalab_completo ? ' is-lista' : ''; ?>">
								<span class="curso-recompensa__icono"><?php afectivalab_icon( 'juego-certificado' ); ?></span>
								<span class="curso-recompensa__texto">
									<strong>
										<?php
										echo esc_html(
											$afectivalab_hijo
												/* translators: %s: nombre del hijo. */
												? sprintf( __( 'Certificado para %s', 'afectivalab' ), $afectivalab_hijo->post_title )
												: __( 'Certificado del curso', 'afectivalab' )
										);
										?>
									</strong>
									<small><?php esc_html_e( 'Para descargar e imprimir', 'afectivalab' ); ?></small>
								</span>
								<span class="curso-recompensa__estado"><?php $afectivalab_completo ? afectivalab_icon( 'check' ) : esc_html_e( 'Meta', 'afectivalab' ); ?></span>
							</li>
						</ul>
					</section>
				<?php endif; ?>

				<?php if ( has_post_thumbnail() || get_the_content() ) : ?>
					<section class="curso-card curso-detalle">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="curso-portada">
								<?php the_post_thumbnail( 'large' ); ?>
							</div>
						<?php endif; ?>
						<?php if ( get_the_content() ) : ?>
							<div class="curso-card__cabecera">
								<h2><?php afectivalab_icon( 'feature-rutas' ); ?><?php esc_html_e( 'Sobre este curso', 'afectivalab' ); ?></h2>
							</div>
							<div class="curso-detalle__texto"><?php the_content(); ?></div>
						<?php endif; ?>
					</section>
				<?php endif; ?>
			</aside>

		</div>
	</div>
</main>

<?php get_footer(); ?>
