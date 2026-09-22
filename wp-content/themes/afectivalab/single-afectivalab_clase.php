<?php
/**
 * Una microclase: el video, el contenido y el botón para marcarla como vista.
 *
 * Las clases se abren en orden. Si alguien llega a una que todavía no le
 * toca, se lo devuelve al curso en vez de mostrarle el contenido.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

the_post();

$afectivalab_clase_id = get_the_ID();
$afectivalab_curso_id = (int) get_post_meta( $afectivalab_clase_id, '_afectivalab_curso', true );
$afectivalab_hijo     = is_user_logged_in() ? afectivalab_hijo_activo() : null;

if ( ! $afectivalab_hijo ) {
	wp_safe_redirect( $afectivalab_curso_id ? get_permalink( $afectivalab_curso_id ) : home_url( '/registro' ) );
	exit;
}

if ( ! afectivalab_clase_desbloqueada( $afectivalab_hijo->ID, $afectivalab_clase_id ) ) {
	wp_safe_redirect( $afectivalab_curso_id ? get_permalink( $afectivalab_curso_id ) : home_url( '/' ) );
	exit;
}

$afectivalab_mision = afectivalab_clase_mision( $afectivalab_clase_id );

// Si la clase lleva misión, resolverla es lo que la completa — no alcanza
// con ver el video (decisión de producto, ver inc/misiones.php). Si no lleva
// misión, sigue funcionando el botón de "marcar como vista" de siempre.
if ( $afectivalab_mision ) {
	afectivalab_handle_mision_form( $afectivalab_clase_id, $afectivalab_hijo->ID );
} else {
	afectivalab_handle_clase_form( $afectivalab_clase_id, $afectivalab_hijo->ID );
}

$afectivalab_hecha        = afectivalab_clase_completada( $afectivalab_hijo->ID, $afectivalab_clase_id );
$afectivalab_mision_estado = $afectivalab_mision ? afectivalab_mision_estado( $afectivalab_hijo->ID, $afectivalab_clase_id ) : '';
$afectivalab_duracion     = (int) get_post_meta( $afectivalab_clase_id, '_afectivalab_duracion', true );
$afectivalab_tipo         = get_post_meta( $afectivalab_clase_id, '_afectivalab_video_tipo', true );
$afectivalab_nodos        = afectivalab_ruta_del_curso( $afectivalab_hijo->ID, $afectivalab_curso_id );

$afectivalab_numero = 0;
foreach ( $afectivalab_nodos as $afectivalab_nodo ) {
	if ( $afectivalab_nodo['clase']->ID === $afectivalab_clase_id ) {
		$afectivalab_numero = $afectivalab_nodo['numero'];
		break;
	}
}

get_header();
?>

<main class="clase-page">
	<div class="container clase-page__inner">

		<a class="clase-volver" href="<?php echo esc_url( get_permalink( $afectivalab_curso_id ) ); ?>">
			<?php afectivalab_icon( 'arrow-right', 'clase-volver__icon' ); ?>
			<?php echo esc_html( get_the_title( $afectivalab_curso_id ) ); ?>
		</a>

		<header class="clase-head">
			<span class="clase-head__numero">
				<?php
				printf(
					/* translators: 1: número de clase, 2: total de clases. */
					esc_html__( 'Clase %1$d de %2$d', 'afectivalab' ),
					absint( $afectivalab_numero ),
					count( $afectivalab_nodos )
				);
				?>
			</span>

			<h1 class="clase-head__title"><?php the_title(); ?></h1>

			<?php if ( $afectivalab_duracion ) : ?>
				<span class="clase-head__meta">
					<?php afectivalab_icon( 'leccion-video-principal' ); ?>
					<?php
					printf(
						/* translators: %d: duración en minutos. */
						esc_html__( '%d min', 'afectivalab' ),
						absint( $afectivalab_duracion )
					);
					?>
				</span>
			<?php endif; ?>
		</header>

		<?php if ( 'url' === $afectivalab_tipo ) : ?>
			<?php
			$afectivalab_video_url = get_post_meta( $afectivalab_clase_id, '_afectivalab_video_url', true );
			$afectivalab_embed     = afectivalab_video_embed_url( $afectivalab_video_url );
			?>
			<?php if ( $afectivalab_embed ) : ?>
				<div class="clase-video">
					<iframe
						src="<?php echo esc_url( $afectivalab_embed ); ?>"
						title="<?php the_title_attribute(); ?>"
						loading="lazy"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						allowfullscreen
					></iframe>
				</div>
			<?php elseif ( $afectivalab_video_url ) : ?>
				<?php // No es YouTube ni Vimeo: en vez de incrustar cualquier cosa, se enlaza. ?>
				<div class="clase-video">
					<a class="clase-video__enlace" href="<?php echo esc_url( $afectivalab_video_url ); ?>" target="_blank" rel="noopener">
						<?php esc_html_e( 'Ver el video', 'afectivalab' ); ?>
					</a>
				</div>
			<?php endif; ?>
		<?php elseif ( 'media' === $afectivalab_tipo ) : ?>
			<?php $afectivalab_video_id = (int) get_post_meta( $afectivalab_clase_id, '_afectivalab_video_id', true ); ?>
			<?php if ( $afectivalab_video_id ) : ?>
				<?php
				// En un video propio sí se puede dejar el reproductor al
				// mínimo: se quitan descarga, velocidad y miniatura flotante,
				// y quedan play, volumen, barra y pantalla completa.
				?>
				<div class="clase-video">
					<video
						controls
						controlsList="nodownload noplaybackrate noremoteplayback"
						disablePictureInPicture
						preload="metadata"
						<?php if ( has_post_thumbnail( $afectivalab_clase_id ) ) : ?>
							poster="<?php echo esc_url( get_the_post_thumbnail_url( $afectivalab_clase_id, 'large' ) ); ?>"
						<?php endif; ?>
						src="<?php echo esc_url( wp_get_attachment_url( $afectivalab_video_id ) ); ?>"
					></video>
				</div>
			<?php endif; ?>
		<?php elseif ( has_post_thumbnail( $afectivalab_clase_id ) ) : ?>
			<?php // Sin video, la imagen destacada es lo que abre la clase. ?>
			<div class="clase-portada">
				<?php the_post_thumbnail( 'large' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( get_the_content() ) : ?>
			<div class="clase-contenido"><?php the_content(); ?></div>
		<?php endif; ?>

		<div class="clase-accion <?php echo $afectivalab_hecha ? 'is-hecha' : ''; ?>">
			<?php if ( $afectivalab_hecha ) : ?>
				<span class="clase-accion__sello">
					<?php afectivalab_icon( 'check' ); ?>
					<?php
					if ( $afectivalab_mision ) {
						printf(
							/* translators: %d: monedas ganadas. */
							esc_html__( '¡Misión cumplida! +%d monedas', 'afectivalab' ),
							absint( $afectivalab_mision['recompensa'] )
						);
					} else {
						esc_html_e( 'Ya la vieron', 'afectivalab' );
					}
					?>
				</span>

				<?php
				$afectivalab_progreso = afectivalab_progreso_curso( $afectivalab_hijo->ID, $afectivalab_curso_id );
				$afectivalab_siguiente = $afectivalab_progreso['siguiente'];
				?>

				<?php if ( $afectivalab_siguiente ) : ?>
					<a class="btn btn-primary" href="<?php echo esc_url( get_permalink( $afectivalab_siguiente ) ); ?>">
						<?php esc_html_e( 'Siguiente clase', 'afectivalab' ); ?>
						<?php afectivalab_icon( 'arrow-right' ); ?>
					</a>
				<?php else : ?>
					<a class="btn btn-primary" href="<?php echo esc_url( get_permalink( $afectivalab_curso_id ) ); ?>">
						<?php esc_html_e( 'Volver al curso', 'afectivalab' ); ?>
						<?php afectivalab_icon( 'arrow-right' ); ?>
					</a>
				<?php endif; ?>
			<?php elseif ( $afectivalab_mision ) : ?>

				<?php // Clase con misión: resolverla es lo que la completa. Ver inc/misiones.php. ?>
				<div class="mision-card <?php echo 'despues' === $afectivalab_mision_estado ? 'is-despues' : ''; ?>">
					<span class="mision-card__icono">
						<?php afectivalab_icon( $afectivalab_mision['icono'] ); ?>
					</span>

					<div class="mision-card__cuerpo">
						<span class="mision-card__etiqueta"><?php echo esc_html( $afectivalab_mision['nombre'] ); ?></span>
						<p class="mision-card__texto"><?php echo esc_html( $afectivalab_mision['texto'] ); ?></p>

						<?php if ( 'despues' === $afectivalab_mision_estado ) : ?>
							<p class="mision-card__aviso">
								<?php esc_html_e( 'La dejaste pendiente. Cuando la hagan, márcala aquí para seguir avanzando.', 'afectivalab' ); ?>
							</p>
						<?php endif; ?>

						<p class="mision-card__recompensa">
							<?php afectivalab_icon( 'juego-monedas' ); ?>
							<?php
							printf(
								/* translators: %d: monedas que otorga la misión. */
								esc_html__( 'Vale %d monedas al completarla.', 'afectivalab' ),
								absint( $afectivalab_mision['recompensa'] )
							);
							?>
						</p>

						<form method="post" class="mision-card__form" <?php echo $afectivalab_mision['requiere_evidencia'] ? 'enctype="multipart/form-data"' : ''; ?>>
							<?php wp_nonce_field( 'afectivalab_mision_' . $afectivalab_clase_id, 'afectivalab_mision_nonce' ); ?>

							<?php if ( $afectivalab_mision['requiere_evidencia'] ) : ?>
								<label class="mision-card__subir">
									<?php esc_html_e( 'Sube una foto como evidencia', 'afectivalab' ); ?>
									<input type="file" name="evidencia" accept="image/jpeg,image/png,image/webp" required>
								</label>
							<?php endif; ?>

							<div class="mision-card__botones">
								<button type="submit" name="afectivalab_mision_accion" value="hecha" class="btn btn-primary">
									<?php afectivalab_icon( 'check' ); ?>
									<?php esc_html_e( 'Ya la hicimos', 'afectivalab' ); ?>
								</button>

								<?php if ( 'despues' !== $afectivalab_mision_estado ) : ?>
									<button type="submit" name="afectivalab_mision_accion" value="despues" class="btn btn-ghost" formnovalidate>
										<?php esc_html_e( 'La haremos después', 'afectivalab' ); ?>
									</button>
								<?php endif; ?>
							</div>
						</form>
					</div>
				</div>

			<?php else : ?>
				<div class="clase-accion__texto">
					<strong><?php esc_html_e( '¿Ya vieron esta clase?', 'afectivalab' ); ?></strong>
					<p>
						<?php
						printf(
							/* translators: %s: nombre del hijo. */
							esc_html__( 'Al marcarla avanza la ruta de %s y se desbloquea la siguiente.', 'afectivalab' ),
							esc_html( $afectivalab_hijo->post_title )
						);
						?>
					</p>
				</div>

				<form method="post">
					<?php wp_nonce_field( 'afectivalab_clase_' . $afectivalab_clase_id, 'afectivalab_clase_nonce' ); ?>
					<button type="submit" name="afectivalab_clase_hecha" value="1" class="btn btn-primary">
						<?php afectivalab_icon( 'check' ); ?>
						<?php esc_html_e( 'Marcar como vista', 'afectivalab' ); ?>
					</button>
				</form>
			<?php endif; ?>
		</div>

	</div>
</main>

<?php get_footer(); ?>
