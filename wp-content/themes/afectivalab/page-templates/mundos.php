<?php
/**
 * Ruta /mundos — exploración libre por mundo temático (eje), navegación
 * SECUNDARIA a la ruta personalizada de /panel. Ver inc/mundos.php.
 *
 * /mundos            → los 8 mundos, como mapa.
 * /mundos?eje=<slug> → los cursos de ese mundo, agrupados por etapa.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/ingresar' ) );
	exit;
}

$afectivalab_ejes      = afectivalab_ejes();
$afectivalab_eje_pedido = isset( $_GET['eje'] ) ? sanitize_key( wp_unslash( $_GET['eje'] ) ) : '';
$afectivalab_eje_actual = isset( $afectivalab_ejes[ $afectivalab_eje_pedido ] ) ? $afectivalab_eje_pedido : '';

get_header();
?>

<main class="mundos-page">
	<div class="container">

		<?php if ( ! $afectivalab_eje_actual ) : ?>

			<?php
			$afectivalab_cantidades = array();
			foreach ( $afectivalab_ejes as $afectivalab_slug => $afectivalab_datos ) {
				$afectivalab_cantidades[ $afectivalab_slug ] = count( afectivalab_cursos_del_eje( $afectivalab_slug ) );
			}
			$afectivalab_total_cursos = array_sum( $afectivalab_cantidades );
			?>

			<header class="mundos-hub__head reveal">
				<span class="mundos-hub__chip">
					<?php
					printf(
						/* translators: 1: cantidad de cursos, 2: cantidad de mundos. */
						esc_html( _n( '%1$d curso en %2$d mundos', '%1$d cursos en %2$d mundos', $afectivalab_total_cursos, 'afectivalab' ) ),
						absint( $afectivalab_total_cursos ),
						absint( count( $afectivalab_ejes ) )
					);
					?>
				</span>
				<h1><?php esc_html_e( 'Explora por mundo temático', 'afectivalab' ); ?></h1>
				<p><?php esc_html_e( 'Además de la ruta pensada para tu hijo, puedes recorrer libremente todos los temas que trabajamos, a tu propio ritmo.', 'afectivalab' ); ?></p>
			</header>

			<ul class="mundos-grid reveal-stagger">
				<?php foreach ( $afectivalab_ejes as $afectivalab_slug => $afectivalab_datos ) : ?>
					<?php $afectivalab_cantidad = $afectivalab_cantidades[ $afectivalab_slug ]; ?>
					<li>
						<a class="mundo-card<?php echo $afectivalab_cantidad ? '' : ' is-vacio'; ?>" href="<?php echo esc_url( add_query_arg( 'eje', $afectivalab_slug, home_url( '/mundos' ) ) ); ?>">
							<span class="mundo-card__top">
								<span class="mundo-card__icono">
									<?php afectivalab_icon( 'eje-' . $afectivalab_slug ); ?>
								</span>
								<span class="mundo-card__cantidad">
									<?php
									if ( $afectivalab_cantidad ) {
										printf(
											/* translators: %d: cantidad de cursos. */
											esc_html( _n( '%d curso', '%d cursos', $afectivalab_cantidad, 'afectivalab' ) ),
											absint( $afectivalab_cantidad )
										);
									} else {
										esc_html_e( 'Próximamente', 'afectivalab' );
									}
									?>
								</span>
							</span>
							<strong><?php echo esc_html( $afectivalab_datos['nombre'] ); ?></strong>
							<p><?php echo esc_html( $afectivalab_datos['resumen'] ); ?></p>
							<span class="mundo-card__ir">
								<?php esc_html_e( 'Ver cursos', 'afectivalab' ); ?>
								<?php afectivalab_icon( 'arrow-right' ); ?>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

		<?php else : ?>

			<a class="mundos-volver" href="<?php echo esc_url( home_url( '/mundos' ) ); ?>">
				<?php afectivalab_icon( 'arrow-right', 'mundos-volver__icono' ); ?>
				<?php esc_html_e( 'Todos los mundos', 'afectivalab' ); ?>
			</a>

			<?php
			$afectivalab_grupos       = afectivalab_cursos_del_eje_por_etapa( $afectivalab_eje_actual );
			$afectivalab_etapas_datos = afectivalab_etapas();
			$afectivalab_hijo_mundo   = afectivalab_hijo_activo();
			$afectivalab_cursos_total = 0;
			foreach ( $afectivalab_grupos as $afectivalab_grupo ) {
				$afectivalab_cursos_total += count( $afectivalab_grupo['cursos'] );
			}
			?>

			<header class="mundo-detalle__head reveal">
				<span class="mundo-detalle__icono">
					<?php afectivalab_icon( 'eje-' . $afectivalab_eje_actual ); ?>
				</span>
				<div class="mundo-detalle__texto">
					<span class="mundo-detalle__chip"><?php esc_html_e( 'Mundo temático', 'afectivalab' ); ?></span>
					<h1><?php echo esc_html( $afectivalab_ejes[ $afectivalab_eje_actual ]['nombre'] ); ?></h1>
					<p><?php echo esc_html( $afectivalab_ejes[ $afectivalab_eje_actual ]['resumen'] ); ?></p>
				</div>
				<?php if ( $afectivalab_cursos_total ) : ?>
					<div class="mundo-detalle__cifras">
						<span>
							<strong><?php echo absint( $afectivalab_cursos_total ); ?></strong>
							<?php echo esc_html( _n( 'curso', 'cursos', $afectivalab_cursos_total, 'afectivalab' ) ); ?>
						</span>
						<span>
							<strong><?php echo absint( count( $afectivalab_grupos ) ); ?></strong>
							<?php echo esc_html( _n( 'etapa', 'etapas', count( $afectivalab_grupos ), 'afectivalab' ) ); ?>
						</span>
					</div>
				<?php endif; ?>
			</header>

			<?php if ( ! $afectivalab_grupos ) : ?>

				<div class="mundos-vacio reveal">
					<?php afectivalab_icon( 'juego-mapa-desbloqueable', 'mundos-vacio__icon' ); ?>
					<h2><?php esc_html_e( 'Todavía no hay cursos en este mundo', 'afectivalab' ); ?></h2>
					<p><?php esc_html_e( 'Estamos preparando contenido para este tema. Vuelve pronto.', 'afectivalab' ); ?></p>
				</div>

			<?php else : ?>

				<?php if ( count( $afectivalab_grupos ) > 1 ) : ?>
					<div class="mundo-filtro" role="group" aria-label="<?php esc_attr_e( 'Filtrar por edad', 'afectivalab' ); ?>" data-mundo-filtro hidden>
						<button type="button" class="mundo-filtro__chip is-activo" data-etapa="" aria-pressed="true">
							<?php esc_html_e( 'Todas las edades', 'afectivalab' ); ?>
						</button>
						<?php foreach ( $afectivalab_grupos as $afectivalab_slug => $afectivalab_grupo ) : ?>
							<button type="button" class="mundo-filtro__chip" data-etapa="<?php echo esc_attr( $afectivalab_slug ? $afectivalab_slug : 'sin-etapa' ); ?>" aria-pressed="false">
								<?php echo esc_html( $afectivalab_grupo['etapa'] ? $afectivalab_grupo['etapa']->name : __( 'Sin etapa', 'afectivalab' ) ); ?>
								<span class="mundo-filtro__num"><?php echo absint( count( $afectivalab_grupo['cursos'] ) ); ?></span>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php foreach ( $afectivalab_grupos as $afectivalab_slug => $afectivalab_grupo ) : ?>
					<?php $afectivalab_rango = isset( $afectivalab_etapas_datos[ $afectivalab_slug ] ) ? $afectivalab_etapas_datos[ $afectivalab_slug ] : null; ?>
					<section class="mundo-grupo" data-etapa="<?php echo esc_attr( $afectivalab_slug ? $afectivalab_slug : 'sin-etapa' ); ?>">
						<div class="mundo-grupo__head">
							<h2 class="mundo-grupo__titulo">
								<?php echo esc_html( $afectivalab_grupo['etapa'] ? $afectivalab_grupo['etapa']->name : __( 'Sin etapa asignada', 'afectivalab' ) ); ?>
							</h2>
							<?php if ( $afectivalab_rango ) : ?>
								<span class="mundo-grupo__edad">
									<?php
									printf(
										/* translators: 1: edad mínima, 2: edad máxima. */
										esc_html__( '%1$d a %2$d años', 'afectivalab' ),
										absint( $afectivalab_rango['edad_min'] ),
										absint( $afectivalab_rango['edad_max'] )
									);
									?>
								</span>
							<?php endif; ?>
						</div>

						<ul class="mundo-cursos reveal-stagger">
							<?php foreach ( $afectivalab_grupo['cursos'] as $afectivalab_curso ) : ?>
								<?php
								$afectivalab_clases  = afectivalab_clases_del_curso( $afectivalab_curso->ID );
								$afectivalab_minutos = 0;
								foreach ( $afectivalab_clases as $afectivalab_clase ) {
									$afectivalab_minutos += (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_duracion', true );
								}
								$afectivalab_avance = $afectivalab_hijo_mundo ? afectivalab_progreso_curso( $afectivalab_hijo_mundo->ID, $afectivalab_curso->ID ) : null;
								?>
								<li class="mundo-curso-card">
									<a href="<?php echo esc_url( get_permalink( $afectivalab_curso ) ); ?>">
										<span class="mundo-curso-card__imagen">
											<?php if ( has_post_thumbnail( $afectivalab_curso ) ) : ?>
												<?php echo get_the_post_thumbnail( $afectivalab_curso, 'medium_large', array( 'alt' => esc_attr( $afectivalab_curso->post_title ) ) ); ?>
											<?php else : ?>
												<?php afectivalab_icon( 'eje-' . $afectivalab_eje_actual, 'mundo-curso-card__sin-foto' ); ?>
											<?php endif; ?>
											<?php if ( $afectivalab_avance && $afectivalab_avance['completo'] ) : ?>
												<span class="mundo-curso-card__estado is-hecho"><?php esc_html_e( 'Completado', 'afectivalab' ); ?></span>
											<?php elseif ( $afectivalab_avance && $afectivalab_avance['hechas'] ) : ?>
												<span class="mundo-curso-card__estado"><?php esc_html_e( 'En curso', 'afectivalab' ); ?></span>
											<?php endif; ?>
										</span>
										<span class="mundo-curso-card__texto">
											<strong><?php echo esc_html( $afectivalab_curso->post_title ); ?></strong>
											<?php if ( has_excerpt( $afectivalab_curso ) ) : ?>
												<p><?php echo esc_html( get_the_excerpt( $afectivalab_curso ) ); ?></p>
											<?php endif; ?>

											<?php if ( $afectivalab_avance && $afectivalab_avance['hechas'] && ! $afectivalab_avance['completo'] ) : ?>
												<span class="mundo-curso-card__avance">
													<span class="progreso-bar progreso-bar--mini"><span class="progreso-bar__fill" style="width: <?php echo esc_attr( $afectivalab_avance['porcentaje'] ); ?>%"></span></span>
													<?php
													printf(
														/* translators: 1: nombre del hijo, 2: clases hechas, 3: total de clases. */
														esc_html__( '%1$s va %2$d de %3$d', 'afectivalab' ),
														esc_html( $afectivalab_hijo_mundo->post_title ),
														absint( $afectivalab_avance['hechas'] ),
														absint( $afectivalab_avance['total'] )
													);
													?>
												</span>
											<?php endif; ?>

											<span class="mundo-curso-card__pie">
												<span class="mundo-curso-card__meta">
													<?php
													if ( $afectivalab_clases ) {
														printf(
															/* translators: %d: cantidad de clases. */
															esc_html( _n( '%d clase', '%d clases', count( $afectivalab_clases ), 'afectivalab' ) ),
															absint( count( $afectivalab_clases ) )
														);
														if ( $afectivalab_minutos ) {
															echo ' · ' . absint( $afectivalab_minutos ) . ' min';
														}
													} else {
														esc_html_e( 'En preparación', 'afectivalab' );
													}
													?>
												</span>
												<span class="mundo-curso-card__ir" aria-hidden="true"><?php afectivalab_icon( 'arrow-right' ); ?></span>
											</span>
										</span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endforeach; ?>

			<?php endif; ?>

		<?php endif; ?>

	</div>
</main>

<?php get_footer(); ?>
