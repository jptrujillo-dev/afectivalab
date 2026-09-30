<?php
/**
 * Una fila de microclase del panel.
 *
 * Dos contextos:
 * - 'lista': el listado de Microclases (con los data-* del buscador).
 * - 'curso': la lista dentro de la edición de un curso, con los botones
 *   para subirla o bajarla de lugar.
 *
 * @var array $args { clase: WP_Post, contexto: string }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_clase    = $args['clase'];
$afectivalab_contexto = $args['contexto'] ?? 'lista';
$afectivalab_curso_id = (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_curso', true );
$afectivalab_duracion = (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_duracion', true );
$afectivalab_video    = get_post_meta( $afectivalab_clase->ID, '_afectivalab_video_tipo', true );
$afectivalab_mision   = afectivalab_clase_mision( $afectivalab_clase->ID );
$afectivalab_caso     = '' !== (string) get_post_meta( $afectivalab_clase->ID, '_afectivalab_caso_paso1_situacion', true );
$afectivalab_editar   = afectivalab_panel_url( array( 'seccion' => 'clases', 'accion' => 'editar', 'id' => $afectivalab_clase->ID ) );
$afectivalab_puede    = current_user_can( 'edit_post', $afectivalab_clase->ID );
?>

<li
	class="eq-fila eq-fila--clase"
	data-eq-item
	data-eq-clase="<?php echo esc_attr( $afectivalab_clase->ID ); ?>"
	data-texto="<?php echo esc_attr( remove_accents( mb_strtolower( $afectivalab_clase->post_title ) ) ); ?>"
	data-estado="<?php echo 'publish' === $afectivalab_clase->post_status ? 'publish' : 'draft'; ?>"
	data-curso="<?php echo esc_attr( $afectivalab_curso_id ); ?>"
>
	<span class="eq-fila__orden" data-eq-numero><?php echo absint( $afectivalab_clase->menu_order ); ?></span>

	<div class="eq-fila__principal">
		<?php if ( $afectivalab_puede ) : ?>
			<a class="eq-fila__titulo" href="<?php echo esc_url( $afectivalab_editar ); ?>"><?php echo esc_html( $afectivalab_clase->post_title ); ?></a>
		<?php else : ?>
			<strong class="eq-fila__titulo"><?php echo esc_html( $afectivalab_clase->post_title ); ?></strong>
		<?php endif; ?>

		<span class="eq-fila__meta">
			<?php if ( $afectivalab_duracion ) : ?>
				<span class="eq-fila__dato"><?php echo esc_html( sprintf( /* translators: %d: minutos. */ __( '%d min', 'afectivalab' ), $afectivalab_duracion ) ); ?></span>
			<?php endif; ?>

			<?php if ( in_array( $afectivalab_video, array( 'url', 'media' ), true ) ) : ?>
				<span class="eq-chip eq-chip--icono" title="<?php esc_attr_e( 'Lleva video', 'afectivalab' ); ?>"><?php afectivalab_icon( 'eq-play' ); ?><?php esc_html_e( 'Video', 'afectivalab' ); ?></span>
			<?php endif; ?>

			<?php if ( $afectivalab_mision ) : ?>
				<span class="eq-chip eq-chip--icono eq-chip--verde" title="<?php echo esc_attr( $afectivalab_mision['nombre'] ); ?>"><?php afectivalab_icon( 'juego-monedas' ); ?><?php echo esc_html( sprintf( /* translators: %d: monedas. */ __( 'Misión +%d', 'afectivalab' ), $afectivalab_mision['recompensa'] ) ); ?></span>
			<?php endif; ?>

			<?php if ( $afectivalab_caso ) : ?>
				<span class="eq-chip eq-chip--icono eq-chip--morado" title="<?php esc_attr_e( 'Lleva caso interactivo', 'afectivalab' ); ?>"><?php esc_html_e( 'Caso', 'afectivalab' ); ?></span>
			<?php endif; ?>
		</span>
	</div>

	<?php get_template_part( 'template-parts/panel/estado-switch', null, array( 'post' => $afectivalab_clase ) ); ?>

	<div class="eq-fila__acciones">
		<?php if ( 'curso' === $afectivalab_contexto && $afectivalab_puede ) : ?>
			<?php foreach ( array( 'arriba' => __( 'Subir un lugar', 'afectivalab' ), 'abajo' => __( 'Bajar un lugar', 'afectivalab' ) ) as $afectivalab_dir => $afectivalab_txt ) : ?>
				<form method="post" data-eq-ajax="mover">
					<?php wp_nonce_field( 'afectivalab_panel_orden', 'afectivalab_panel_nonce', false ); ?>
					<input type="hidden" name="afectivalab_panel_accion" value="mover_clase">
					<input type="hidden" name="clase_id" value="<?php echo esc_attr( $afectivalab_clase->ID ); ?>">
					<input type="hidden" name="direccion" value="<?php echo esc_attr( $afectivalab_dir ); ?>">
					<button type="submit" class="eq-boton-icono eq-boton-icono--<?php echo esc_attr( $afectivalab_dir ); ?>" title="<?php echo esc_attr( $afectivalab_txt ); ?>" data-eq-mover="<?php echo esc_attr( $afectivalab_dir ); ?>">
						<?php afectivalab_icon( 'eq-flecha' ); ?>
						<span class="screen-reader-text"><?php echo esc_html( $afectivalab_txt ); ?></span>
					</button>
				</form>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if ( $afectivalab_puede ) : ?>
			<a class="eq-boton-icono" href="<?php echo esc_url( $afectivalab_editar ); ?>" title="<?php esc_attr_e( 'Editar', 'afectivalab' ); ?>">
				<?php afectivalab_icon( 'eq-editar' ); ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Editar', 'afectivalab' ); ?></span>
			</a>
		<?php endif; ?>

		<?php if ( 'lista' === $afectivalab_contexto && current_user_can( 'delete_post', $afectivalab_clase->ID ) ) : ?>
			<form method="post" data-eq-ajax="eliminar">
				<?php wp_nonce_field( 'afectivalab_panel_eliminar', 'afectivalab_panel_nonce', false ); ?>
				<input type="hidden" name="afectivalab_panel_accion" value="eliminar_clase">
				<input type="hidden" name="post_id" value="<?php echo esc_attr( $afectivalab_clase->ID ); ?>">
				<button
					type="submit"
					class="eq-boton-icono eq-boton-icono--peligro"
					title="<?php esc_attr_e( 'Enviar a la papelera', 'afectivalab' ); ?>"
					data-confirm="<?php echo esc_attr( sprintf( /* translators: %s: título de la microclase. */ __( '¿Enviar "%s" a la papelera? Se puede restaurar desde el escritorio.', 'afectivalab' ), $afectivalab_clase->post_title ) ); ?>"
					data-confirm-boton="<?php esc_attr_e( 'Sí, eliminar', 'afectivalab' ); ?>"
				>
					<?php afectivalab_icon( 'eq-papelera' ); ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Eliminar', 'afectivalab' ); ?></span>
				</button>
			</form>
		<?php endif; ?>
	</div>
</li>
