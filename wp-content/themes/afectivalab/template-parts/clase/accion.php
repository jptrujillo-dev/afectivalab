<?php
/**
 * El cierre de una microclase: la misión (si la trae), el botón "Marcar como
 * vista" (si no) o, una vez completada, el sello con el paso siguiente.
 *
 * Pieza aparte porque la pintan dos lados: la página de la clase y las
 * respuestas AJAX de misión y "marcar como vista" (ver inc/clase-ajax.php),
 * que devuelven este mismo bloque ya en su estado nuevo sin recargar.
 *
 * Los formularios siguen siendo formularios normales: sin JS se envían como
 * siempre y los procesa la plantilla (afectivalab_handle_mision_form(),
 * afectivalab_handle_clase_form()).
 *
 * Args: clase_id, hijo_id.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_clase_id      = (int) $args['clase_id'];
$afectivalab_hijo_id       = (int) $args['hijo_id'];
$afectivalab_hijo          = get_post( $afectivalab_hijo_id );
$afectivalab_curso_id      = (int) get_post_meta( $afectivalab_clase_id, '_afectivalab_curso', true );
$afectivalab_mision        = afectivalab_clase_mision( $afectivalab_clase_id );
$afectivalab_hecha         = afectivalab_clase_completada( $afectivalab_hijo_id, $afectivalab_clase_id );
$afectivalab_mision_estado = $afectivalab_mision ? afectivalab_mision_estado( $afectivalab_hijo_id, $afectivalab_clase_id ) : '';
$afectivalab_progreso      = afectivalab_progreso_curso( $afectivalab_hijo_id, $afectivalab_curso_id );
$afectivalab_habilidad     = get_post_meta( $afectivalab_curso_id, '_afectivalab_habilidad', true );
$afectivalab_datos_form    = sprintf( 'data-clase="%d" data-hijo="%d"', $afectivalab_clase_id, $afectivalab_hijo_id );

// La clase que viene después de esta, para el cierre "¿Qué desbloqueas?".
$afectivalab_numero      = 0;
$afectivalab_clase_luego = null;
$afectivalab_nodos       = afectivalab_ruta_del_curso( $afectivalab_hijo_id, $afectivalab_curso_id );
foreach ( $afectivalab_nodos as $afectivalab_i => $afectivalab_nodo ) {
	if ( $afectivalab_nodo['clase']->ID === $afectivalab_clase_id ) {
		$afectivalab_numero      = $afectivalab_nodo['numero'];
		$afectivalab_clase_luego = $afectivalab_nodos[ $afectivalab_i + 1 ]['clase'] ?? null;
		break;
	}
}
?>
<div class="clase-accion-zona" data-clase-accion>

	<?php if ( $afectivalab_hecha ) : ?>
		<div class="clase-accion is-hecha">
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

			<div class="clase-accion__botones">
				<?php if ( $afectivalab_progreso['completo'] && function_exists( 'afectivalab_certificado_url' ) ) : ?>
					<a class="btn btn-secondary" href="<?php echo esc_url( afectivalab_certificado_url( $afectivalab_hijo_id, $afectivalab_curso_id ) ); ?>">
						<?php afectivalab_icon( 'juego-certificado' ); ?>
						<?php esc_html_e( 'Ver certificado', 'afectivalab' ); ?>
					</a>
				<?php endif; ?>

				<?php if ( $afectivalab_progreso['siguiente'] ) : ?>
					<a class="btn btn-primary" href="<?php echo esc_url( get_permalink( $afectivalab_progreso['siguiente'] ) ); ?>">
						<?php esc_html_e( 'Siguiente clase', 'afectivalab' ); ?>
						<?php afectivalab_icon( 'arrow-right' ); ?>
					</a>
				<?php else : ?>
					<a class="btn btn-primary" href="<?php echo esc_url( get_permalink( $afectivalab_curso_id ) ); ?>">
						<?php esc_html_e( 'Volver al curso', 'afectivalab' ); ?>
						<?php afectivalab_icon( 'arrow-right' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

	<?php elseif ( $afectivalab_mision ) : ?>

		<?php // Clase con misión: resolverla es lo que la completa. Ver inc/misiones.php. ?>
		<section class="mision-card <?php echo 'despues' === $afectivalab_mision_estado ? 'is-despues' : ''; ?>">
			<div class="mision-card__cabecera">
				<span class="mision-card__icono">
					<?php afectivalab_icon( $afectivalab_mision['icono'] ); ?>
				</span>
				<div class="mision-card__titulos">
					<span class="mision-card__etiqueta"><?php echo esc_html( $afectivalab_mision['nombre'] ); ?></span>
					<h2 class="mision-card__titulo"><?php esc_html_e( 'Su misión en familia', 'afectivalab' ); ?></h2>
				</div>
				<span class="mision-card__recompensa">
					<?php afectivalab_icon( 'juego-monedas' ); ?>
					<?php
					printf(
						/* translators: %d: monedas que otorga la misión. */
						esc_html__( '+%d monedas', 'afectivalab' ),
						absint( $afectivalab_mision['recompensa'] )
					);
					?>
				</span>
			</div>

			<p class="mision-card__texto"><?php echo esc_html( $afectivalab_mision['texto'] ); ?></p>

			<?php if ( 'despues' === $afectivalab_mision_estado ) : ?>
				<p class="mision-card__aviso">
					<?php esc_html_e( 'La dejaste pendiente. Cuando la hagan, márcala aquí para seguir avanzando.', 'afectivalab' ); ?>
				</p>
			<?php endif; ?>

			<form method="post" class="mision-card__form" data-clase-ajax="mision" <?php echo $afectivalab_datos_form; // phpcs:ignore WordPress.Security.EscapeOutput -- solo enteros. ?> <?php echo $afectivalab_mision['requiere_evidencia'] ? 'enctype="multipart/form-data" data-requiere-foto' : ''; ?>>
				<?php wp_nonce_field( 'afectivalab_mision_' . $afectivalab_clase_id, 'afectivalab_mision_nonce', false ); ?>

				<div class="form-alert" role="alert" data-clase-alerta <?php echo afectivalab_mision_error() ? '' : 'hidden'; ?>><?php echo esc_html( afectivalab_mision_error() ); ?></div>

				<?php if ( $afectivalab_mision['requiere_evidencia'] ) : ?>
					<label class="mision-card__subir" data-mision-subir>
						<span class="mision-card__subir-icono" aria-hidden="true"><?php afectivalab_icon( 'camera' ); ?></span>
						<img class="mision-card__subir-preview" alt="" data-mision-preview hidden>
						<span class="mision-card__subir-texto">
							<strong data-mision-archivo><?php esc_html_e( 'Sube una foto como evidencia', 'afectivalab' ); ?></strong>
							<small data-mision-ayuda><?php esc_html_e( 'JPG, PNG o WEBP de hasta 3MB', 'afectivalab' ); ?></small>
						</span>
						<input type="file" name="evidencia" accept="image/jpeg,image/png,image/webp" required>
					</label>
				<?php endif; ?>

				<div class="mision-card__botones">
					<?php if ( 'despues' !== $afectivalab_mision_estado ) : ?>
						<button type="submit" name="afectivalab_mision_accion" value="despues" class="btn btn-secondary" formnovalidate>
							<?php esc_html_e( 'La haremos después', 'afectivalab' ); ?>
						</button>
					<?php endif; ?>

					<button type="submit" name="afectivalab_mision_accion" value="hecha" class="btn btn-primary btn-lg mision-card__cumplida">
						<?php afectivalab_icon( 'check' ); ?>
						<?php
						printf(
							/* translators: %d: monedas que otorga la misión. */
							esc_html__( 'Ya la hicimos · reclamar %d monedas', 'afectivalab' ),
							absint( $afectivalab_mision['recompensa'] )
						);
						?>
					</button>
				</div>
			</form>
		</section>

	<?php else : ?>
		<div class="clase-accion">
			<div class="clase-accion__texto">
				<strong><?php esc_html_e( '¿Ya vieron esta clase?', 'afectivalab' ); ?></strong>
				<p>
					<?php
					printf(
						/* translators: %s: nombre del hijo. */
						esc_html__( 'Al marcarla avanza la ruta de %s y se desbloquea la siguiente.', 'afectivalab' ),
						esc_html( $afectivalab_hijo ? $afectivalab_hijo->post_title : '' )
					);
					?>
				</p>
			</div>

			<form method="post" data-clase-ajax="vista" <?php echo $afectivalab_datos_form; // phpcs:ignore WordPress.Security.EscapeOutput -- solo enteros. ?>>
				<?php wp_nonce_field( 'afectivalab_clase_' . $afectivalab_clase_id, 'afectivalab_clase_nonce', false ); ?>
				<button type="submit" name="afectivalab_clase_hecha" value="1" class="btn btn-primary">
					<?php afectivalab_icon( 'check' ); ?>
					<?php esc_html_e( 'Marcar como vista', 'afectivalab' ); ?>
				</button>
				<div class="form-alert" role="alert" data-clase-alerta hidden></div>
			</form>
		</div>
	<?php endif; ?>

	<?php if ( ! $afectivalab_hecha ) : ?>
		<section class="clase-siguiente">
			<span class="clase-siguiente__icono" aria-hidden="true">
				<?php afectivalab_icon( $afectivalab_clase_luego ? 'lock' : 'juego-certificado' ); ?>
			</span>
			<div class="clase-siguiente__texto">
				<span><?php esc_html_e( '¿Qué desbloqueas a continuación?', 'afectivalab' ); ?></span>
				<strong>
					<?php
					if ( $afectivalab_clase_luego ) {
						printf(
							/* translators: 1: número de la clase, 2: título. */
							esc_html__( 'Clase %1$d: %2$s', 'afectivalab' ),
							absint( $afectivalab_numero + 1 ),
							esc_html( $afectivalab_clase_luego->post_title )
						);
					} else {
						esc_html_e( 'La insignia y el certificado del curso', 'afectivalab' );
					}
					?>
				</strong>
				<?php if ( ! $afectivalab_clase_luego && $afectivalab_habilidad ) : ?>
					<small>
						<?php
						printf(
							/* translators: %s: habilidad del curso. */
							esc_html__( 'Insignia "%s"', 'afectivalab' ),
							esc_html( $afectivalab_habilidad )
						);
						?>
					</small>
				<?php endif; ?>
			</div>
			<a class="btn btn-secondary" href="<?php echo esc_url( get_permalink( $afectivalab_curso_id ) ); ?>">
				<?php esc_html_e( 'Ver el mapa', 'afectivalab' ); ?>
				<?php afectivalab_icon( 'arrow-right' ); ?>
			</a>
		</section>
	<?php endif; ?>
</div>
