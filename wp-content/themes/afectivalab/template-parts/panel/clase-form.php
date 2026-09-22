<?php
/**
 * Panel > Microclases: alta y edición.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_id    = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
$afectivalab_clase = $afectivalab_id ? afectivalab_panel_post_editable( $afectivalab_id, AFECTIVALAB_CPT_CLASE ) : null;

if ( $afectivalab_id && ! $afectivalab_clase ) {
	echo '<div class="form-alert" role="alert">' . esc_html__( 'No encontramos esa microclase, o no puedes editarla.', 'afectivalab' ) . '</div>';
	return;
}

$afectivalab_v = $args['estado']['valores'] ?? array();

if ( ! $afectivalab_v ) {
	$afectivalab_v = array(
		'titulo'     => $afectivalab_clase ? $afectivalab_clase->post_title : '',
		// Al llegar desde "Añadir microclase" de un curso, viene preelegido.
		'curso'      => $afectivalab_clase
			? (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_curso', true )
			: ( isset( $_GET['curso'] ) ? absint( $_GET['curso'] ) : 0 ),
		'orden'      => $afectivalab_clase ? (int) $afectivalab_clase->menu_order : 0,
		'duracion'   => $afectivalab_clase ? (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_duracion', true ) : 0,
		'video_tipo' => $afectivalab_clase ? get_post_meta( $afectivalab_clase->ID, '_afectivalab_video_tipo', true ) : 'ninguno',
		'video_url'  => $afectivalab_clase ? get_post_meta( $afectivalab_clase->ID, '_afectivalab_video_url', true ) : '',
		'contenido'  => $afectivalab_clase ? $afectivalab_clase->post_content : '',
		'mision_tipo'  => $afectivalab_clase ? get_post_meta( $afectivalab_clase->ID, '_afectivalab_mision_tipo', true ) : 'ninguna',
		'mision_texto' => $afectivalab_clase ? get_post_meta( $afectivalab_clase->ID, '_afectivalab_mision_texto', true ) : '',
		'estado'     => $afectivalab_clase ? $afectivalab_clase->post_status : 'draft',
	);
}

if ( empty( $afectivalab_v['video_tipo'] ) ) {
	$afectivalab_v['video_tipo'] = 'ninguno';
}

if ( empty( $afectivalab_v['mision_tipo'] ) ) {
	$afectivalab_v['mision_tipo'] = 'ninguna';
}

$afectivalab_video_id = $afectivalab_clase ? (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_video_id', true ) : 0;

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
	<h2 class="panel-seccion__titulo">
		<?php
		$afectivalab_clase
			? esc_html_e( 'Editar microclase', 'afectivalab' )
			: esc_html_e( 'Nueva microclase', 'afectivalab' );
		?>
	</h2>
	<a class="btn btn-ghost" href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'clases' ) ) ); ?>">
		<?php esc_html_e( 'Volver al listado', 'afectivalab' ); ?>
	</a>
</div>

<form method="post" class="panel-form" enctype="multipart/form-data">
	<?php wp_nonce_field( 'afectivalab_panel_clase', 'afectivalab_panel_nonce' ); ?>
	<input type="hidden" name="afectivalab_panel_accion" value="guardar_clase">
	<input type="hidden" name="clase_id" value="<?php echo esc_attr( $afectivalab_clase ? $afectivalab_clase->ID : 0 ); ?>">

	<div class="form-field">
		<label for="clase-titulo"><?php esc_html_e( 'Título de la microclase', 'afectivalab' ); ?></label>
		<div class="form-input">
			<input type="text" id="clase-titulo" name="titulo" value="<?php echo esc_attr( $afectivalab_v['titulo'] ); ?>" required>
		</div>
	</div>

	<div class="form-field">
		<label for="clase-curso"><?php esc_html_e( 'Curso al que pertenece', 'afectivalab' ); ?></label>
		<div class="form-input">
			<select id="clase-curso" name="curso">
				<option value="0"><?php esc_html_e( 'Sin asignar', 'afectivalab' ); ?></option>
				<?php foreach ( $afectivalab_cursos as $afectivalab_curso ) : ?>
					<option value="<?php echo esc_attr( $afectivalab_curso->ID ); ?>" <?php selected( $afectivalab_v['curso'], $afectivalab_curso->ID ); ?>>
						<?php echo esc_html( $afectivalab_curso->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="form-row-split">
		<div class="form-field">
			<label for="clase-orden"><?php esc_html_e( 'Orden dentro del curso', 'afectivalab' ); ?></label>
			<div class="form-input">
				<input type="number" id="clase-orden" name="orden" value="<?php echo esc_attr( $afectivalab_v['orden'] ); ?>" min="0" step="1">
			</div>
		</div>

		<div class="form-field">
			<label for="clase-duracion"><?php esc_html_e( 'Duración en minutos', 'afectivalab' ); ?></label>
			<div class="form-input">
				<input type="number" id="clase-duracion" name="duracion" value="<?php echo esc_attr( $afectivalab_v['duracion'] ? $afectivalab_v['duracion'] : '' ); ?>" min="1" max="120" step="1">
			</div>
		</div>
	</div>

	<fieldset class="form-fieldset">
		<legend><?php esc_html_e( 'Video principal', 'afectivalab' ); ?></legend>

		<label class="form-field__radio">
			<input type="radio" name="video_tipo" value="ninguno" <?php checked( $afectivalab_v['video_tipo'], 'ninguno' ); ?>>
			<?php esc_html_e( 'Esta clase no lleva video', 'afectivalab' ); ?>
		</label>

		<label class="form-field__radio">
			<input type="radio" name="video_tipo" value="url" <?php checked( $afectivalab_v['video_tipo'], 'url' ); ?>>
			<?php esc_html_e( 'Enlace de YouTube o Vimeo', 'afectivalab' ); ?>
		</label>

		<div class="form-field__sub" data-video-panel="url">
			<div class="form-input">
				<input type="url" name="video_url" value="<?php echo esc_attr( $afectivalab_v['video_url'] ); ?>" placeholder="https://www.youtube.com/watch?v=...">
			</div>
		</div>

		<label class="form-field__radio">
			<input type="radio" name="video_tipo" value="media" <?php checked( $afectivalab_v['video_tipo'], 'media' ); ?>>
			<?php esc_html_e( 'Subir un archivo de video', 'afectivalab' ); ?>
		</label>

		<div class="form-field__sub" data-video-panel="media">
			<?php if ( $afectivalab_video_id ) : ?>
				<p class="form-hint">
					<?php
					printf(
						/* translators: %s: nombre del archivo subido. */
						esc_html__( 'Archivo actual: %s. Si eliges otro, lo reemplaza.', 'afectivalab' ),
						esc_html( get_the_title( $afectivalab_video_id ) )
					);
					?>
				</p>
			<?php endif; ?>
			<input type="file" name="video_archivo" accept="video/*">
			<p class="form-hint"><?php esc_html_e( 'Para videos largos conviene usar YouTube o Vimeo: pesan menos en el hosting y se ven mejor en móvil.', 'afectivalab' ); ?></p>
		</div>
	</fieldset>

	<fieldset class="form-fieldset">
		<legend><?php esc_html_e( 'Misión', 'afectivalab' ); ?></legend>
		<p class="form-hint">
			<?php esc_html_e( 'La acción fuera de la pantalla que hace el padre con su hijo. Si la clase lleva misión, resolverla es lo que la completa: ver el video no alcanza.', 'afectivalab' ); ?>
		</p>

		<label class="form-field__radio">
			<input type="radio" name="mision_tipo" value="ninguna" <?php checked( $afectivalab_v['mision_tipo'], 'ninguna' ); ?>>
			<?php esc_html_e( 'Esta clase no lleva misión', 'afectivalab' ); ?>
		</label>

		<?php foreach ( afectivalab_mision_tipos() as $afectivalab_slug => $afectivalab_tipo ) : ?>
			<label class="form-field__radio">
				<input type="radio" name="mision_tipo" value="<?php echo esc_attr( $afectivalab_slug ); ?>" <?php checked( $afectivalab_v['mision_tipo'], $afectivalab_slug ); ?>>
				<?php echo esc_html( $afectivalab_tipo['nombre'] ); ?>
				<?php if ( $afectivalab_tipo['requiere_evidencia'] ) : ?>
					<span class="form-hint-inline"><?php esc_html_e( '(el padre sube una foto)', 'afectivalab' ); ?></span>
				<?php endif; ?>
				—
				<?php
				printf(
					/* translators: %d: monedas que otorga. */
					esc_html__( '%d monedas', 'afectivalab' ),
					absint( $afectivalab_tipo['recompensa'] )
				);
				?>
			</label>
		<?php endforeach; ?>

		<div class="form-field__sub" data-mision-panel="casa,taller">
			<div class="form-input">
				<textarea name="mision_texto" rows="2" placeholder="<?php esc_attr_e( 'Ej. Pregúntale a tu hijo: ¿qué fue lo que mejor hiciste esta semana aunque te haya costado?', 'afectivalab' ); ?>"><?php echo esc_textarea( $afectivalab_v['mision_texto'] ); ?></textarea>
			</div>
		</div>
	</fieldset>

	<div class="form-field">
		<label for="clase-imagen"><?php esc_html_e( 'Imagen destacada', 'afectivalab' ); ?></label>

		<?php if ( $afectivalab_clase && has_post_thumbnail( $afectivalab_clase->ID ) ) : ?>
			<div class="imagen-actual">
				<?php echo get_the_post_thumbnail( $afectivalab_clase->ID, 'medium' ); ?>
				<label class="form-field__radio">
					<input type="checkbox" name="quitar_imagen" value="1">
					<?php esc_html_e( 'Quitar esta imagen', 'afectivalab' ); ?>
				</label>
			</div>
		<?php endif; ?>

		<input type="file" id="clase-imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
		<p class="form-hint"><?php esc_html_e( 'Si la clase lleva video subido, se usa como portada antes de darle play. Si no lleva video, se muestra en su lugar.', 'afectivalab' ); ?></p>
	</div>

	<div class="form-field">
		<label for="clase_contenido"><?php esc_html_e( 'Contenido de la clase', 'afectivalab' ); ?></label>
		<?php
		// El id no lleva guiones a propósito: el editor visual de WordPress
		// no arranca si el id del textarea tiene guiones.
		wp_editor(
			$afectivalab_v['contenido'],
			'clase_contenido',
			array(
				'textarea_name' => 'contenido',
				'textarea_rows' => 12,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => true,
			)
		);
		?>
	</div>

	<?php if ( current_user_can( 'publish_afectivalab_clases' ) ) : ?>
		<fieldset class="form-fieldset">
			<legend><?php esc_html_e( 'Estado', 'afectivalab' ); ?></legend>
			<label class="form-field__radio">
				<input type="radio" name="estado" value="draft" <?php checked( $afectivalab_v['estado'], 'draft' ); ?>>
				<?php esc_html_e( 'Borrador — solo lo ve el equipo', 'afectivalab' ); ?>
			</label>
			<label class="form-field__radio">
				<input type="radio" name="estado" value="publish" <?php checked( $afectivalab_v['estado'], 'publish' ); ?>>
				<?php esc_html_e( 'Publicada — entra en el camino del curso', 'afectivalab' ); ?>
			</label>
		</fieldset>
	<?php else : ?>
		<input type="hidden" name="estado" value="draft">
		<p class="form-hint"><?php esc_html_e( 'Se guardará como borrador: no tienes permiso para publicar.', 'afectivalab' ); ?></p>
	<?php endif; ?>

	<div class="panel-form__acciones">
		<button type="submit" class="btn btn-primary">
			<?php
			$afectivalab_clase
				? esc_html_e( 'Guardar cambios', 'afectivalab' )
				: esc_html_e( 'Crear microclase', 'afectivalab' );
			?>
		</button>
	</div>
</form>
