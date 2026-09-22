<?php
/**
 * Panel > Cursos: alta y edición.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_id    = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
$afectivalab_curso = $afectivalab_id ? afectivalab_panel_post_editable( $afectivalab_id, AFECTIVALAB_CPT_CURSO ) : null;

if ( $afectivalab_id && ! $afectivalab_curso ) {
	echo '<div class="form-alert" role="alert">' . esc_html__( 'No encontramos ese curso, o no puedes editarlo.', 'afectivalab' ) . '</div>';
	return;
}

// Si el formulario volvió con errores, se repintan los valores que la persona
// ya había escrito; si no, los del curso guardado.
$afectivalab_v = $args['estado']['valores'] ?? array();

if ( ! $afectivalab_v ) {
	$afectivalab_eje_actual   = $afectivalab_curso ? afectivalab_eje_del_curso( $afectivalab_curso->ID ) : null;
	$afectivalab_etapas_curso = $afectivalab_curso ? get_the_terms( $afectivalab_curso->ID, AFECTIVALAB_TAX_ETAPA ) : false;
	$afectivalab_etapa_actual = ( $afectivalab_etapas_curso && ! is_wp_error( $afectivalab_etapas_curso ) ) ? reset( $afectivalab_etapas_curso ) : null;

	$afectivalab_v = array(
		'titulo'    => $afectivalab_curso ? $afectivalab_curso->post_title : '',
		'resumen'   => $afectivalab_curso ? $afectivalab_curso->post_excerpt : '',
		'contenido' => $afectivalab_curso ? $afectivalab_curso->post_content : '',
		'etapa'     => $afectivalab_etapa_actual ? $afectivalab_etapa_actual->slug : '',
		'eje'       => $afectivalab_eje_actual ? $afectivalab_eje_actual->slug : '',
		'habilidad' => $afectivalab_curso ? get_post_meta( $afectivalab_curso->ID, '_afectivalab_habilidad', true ) : '',
		'estado'    => $afectivalab_curso ? $afectivalab_curso->post_status : 'draft',
	);
}
?>

<div class="panel-seccion__head">
	<h2 class="panel-seccion__titulo">
		<?php
		$afectivalab_curso
			? esc_html_e( 'Editar curso', 'afectivalab' )
			: esc_html_e( 'Nuevo curso', 'afectivalab' );
		?>
	</h2>
	<a class="btn btn-ghost" href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'cursos' ) ) ); ?>">
		<?php esc_html_e( 'Volver al listado', 'afectivalab' ); ?>
	</a>
</div>

<form method="post" class="panel-form" enctype="multipart/form-data">
	<?php wp_nonce_field( 'afectivalab_panel_curso', 'afectivalab_panel_nonce' ); ?>
	<input type="hidden" name="afectivalab_panel_accion" value="guardar_curso">
	<input type="hidden" name="curso_id" value="<?php echo esc_attr( $afectivalab_curso ? $afectivalab_curso->ID : 0 ); ?>">

	<div class="form-field">
		<label for="curso-titulo"><?php esc_html_e( 'Título del curso', 'afectivalab' ); ?></label>
		<div class="form-input">
			<input type="text" id="curso-titulo" name="titulo" value="<?php echo esc_attr( $afectivalab_v['titulo'] ); ?>" required>
		</div>
	</div>

	<div class="form-row-split">
		<div class="form-field">
			<label for="curso-etapa"><?php esc_html_e( 'Etapa de edad', 'afectivalab' ); ?></label>
			<div class="form-input">
				<select id="curso-etapa" name="etapa" required>
					<option value=""><?php esc_html_e( 'Elige una etapa', 'afectivalab' ); ?></option>
					<?php foreach ( afectivalab_etapas() as $afectivalab_slug => $afectivalab_etapa ) : ?>
						<option value="<?php echo esc_attr( $afectivalab_slug ); ?>" <?php selected( $afectivalab_v['etapa'], $afectivalab_slug ); ?>>
							<?php
							printf(
								'%1$s (%2$d–%3$d)',
								esc_html( $afectivalab_etapa['nombre'] ),
								absint( $afectivalab_etapa['edad_min'] ),
								absint( $afectivalab_etapa['edad_max'] )
							);
							?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="form-field">
			<label for="curso-eje"><?php esc_html_e( 'Eje temático', 'afectivalab' ); ?></label>
			<div class="form-input">
				<select id="curso-eje" name="eje" required>
					<option value=""><?php esc_html_e( 'Elige un eje', 'afectivalab' ); ?></option>
					<?php foreach ( afectivalab_ejes() as $afectivalab_slug => $afectivalab_eje ) : ?>
						<option value="<?php echo esc_attr( $afectivalab_slug ); ?>" <?php selected( $afectivalab_v['eje'], $afectivalab_slug ); ?>>
							<?php echo esc_html( $afectivalab_eje['nombre'] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>

	<div class="form-field">
		<label for="curso-habilidad"><?php esc_html_e( 'Habilidad adquirida', 'afectivalab' ); ?></label>
		<div class="form-input">
			<input type="text" id="curso-habilidad" name="habilidad" value="<?php echo esc_attr( $afectivalab_v['habilidad'] ); ?>" placeholder="<?php esc_attr_e( 'Ej. Prevención y manejo del bullying', 'afectivalab' ); ?>">
		</div>
		<p class="form-hint"><?php esc_html_e( 'Es lo que gana el padre al terminar: da nombre a su insignia y a su certificado.', 'afectivalab' ); ?></p>
	</div>

	<div class="form-field">
		<label for="curso-resumen"><?php esc_html_e( 'Resumen', 'afectivalab' ); ?></label>
		<div class="form-input">
			<textarea id="curso-resumen" name="resumen" rows="2"><?php echo esc_textarea( $afectivalab_v['resumen'] ); ?></textarea>
		</div>
		<p class="form-hint"><?php esc_html_e( 'Una línea que aparece bajo el título del curso.', 'afectivalab' ); ?></p>
	</div>

	<div class="form-field">
		<label for="curso_contenido"><?php esc_html_e( 'Descripción', 'afectivalab' ); ?></label>
		<?php
		// El id no lleva guiones a propósito: el editor visual de WordPress
		// no arranca si el id del textarea tiene guiones.
		wp_editor(
			$afectivalab_v['contenido'],
			'curso_contenido',
			array(
				'textarea_name' => 'contenido',
				'textarea_rows' => 10,
				'media_buttons' => false,
				// teeny deja solo lo que hace falta aquí: negrita, cursiva,
				// listas, enlaces y citas.
				'teeny'         => true,
				'quicktags'     => true,
			)
		);
		?>
	</div>

	<div class="form-field">
		<label for="curso-imagen"><?php esc_html_e( 'Imagen destacada', 'afectivalab' ); ?></label>

		<?php if ( $afectivalab_curso && has_post_thumbnail( $afectivalab_curso->ID ) ) : ?>
			<div class="imagen-actual">
				<?php echo get_the_post_thumbnail( $afectivalab_curso->ID, 'medium' ); ?>
				<label class="form-field__radio">
					<input type="checkbox" name="quitar_imagen" value="1">
					<?php esc_html_e( 'Quitar esta imagen', 'afectivalab' ); ?>
				</label>
			</div>
		<?php endif; ?>

		<input type="file" id="curso-imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
		<p class="form-hint"><?php esc_html_e( 'JPG, PNG o WEBP. Se ve arriba del curso, antes del camino de clases.', 'afectivalab' ); ?></p>
	</div>

	<?php if ( current_user_can( 'publish_afectivalab_cursos' ) ) : ?>
		<fieldset class="form-fieldset">
			<legend><?php esc_html_e( 'Estado', 'afectivalab' ); ?></legend>
			<label class="form-field__radio">
				<input type="radio" name="estado" value="draft" <?php checked( $afectivalab_v['estado'], 'draft' ); ?>>
				<?php esc_html_e( 'Borrador — solo lo ve el equipo', 'afectivalab' ); ?>
			</label>
			<label class="form-field__radio">
				<input type="radio" name="estado" value="publish" <?php checked( $afectivalab_v['estado'], 'publish' ); ?>>
				<?php esc_html_e( 'Publicado — entra en las rutas de las familias', 'afectivalab' ); ?>
			</label>
		</fieldset>
	<?php else : ?>
		<input type="hidden" name="estado" value="draft">
		<p class="form-hint"><?php esc_html_e( 'Se guardará como borrador: no tienes permiso para publicar.', 'afectivalab' ); ?></p>
	<?php endif; ?>

	<div class="panel-form__acciones">
		<button type="submit" class="btn btn-primary">
			<?php
			$afectivalab_curso
				? esc_html_e( 'Guardar cambios', 'afectivalab' )
				: esc_html_e( 'Crear curso', 'afectivalab' );
			?>
		</button>

		<?php if ( $afectivalab_curso ) : ?>
			<a class="btn btn-ghost" href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'clases', 'accion' => 'nuevo', 'curso' => $afectivalab_curso->ID ) ) ); ?>">
				<?php esc_html_e( 'Añadir microclase', 'afectivalab' ); ?>
			</a>
		<?php endif; ?>
	</div>
</form>

<?php if ( $afectivalab_curso ) : ?>
	<?php $afectivalab_clases = afectivalab_clases_del_curso( $afectivalab_curso->ID, array( 'publish', 'draft', 'pending', 'private' ) ); ?>

	<section class="panel-subseccion">
		<h3 class="panel-seccion__titulo"><?php esc_html_e( 'Microclases de este curso', 'afectivalab' ); ?></h3>

		<?php if ( ! $afectivalab_clases ) : ?>
			<p class="equipo-nota"><?php esc_html_e( 'Todavía no tiene ninguna. Las familias lo verán como "en preparación".', 'afectivalab' ); ?></p>
		<?php else : ?>
			<ul class="tabla">
				<?php foreach ( $afectivalab_clases as $afectivalab_clase ) : ?>
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
							<a href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'clases', 'accion' => 'editar', 'id' => $afectivalab_clase->ID ) ) ); ?>">
								<?php esc_html_e( 'Editar', 'afectivalab' ); ?>
							</a>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</section>
<?php endif; ?>
