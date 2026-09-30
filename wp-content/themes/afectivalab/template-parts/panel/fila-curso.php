<?php
/**
 * Una fila del listado de cursos del panel.
 *
 * Los data-* son los que usa el buscador y los filtros del listado
 * (assets/js/equipo.js): texto sin tildes, estado y etapa.
 *
 * @var array $args { curso: WP_Post }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_curso   = $args['curso'];
$afectivalab_eje     = afectivalab_eje_del_curso( $afectivalab_curso->ID );
$afectivalab_etapas  = get_the_terms( $afectivalab_curso->ID, AFECTIVALAB_TAX_ETAPA );
$afectivalab_etapa   = ( $afectivalab_etapas && ! is_wp_error( $afectivalab_etapas ) ) ? reset( $afectivalab_etapas ) : null;
$afectivalab_nclases = count( afectivalab_clases_del_curso( $afectivalab_curso->ID, afectivalab_panel_estados_visibles() ) );
$afectivalab_editar  = afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'editar', 'id' => $afectivalab_curso->ID ) );
$afectivalab_puede   = current_user_can( 'edit_post', $afectivalab_curso->ID );
?>

<li
	class="eq-fila"
	data-eq-item
	data-texto="<?php echo esc_attr( remove_accents( mb_strtolower( $afectivalab_curso->post_title . ' ' . ( $afectivalab_eje ? $afectivalab_eje->name : '' ) ) ) ); ?>"
	data-estado="<?php echo 'publish' === $afectivalab_curso->post_status ? 'publish' : 'draft'; ?>"
	data-etapa="<?php echo esc_attr( $afectivalab_etapa ? $afectivalab_etapa->slug : '' ); ?>"
>
	<span class="eq-fila__miniatura">
		<?php if ( has_post_thumbnail( $afectivalab_curso->ID ) ) : ?>
			<?php echo get_the_post_thumbnail( $afectivalab_curso->ID, 'thumbnail', array( 'alt' => '' ) ); ?>
		<?php elseif ( $afectivalab_eje ) : ?>
			<?php afectivalab_icon( 'eje-' . $afectivalab_eje->slug ); ?>
		<?php endif; ?>
	</span>

	<div class="eq-fila__principal">
		<?php if ( $afectivalab_puede ) : ?>
			<a class="eq-fila__titulo" href="<?php echo esc_url( $afectivalab_editar ); ?>"><?php echo esc_html( $afectivalab_curso->post_title ); ?></a>
		<?php else : ?>
			<strong class="eq-fila__titulo"><?php echo esc_html( $afectivalab_curso->post_title ); ?></strong>
		<?php endif; ?>

		<span class="eq-fila__meta">
			<?php if ( $afectivalab_etapa ) : ?>
				<span class="eq-chip eq-chip--amarillo"><?php echo esc_html( $afectivalab_etapa->name ); ?></span>
			<?php endif; ?>
			<?php if ( $afectivalab_eje ) : ?>
				<span class="eq-chip"><?php echo esc_html( $afectivalab_eje->name ); ?></span>
			<?php endif; ?>
			<span class="eq-fila__dato<?php echo $afectivalab_nclases ? '' : ' is-alerta'; ?>">
				<?php
				echo $afectivalab_nclases
					? esc_html( sprintf( /* translators: %d: cantidad de microclases. */ _n( '%d microclase', '%d microclases', $afectivalab_nclases, 'afectivalab' ), $afectivalab_nclases ) )
					: esc_html__( 'Sin microclases', 'afectivalab' );
				?>
			</span>
		</span>
	</div>

	<?php get_template_part( 'template-parts/panel/estado-switch', null, array( 'post' => $afectivalab_curso ) ); ?>

	<div class="eq-fila__acciones">
		<?php if ( $afectivalab_puede ) : ?>
			<a class="eq-boton-icono" href="<?php echo esc_url( $afectivalab_editar ); ?>" title="<?php esc_attr_e( 'Editar', 'afectivalab' ); ?>">
				<?php afectivalab_icon( 'eq-editar' ); ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Editar', 'afectivalab' ); ?></span>
			</a>
		<?php endif; ?>

		<a class="eq-boton-icono" href="<?php echo esc_url( get_permalink( $afectivalab_curso ) ); ?>" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Ver como familia', 'afectivalab' ); ?>">
			<?php afectivalab_icon( 'eq-ver' ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Ver como familia', 'afectivalab' ); ?></span>
		</a>

		<?php if ( current_user_can( 'delete_post', $afectivalab_curso->ID ) ) : ?>
			<form method="post" data-eq-ajax="eliminar">
				<?php wp_nonce_field( 'afectivalab_panel_eliminar', 'afectivalab_panel_nonce', false ); ?>
				<input type="hidden" name="afectivalab_panel_accion" value="eliminar_curso">
				<input type="hidden" name="post_id" value="<?php echo esc_attr( $afectivalab_curso->ID ); ?>">
				<button
					type="submit"
					class="eq-boton-icono eq-boton-icono--peligro"
					title="<?php esc_attr_e( 'Enviar a la papelera', 'afectivalab' ); ?>"
					data-confirm="<?php echo esc_attr( sprintf( /* translators: %s: título del curso. */ __( '¿Enviar "%s" a la papelera? Sus microclases no se borran y se puede restaurar desde el escritorio.', 'afectivalab' ), $afectivalab_curso->post_title ) ); ?>"
					data-confirm-boton="<?php esc_attr_e( 'Sí, eliminar', 'afectivalab' ); ?>"
				>
					<?php afectivalab_icon( 'eq-papelera' ); ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Eliminar', 'afectivalab' ); ?></span>
				</button>
			</form>
		<?php endif; ?>
	</div>
</li>
