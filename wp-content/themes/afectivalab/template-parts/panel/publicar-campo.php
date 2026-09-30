<?php
/**
 * Barra lateral del editor: estado, guardar y acciones del contenido.
 *
 * Los campos van asociados al formulario principal con form=. Eliminar es
 * un formulario aparte (no puede ir dentro del principal).
 *
 * @var array $args {
 *     post:   WP_Post|null,
 *     form:   string  id del formulario principal,
 *     estado: string  'publish' o 'draft' (lo que hay que marcar),
 *     tipo:   string  'curso' o 'clase',
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_post     = $args['post'] ?? null;
$afectivalab_form     = $args['form'];
$afectivalab_es_curso = 'curso' === $args['tipo'];
$afectivalab_publica  = current_user_can( $afectivalab_es_curso ? 'publish_afectivalab_cursos' : 'publish_afectivalab_clases' );
$afectivalab_estado   = 'publish' === $args['estado'] ? 'publish' : 'draft';
?>

<section class="eq-card eq-card--publicar">
	<header class="eq-card__head eq-card__head--simple">
		<h3><?php esc_html_e( 'Publicación', 'afectivalab' ); ?></h3>
		<span class="eq-guardado" data-eq-guardado aria-live="polite">
			<?php echo $afectivalab_post ? esc_html( afectivalab_panel_hace( $afectivalab_post->ID ) ) : esc_html__( 'Sin guardar todavía', 'afectivalab' ); ?>
		</span>
	</header>

	<?php if ( $afectivalab_publica ) : ?>
		<fieldset class="eq-segmento">
			<legend class="screen-reader-text"><?php esc_html_e( 'Estado', 'afectivalab' ); ?></legend>
			<label>
				<input type="radio" name="estado" value="draft" form="<?php echo esc_attr( $afectivalab_form ); ?>" <?php checked( $afectivalab_estado, 'draft' ); ?>>
				<span><?php esc_html_e( 'Borrador', 'afectivalab' ); ?></span>
			</label>
			<label>
				<input type="radio" name="estado" value="publish" form="<?php echo esc_attr( $afectivalab_form ); ?>" <?php checked( $afectivalab_estado, 'publish' ); ?>>
				<span><?php echo $afectivalab_es_curso ? esc_html__( 'Publicado', 'afectivalab' ) : esc_html__( 'Publicada', 'afectivalab' ); ?></span>
			</label>
		</fieldset>
		<p class="eq-ayuda" data-eq-estado-ayuda>
			<?php
			echo $afectivalab_es_curso
				? esc_html__( 'Borrador: solo lo ve el equipo. Publicado: entra en las rutas de las familias.', 'afectivalab' )
				: esc_html__( 'Borrador: solo la ve el equipo. Publicada: aparece en el camino del curso.', 'afectivalab' );
			?>
		</p>
	<?php else : ?>
		<input type="hidden" name="estado" value="draft" form="<?php echo esc_attr( $afectivalab_form ); ?>">
		<p class="eq-ayuda"><?php esc_html_e( 'Se guardará como borrador: publicar lo hace un administrador.', 'afectivalab' ); ?></p>
	<?php endif; ?>

	<button type="submit" form="<?php echo esc_attr( $afectivalab_form ); ?>" class="btn btn-primary eq-guardar" data-eq-guardar>
		<?php
		if ( $afectivalab_post ) {
			esc_html_e( 'Guardar cambios', 'afectivalab' );
		} else {
			$afectivalab_es_curso ? esc_html_e( 'Crear curso', 'afectivalab' ) : esc_html_e( 'Crear microclase', 'afectivalab' );
		}
		?>
	</button>
	<p class="eq-atajo"><?php esc_html_e( 'Atajo: Ctrl + S', 'afectivalab' ); ?></p>

	<div class="eq-card__pie">
		<a class="eq-enlace" href="<?php echo esc_url( $afectivalab_post ? get_permalink( $afectivalab_post ) : '#' ); ?>" target="_blank" rel="noopener" data-eq-ver<?php echo $afectivalab_post ? '' : ' hidden'; ?>>
			<?php afectivalab_icon( 'eq-ver' ); ?>
			<?php esc_html_e( 'Ver como familia', 'afectivalab' ); ?>
		</a>

		<?php if ( $afectivalab_post && current_user_can( 'delete_post', $afectivalab_post->ID ) ) : ?>
			<form method="post" data-eq-ajax="eliminar" data-eq-despues="lista">
				<?php wp_nonce_field( 'afectivalab_panel_eliminar', 'afectivalab_panel_nonce', false ); ?>
				<input type="hidden" name="afectivalab_panel_accion" value="<?php echo $afectivalab_es_curso ? 'eliminar_curso' : 'eliminar_clase'; ?>">
				<input type="hidden" name="post_id" value="<?php echo esc_attr( $afectivalab_post->ID ); ?>">
				<button
					type="submit"
					class="eq-enlace eq-enlace--peligro"
					data-confirm="<?php echo esc_attr( $afectivalab_es_curso ? __( '¿Enviar este curso a la papelera? Sus microclases no se borran y se puede restaurar desde el escritorio.', 'afectivalab' ) : __( '¿Enviar esta microclase a la papelera? Se puede restaurar desde el escritorio.', 'afectivalab' ) ); ?>"
					data-confirm-boton="<?php esc_attr_e( 'Sí, eliminar', 'afectivalab' ); ?>"
				>
					<?php afectivalab_icon( 'eq-papelera' ); ?>
					<?php esc_html_e( 'Enviar a la papelera', 'afectivalab' ); ?>
				</button>
			</form>
		<?php endif; ?>
	</div>
</section>
