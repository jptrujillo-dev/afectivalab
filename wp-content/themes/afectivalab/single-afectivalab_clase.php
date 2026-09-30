<?php
/**
 * Una microclase: el video, el contenido y el botón para marcarla como vista.
 *
 * Las clases se abren en orden. Si alguien llega a una que todavía no le
 * toca, se lo devuelve al curso en vez de mostrarle el contenido.
 *
 * Diseño: pantalla 3 de la referencia Stitch (docs/rediseno-stitch.md). De
 * esa referencia solo se toma la forma; todo lo que muestra sale de datos
 * reales de la clase, del curso y del hijo.
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

// Recién acá, con sesión y un hijo real, tiene sentido pedir suscripción: el
// contenido de la microclase (video, misión, caso) es lo que de verdad se
// paga — el curso-vitrina sigue viéndose sin pagar (ver single-afectivalab_curso.php).
afectivalab_requiere_suscripcion();

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

// El caso interactivo es independiente de lo anterior: nunca redirige y
// nunca completa la clase por su cuenta (ver inc/casos.php), así que se
// procesa siempre, sin importar si la clase lleva misión o no.
afectivalab_handle_caso_form( $afectivalab_clase_id, $afectivalab_hijo->ID );

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

// El caso, el progreso y el cierre (misión / "marcar como vista") viven en
// template-parts/clase/: los mismos bloques los devuelven las respuestas
// AJAX de inc/clase-ajax.php, así que se responde sin recargar la página.

get_header();
?>

<main class="clase-page">
	<div class="container clase-page__inner">

		<div class="clase-top">
			<a class="clase-volver" href="<?php echo esc_url( get_permalink( $afectivalab_curso_id ) ); ?>">
				<span class="clase-volver__flecha" aria-hidden="true"><?php afectivalab_icon( 'arrow-right', 'clase-volver__icon' ); ?></span>
				<?php esc_html_e( 'Volver al mapa del curso', 'afectivalab' ); ?>
			</a>

			<nav class="clase-migas" aria-label="<?php esc_attr_e( 'Dónde estás', 'afectivalab' ); ?>">
				<a href="<?php echo esc_url( home_url( '/panel' ) ); ?>">
					<?php
					printf(
						/* translators: %s: nombre del hijo. */
						esc_html__( 'Ruta de %s', 'afectivalab' ),
						esc_html( $afectivalab_hijo->post_title )
					);
					?>
				</a>
				<span aria-hidden="true">›</span>
				<a href="<?php echo esc_url( get_permalink( $afectivalab_curso_id ) ); ?>"><?php echo esc_html( get_the_title( $afectivalab_curso_id ) ); ?></a>
			</nav>
		</div>

		<header class="clase-head">
			<ul class="clase-head__chips">
				<li class="clase-chip clase-chip--numero">
					<?php afectivalab_icon( 'juego-mapa-desbloqueable' ); ?>
					<?php
					printf(
						/* translators: 1: número de clase, 2: total de clases. */
						esc_html__( 'Clase %1$d de %2$d', 'afectivalab' ),
						absint( $afectivalab_numero ),
						count( $afectivalab_nodos )
					);
					?>
				</li>
				<?php if ( $afectivalab_duracion ) : ?>
					<li class="clase-chip">
						<?php afectivalab_icon( 'leccion-video-principal' ); ?>
						<?php
						printf(
							/* translators: %d: duración en minutos. */
							esc_html__( '%d min', 'afectivalab' ),
							absint( $afectivalab_duracion )
						);
						?>
					</li>
				<?php endif; ?>
				<?php if ( $afectivalab_mision ) : ?>
					<li class="clase-chip clase-chip--monedas">
						<?php afectivalab_icon( 'juego-monedas' ); ?>
						<?php
						printf(
							/* translators: %d: monedas de la misión. */
							esc_html__( '+%d monedas al completar la misión', 'afectivalab' ),
							absint( $afectivalab_mision['recompensa'] )
						);
						?>
					</li>
				<?php endif; ?>
			</ul>

			<h1 class="clase-head__title"><?php the_title(); ?></h1>

			<?php get_template_part( 'template-parts/clase/progreso', null, array( 'clase_id' => $afectivalab_clase_id, 'hijo_id' => $afectivalab_hijo->ID ) ); ?>
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
						<span class="clase-video__play" aria-hidden="true"></span>
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
			<section class="clase-bloque clase-contenido">
				<div class="clase-bloque__cabecera">
					<span class="clase-bloque__icono"><?php afectivalab_icon( 'paso-aplica-casa' ); ?></span>
					<div>
						<h2><?php esc_html_e( 'Lo esencial de esta clase', 'afectivalab' ); ?></h2>
						<p>
							<?php
							printf(
								/* translators: %s: nombre del hijo. */
								esc_html__( 'Para aplicar hoy en casa con %s', 'afectivalab' ),
								esc_html( $afectivalab_hijo->post_title )
							);
							?>
						</p>
					</div>
				</div>
				<div class="clase-contenido__texto"><?php the_content(); ?></div>
			</section>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/clase/caso', null, array( 'clase_id' => $afectivalab_clase_id, 'hijo_id' => $afectivalab_hijo->ID ) ); ?>

		<?php get_template_part( 'template-parts/clase/accion', null, array( 'clase_id' => $afectivalab_clase_id, 'hijo_id' => $afectivalab_hijo->ID ) ); ?>

	</div>
</main>

<?php get_footer(); ?>
