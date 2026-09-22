<?php
/**
 * Un curso: su ruta de microclases como camino, no como lista.
 *
 * Si no hay sesión, el curso igual se ve (sirve de vitrina), pero el camino
 * aparece cerrado con una invitación a crear la cuenta.
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

$afectivalab_hijo     = is_user_logged_in() ? afectivalab_hijo_activo() : null;
$afectivalab_progreso = $afectivalab_hijo ? afectivalab_progreso_curso( $afectivalab_hijo->ID, $afectivalab_curso_id ) : null;

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

get_header();
?>

<main class="curso-page">

	<header class="curso-hero">
		<div class="container curso-hero__inner">
			<?php if ( $afectivalab_eje ) : ?>
				<span class="curso-hero__eje">
					<?php afectivalab_icon( 'eje-' . $afectivalab_eje->slug ); ?>
					<?php echo esc_html( $afectivalab_eje->name ); ?>
				</span>
			<?php endif; ?>

			<h1 class="curso-hero__title"><?php the_title(); ?></h1>

			<?php if ( has_excerpt() ) : ?>
				<p class="curso-hero__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>

			<ul class="curso-hero__facts">
				<?php if ( $afectivalab_etapa ) : ?>
					<li>
						<?php afectivalab_icon( 'juego-mapa-desbloqueable' ); ?>
						<?php echo esc_html( $afectivalab_etapa->name ); ?>
					</li>
				<?php endif; ?>
				<li>
					<?php afectivalab_icon( 'leccion-video-principal' ); ?>
					<?php
					printf(
						/* translators: %d: cantidad de microclases. */
						esc_html( _n( '%d microclase', '%d microclases', count( $afectivalab_nodos ), 'afectivalab' ) ),
						count( $afectivalab_nodos )
					);
					?>
				</li>
				<?php if ( $afectivalab_habilidad ) : ?>
					<li>
						<?php afectivalab_icon( 'juego-insignia' ); ?>
						<?php echo esc_html( $afectivalab_habilidad ); ?>
					</li>
				<?php endif; ?>
			</ul>

			<?php if ( $afectivalab_progreso && $afectivalab_progreso['total'] ) : ?>
				<div class="curso-hero__progreso">
					<div class="progreso-bar">
						<span class="progreso-bar__fill" style="width: <?php echo esc_attr( $afectivalab_progreso['porcentaje'] ); ?>%"></span>
					</div>
					<p class="curso-hero__progreso-texto">
						<?php
						printf(
							/* translators: 1: nombre del hijo, 2: clases hechas, 3: total. */
							esc_html__( '%1$s va %2$d de %3$d', 'afectivalab' ),
							esc_html( $afectivalab_hijo->post_title ),
							absint( $afectivalab_progreso['hechas'] ),
							absint( $afectivalab_progreso['total'] )
						);
						?>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</header>

	<div class="container">

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="curso-portada">
				<?php the_post_thumbnail( 'large' ); ?>
			</div>
		<?php endif; ?>

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
				</div>
			<?php endif; ?>

			<ol class="camino" role="list">
				<?php foreach ( $afectivalab_nodos as $afectivalab_nodo ) : ?>
					<?php
					$afectivalab_clase    = $afectivalab_nodo['clase'];
					$afectivalab_estado   = $afectivalab_nodo['estado'];
					$afectivalab_duracion = (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_duracion', true );

					$afectivalab_marca = array(
						'hecha'     => 'check',
						'actual'    => 'star',
						'bloqueada' => 'lock',
					)[ $afectivalab_estado ];
					?>
					<li class="camino__paso is-<?php echo esc_attr( $afectivalab_estado ); ?>">
						<?php if ( 'bloqueada' === $afectivalab_estado ) : ?>
							<span class="camino__nodo" aria-hidden="true">
								<?php afectivalab_icon( $afectivalab_marca, 'camino__marca' ); ?>
							</span>
						<?php else : ?>
							<a class="camino__nodo" href="<?php echo esc_url( get_permalink( $afectivalab_clase ) ); ?>">
								<?php afectivalab_icon( $afectivalab_marca, 'camino__marca' ); ?>
							</a>
						<?php endif; ?>

						<div class="camino__info">
							<span class="camino__numero">
								<?php
								printf(
									/* translators: %d: número de la microclase. */
									esc_html__( 'Clase %d', 'afectivalab' ),
									absint( $afectivalab_nodo['numero'] )
								);
								?>
							</span>

							<strong class="camino__titulo">
								<?php if ( 'bloqueada' === $afectivalab_estado ) : ?>
									<?php echo esc_html( $afectivalab_clase->post_title ); ?>
								<?php else : ?>
									<a href="<?php echo esc_url( get_permalink( $afectivalab_clase ) ); ?>">
										<?php echo esc_html( $afectivalab_clase->post_title ); ?>
									</a>
								<?php endif; ?>
							</strong>

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
					</li>
				<?php endforeach; ?>

				<li class="camino__paso camino__paso--meta <?php echo $afectivalab_progreso && $afectivalab_progreso['completo'] ? 'is-hecha' : 'is-bloqueada'; ?>">
					<span class="camino__nodo camino__nodo--meta" aria-hidden="true">
						<?php afectivalab_icon( 'juego-certificado', 'camino__marca' ); ?>
					</span>
					<div class="camino__info">
						<span class="camino__numero"><?php esc_html_e( 'Meta', 'afectivalab' ); ?></span>
						<strong class="camino__titulo">
							<?php echo esc_html( $afectivalab_habilidad ? $afectivalab_habilidad : __( 'Curso completo', 'afectivalab' ) ); ?>
						</strong>
						<span class="camino__meta"><?php esc_html_e( 'Insignia y certificado', 'afectivalab' ); ?></span>
					</div>
				</li>
			</ol>

			<?php if ( $afectivalab_progreso && $afectivalab_progreso['completo'] ) : ?>
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
					<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/mi-cuenta' ) ); ?>">
						<?php esc_html_e( 'Ver mi avance', 'afectivalab' ); ?>
						<?php afectivalab_icon( 'arrow-right' ); ?>
					</a>
				</div>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ( get_the_content() ) : ?>
			<section class="curso-detalle">
				<h2><?php esc_html_e( 'Sobre este curso', 'afectivalab' ); ?></h2>
				<div class="curso-detalle__texto"><?php the_content(); ?></div>
			</section>
		<?php endif; ?>

	</div>
</main>

<?php get_footer(); ?>
