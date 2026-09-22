<?php
/**
 * Panel > Cursos: el listado.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_cursos = get_posts(
	array(
		'post_type'      => AFECTIVALAB_CPT_CURSO,
		'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);
?>

<div class="panel-seccion__head">
	<h2 class="panel-seccion__titulo"><?php esc_html_e( 'Cursos', 'afectivalab' ); ?></h2>
	<a class="btn btn-primary" href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'nuevo' ) ) ); ?>">
		<?php esc_html_e( 'Nuevo curso', 'afectivalab' ); ?>
	</a>
</div>

<?php if ( ! $afectivalab_cursos ) : ?>
	<div class="equipo-vacio">
		<?php afectivalab_icon( 'juego-mapa-desbloqueable', 'equipo-vacio__icon' ); ?>
		<h3><?php esc_html_e( 'Todavía no hay cursos', 'afectivalab' ); ?></h3>
		<p><?php esc_html_e( 'Crea el primero y después agrégale sus microclases.', 'afectivalab' ); ?></p>
	</div>
<?php else : ?>
	<ul class="tabla">
		<?php foreach ( $afectivalab_cursos as $afectivalab_curso ) : ?>
			<?php
			$afectivalab_eje    = afectivalab_eje_del_curso( $afectivalab_curso->ID );
			$afectivalab_etapas = get_the_terms( $afectivalab_curso->ID, AFECTIVALAB_TAX_ETAPA );
			$afectivalab_etapa  = ( $afectivalab_etapas && ! is_wp_error( $afectivalab_etapas ) ) ? reset( $afectivalab_etapas ) : null;
			$afectivalab_nclases = count( afectivalab_clases_del_curso( $afectivalab_curso->ID, array( 'publish', 'draft', 'pending', 'private' ) ) );
			?>
			<li class="tabla__fila">
				<div class="tabla__principal">
					<strong><?php echo esc_html( $afectivalab_curso->post_title ); ?></strong>
					<span class="tabla__meta">
						<?php if ( $afectivalab_etapa ) : ?>
							<?php echo esc_html( $afectivalab_etapa->name ); ?>
						<?php endif; ?>
						<?php if ( $afectivalab_eje ) : ?>
							<span aria-hidden="true">&middot;</span> <?php echo esc_html( $afectivalab_eje->name ); ?>
						<?php endif; ?>
						<span aria-hidden="true">&middot;</span>
						<?php
						printf(
							/* translators: %d: cantidad de microclases. */
							esc_html( _n( '%d microclase', '%d microclases', $afectivalab_nclases, 'afectivalab' ) ),
							absint( $afectivalab_nclases )
						);
						?>
					</span>
				</div>

				<span class="estado estado--<?php echo esc_attr( $afectivalab_curso->post_status ); ?>">
					<?php
					'publish' === $afectivalab_curso->post_status
						? esc_html_e( 'Publicado', 'afectivalab' )
						: esc_html_e( 'Borrador', 'afectivalab' );
					?>
				</span>

				<div class="tabla__acciones">
					<?php if ( current_user_can( 'edit_post', $afectivalab_curso->ID ) ) : ?>
						<a href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'editar', 'id' => $afectivalab_curso->ID ) ) ); ?>">
							<?php esc_html_e( 'Editar', 'afectivalab' ); ?>
						</a>
					<?php endif; ?>

					<a href="<?php echo esc_url( get_permalink( $afectivalab_curso ) ); ?>">
						<?php esc_html_e( 'Ver', 'afectivalab' ); ?>
					</a>

					<?php if ( current_user_can( 'delete_post', $afectivalab_curso->ID ) ) : ?>
						<form method="post" class="tabla__eliminar">
							<?php wp_nonce_field( 'afectivalab_panel_eliminar', 'afectivalab_panel_nonce' ); ?>
							<input type="hidden" name="afectivalab_panel_accion" value="eliminar_curso">
							<input type="hidden" name="post_id" value="<?php echo esc_attr( $afectivalab_curso->ID ); ?>">
							<button
								type="submit"
								class="tabla__eliminar-boton"
								data-confirm="<?php echo esc_attr( sprintf( __( '¿Enviar "%s" a la papelera? Sus microclases no se borran.', 'afectivalab' ), $afectivalab_curso->post_title ) ); ?>"
							>
								<?php esc_html_e( 'Eliminar', 'afectivalab' ); ?>
							</button>
						</form>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
