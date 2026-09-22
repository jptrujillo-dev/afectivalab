<?php
/**
 * Panel > Microclases: el listado, agrupado por curso.
 *
 * Agrupado y no plano a propósito: con decenas de clases de títulos parecidos
 * entre rutas, una lista corrida es imposible de manejar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_clases = get_posts(
	array(
		'post_type'      => AFECTIVALAB_CPT_CLASE,
		'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	)
);

$afectivalab_grupos = array();

foreach ( $afectivalab_clases as $afectivalab_clase ) {
	$afectivalab_curso_id = (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_curso', true );
	$afectivalab_grupos[ $afectivalab_curso_id ][] = $afectivalab_clase;
}
?>

<div class="panel-seccion__head">
	<h2 class="panel-seccion__titulo"><?php esc_html_e( 'Microclases', 'afectivalab' ); ?></h2>
	<a class="btn btn-primary" href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'clases', 'accion' => 'nuevo' ) ) ); ?>">
		<?php esc_html_e( 'Nueva microclase', 'afectivalab' ); ?>
	</a>
</div>

<?php if ( ! $afectivalab_clases ) : ?>
	<div class="equipo-vacio">
		<?php afectivalab_icon( 'leccion-video-principal', 'equipo-vacio__icon' ); ?>
		<h3><?php esc_html_e( 'Todavía no hay microclases', 'afectivalab' ); ?></h3>
		<p><?php esc_html_e( 'Crea la primera y asígnala a un curso.', 'afectivalab' ); ?></p>
	</div>
<?php else : ?>
	<?php foreach ( $afectivalab_grupos as $afectivalab_curso_id => $afectivalab_lista ) : ?>
		<section class="panel-subseccion">
			<h3 class="panel-seccion__titulo">
				<?php
				echo $afectivalab_curso_id
					? esc_html( get_the_title( $afectivalab_curso_id ) )
					: esc_html__( 'Sin curso asignado', 'afectivalab' );
				?>
			</h3>

			<?php if ( ! $afectivalab_curso_id ) : ?>
				<p class="equipo-nota"><?php esc_html_e( 'Estas microclases no aparecen en ninguna ruta hasta que se les asigne un curso.', 'afectivalab' ); ?></p>
			<?php endif; ?>

			<ul class="tabla">
				<?php foreach ( $afectivalab_lista as $afectivalab_clase ) : ?>
					<?php $afectivalab_duracion = (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_duracion', true ); ?>
					<li class="tabla__fila">
						<div class="tabla__principal">
							<strong><?php echo esc_html( $afectivalab_clase->post_title ); ?></strong>
							<span class="tabla__meta">
								<?php
								printf(
									/* translators: %d: número de orden. */
									esc_html__( 'Orden %d', 'afectivalab' ),
									absint( $afectivalab_clase->menu_order )
								);
								?>
								<?php if ( $afectivalab_duracion ) : ?>
									<span aria-hidden="true">&middot;</span>
									<?php
									printf(
										/* translators: %d: duración en minutos. */
										esc_html__( '%d min', 'afectivalab' ),
										absint( $afectivalab_duracion )
									);
									?>
								<?php endif; ?>
							</span>
						</div>

						<span class="estado estado--<?php echo esc_attr( $afectivalab_clase->post_status ); ?>">
							<?php
							'publish' === $afectivalab_clase->post_status
								? esc_html_e( 'Publicada', 'afectivalab' )
								: esc_html_e( 'Borrador', 'afectivalab' );
							?>
						</span>

						<div class="tabla__acciones">
							<?php if ( current_user_can( 'edit_post', $afectivalab_clase->ID ) ) : ?>
								<a href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'clases', 'accion' => 'editar', 'id' => $afectivalab_clase->ID ) ) ); ?>">
									<?php esc_html_e( 'Editar', 'afectivalab' ); ?>
								</a>
							<?php endif; ?>

							<?php if ( current_user_can( 'delete_post', $afectivalab_clase->ID ) ) : ?>
								<form method="post" class="tabla__eliminar">
									<?php wp_nonce_field( 'afectivalab_panel_eliminar', 'afectivalab_panel_nonce' ); ?>
									<input type="hidden" name="afectivalab_panel_accion" value="eliminar_clase">
									<input type="hidden" name="post_id" value="<?php echo esc_attr( $afectivalab_clase->ID ); ?>">
									<button
										type="submit"
										class="tabla__eliminar-boton"
										data-confirm="<?php echo esc_attr( sprintf( __( '¿Enviar "%s" a la papelera?', 'afectivalab' ), $afectivalab_clase->post_title ) ); ?>"
									>
										<?php esc_html_e( 'Eliminar', 'afectivalab' ); ?>
									</button>
								</form>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endforeach; ?>
<?php endif; ?>
