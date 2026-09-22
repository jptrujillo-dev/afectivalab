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

afectivalab_handle_clase_form( $afectivalab_clase_id, $afectivalab_hijo->ID );

$afectivalab_hecha    = afectivalab_clase_completada( $afectivalab_hijo->ID, $afectivalab_clase_id );
$afectivalab_duracion = (int) get_post_meta( $afectivalab_clase_id, '_afectivalab_duracion', true );
$afectivalab_tipo     = get_post_meta( $afectivalab_clase_id, '_afectivalab_video_tipo', true );
$afectivalab_nodos    = afectivalab_ruta_del_curso( $afectivalab_hijo->ID, $afectivalab_curso_id );

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
			<?php $afectivalab_video_url = get_post_meta( $afectivalab_clase_id, '_afectivalab_video_url', true ); ?>
			<?php if ( $afectivalab_video_url ) : ?>
				<div class="clase-video">
					<?php
					$afectivalab_embed = wp_oembed_get( $afectivalab_video_url );

					if ( $afectivalab_embed ) {
						echo $afectivalab_embed; // phpcs:ignore WordPress.Security.EscapeOutput -- markup de oEmbed de WordPress.
					} else {
						printf(
							'<a class="clase-video__enlace" href="%1$s" target="_blank" rel="noopener">%2$s</a>',
							esc_url( $afectivalab_video_url ),
							esc_html__( 'Ver el video', 'afectivalab' )
						);
					}
					?>
				</div>
			<?php endif; ?>
		<?php elseif ( 'media' === $afectivalab_tipo ) : ?>
			<?php $afectivalab_video_id = (int) get_post_meta( $afectivalab_clase_id, '_afectivalab_video_id', true ); ?>
			<?php if ( $afectivalab_video_id ) : ?>
				<div class="clase-video">
					<video controls preload="metadata" src="<?php echo esc_url( wp_get_attachment_url( $afectivalab_video_id ) ); ?>"></video>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( get_the_content() ) : ?>
			<div class="clase-contenido"><?php the_content(); ?></div>
		<?php endif; ?>

		<div class="clase-accion <?php echo $afectivalab_hecha ? 'is-hecha' : ''; ?>">
			<?php if ( $afectivalab_hecha ) : ?>
				<span class="clase-accion__sello">
					<?php afectivalab_icon( 'check' ); ?>
					<?php esc_html_e( 'Ya la vieron', 'afectivalab' ); ?>
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
