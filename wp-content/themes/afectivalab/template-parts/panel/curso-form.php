<?php
/**
 * Panel > Cursos: alta y edición.
 *
 * Dos columnas: el contenido a la izquierda, y a la derecha lo que se hace
 * con él (estado, guardar, imagen). Guarda por AJAX sin salir de la página
 * (assets/js/equipo.js); sin JavaScript el formulario se envía normal.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_estado_form = $args['estado'] ?? array();

// Si un envío sin JavaScript guardó el curso pero falló la imagen, llega el
// id en el estado: se sigue editando ese curso, no uno nuevo.
$afectivalab_id    = ! empty( $afectivalab_estado_form['id'] ) ? (int) $afectivalab_estado_form['id'] : ( isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0 );
$afectivalab_curso = $afectivalab_id ? afectivalab_panel_post_editable( $afectivalab_id, AFECTIVALAB_CPT_CURSO ) : null;

if ( $afectivalab_id && ! $afectivalab_curso ) {
	echo '<div class="eq-alerta" role="alert">' . esc_html__( 'No encontramos ese curso, o no puedes editarlo.', 'afectivalab' ) . '</div>';
	return;
}

// Si el formulario volvió con errores, se repintan los valores que la persona
// ya había escrito; si no, los del curso guardado.
$afectivalab_v = $afectivalab_estado_form['valores'] ?? array();

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

$afectivalab_form   = 'eq-form-curso';
$afectivalab_errors = $afectivalab_estado_form['errors'] ?? array();
$afectivalab_on     = $afectivalab_curso && 'publish' === $afectivalab_curso->post_status;
?>

<div class="eq-editor" data-eq-editor="curso">
	<div class="eq-editor__barra">
		<a class="eq-volver" href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'cursos' ) ) ); ?>">
			<?php afectivalab_icon( 'arrow-right', 'eq-volver__icono' ); ?>
			<?php esc_html_e( 'Cursos', 'afectivalab' ); ?>
		</a>

		<div class="eq-editor__titulo">
			<span class="eq-editor__tipo"><?php echo $afectivalab_curso ? esc_html__( 'Editando curso', 'afectivalab' ) : esc_html__( 'Nuevo curso', 'afectivalab' ); ?></span>
			<h2 data-eq-titulo data-vacio="<?php esc_attr_e( 'Curso sin título', 'afectivalab' ); ?>">
				<?php echo esc_html( $afectivalab_curso ? $afectivalab_curso->post_title : __( 'Curso sin título', 'afectivalab' ) ); ?>
			</h2>
		</div>

		<span class="eq-estado<?php echo $afectivalab_on ? ' is-publicado' : ''; ?>" data-eq-estado-chip>
			<?php echo esc_html( afectivalab_panel_estado_etiqueta( $afectivalab_curso ? $afectivalab_curso->post_status : 'draft', AFECTIVALAB_CPT_CURSO ) ); ?>
		</span>
	</div>

	<div class="eq-alerta" role="alert" data-eq-alerta<?php echo $afectivalab_errors ? '' : ' hidden'; ?>>
		<?php if ( $afectivalab_errors ) : ?>
			<ul>
				<?php foreach ( $afectivalab_errors as $afectivalab_error ) : ?>
					<li><?php echo esc_html( $afectivalab_error ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<div class="eq-editor__grid">
		<div class="eq-editor__principal">
			<form id="<?php echo esc_attr( $afectivalab_form ); ?>" method="post" class="eq-editor__form" enctype="multipart/form-data" data-eq-ajax="guardar">
				<?php wp_nonce_field( 'afectivalab_panel_curso', 'afectivalab_panel_nonce' ); ?>
				<input type="hidden" name="afectivalab_panel_accion" value="guardar_curso">
				<input type="hidden" name="curso_id" value="<?php echo esc_attr( $afectivalab_curso ? $afectivalab_curso->ID : 0 ); ?>" data-eq-id>

				<section class="eq-card">
					<header class="eq-card__head">
						<span class="eq-card__icono"><?php afectivalab_icon( 'menu-mundos' ); ?></span>
						<div>
							<h3><?php esc_html_e( 'Lo básico', 'afectivalab' ); ?></h3>
							<p><?php esc_html_e( 'Con la etapa y el mundo, la plataforma sabe a qué familias proponérselo.', 'afectivalab' ); ?></p>
						</div>
					</header>

					<div class="eq-campo">
						<label for="curso-titulo"><?php esc_html_e( 'Título del curso', 'afectivalab' ); ?></label>
						<input class="eq-input eq-input--grande" type="text" id="curso-titulo" name="titulo" value="<?php echo esc_attr( $afectivalab_v['titulo'] ); ?>" placeholder="<?php esc_attr_e( 'Ej. Bullying: prevenir y actuar', 'afectivalab' ); ?>" required data-eq-titulo-input>
					</div>

					<fieldset class="eq-campo">
						<legend><?php esc_html_e( 'Etapa de edad', 'afectivalab' ); ?></legend>
						<div class="eq-opciones eq-opciones--etapas">
							<?php $afectivalab_primera = true; ?>
							<?php foreach ( afectivalab_etapas() as $afectivalab_slug => $afectivalab_etapa ) : ?>
								<label class="eq-opcion">
									<input type="radio" name="etapa" value="<?php echo esc_attr( $afectivalab_slug ); ?>" <?php checked( $afectivalab_v['etapa'], $afectivalab_slug ); ?><?php echo $afectivalab_primera ? ' required' : ''; ?>>
									<span class="eq-opcion__caja">
										<strong><?php echo esc_html( $afectivalab_etapa['nombre'] ); ?></strong>
										<small><?php echo esc_html( sprintf( /* translators: 1: edad mínima, 2: edad máxima. */ __( '%1$d a %2$d años', 'afectivalab' ), $afectivalab_etapa['edad_min'], $afectivalab_etapa['edad_max'] ) ); ?></small>
									</span>
								</label>
								<?php $afectivalab_primera = false; ?>
							<?php endforeach; ?>
						</div>
					</fieldset>

					<fieldset class="eq-campo">
						<legend><?php esc_html_e( 'Mundo temático', 'afectivalab' ); ?></legend>
						<div class="eq-opciones eq-opciones--ejes">
							<?php $afectivalab_primera = true; ?>
							<?php foreach ( afectivalab_ejes() as $afectivalab_slug => $afectivalab_eje ) : ?>
								<label class="eq-opcion">
									<input type="radio" name="eje" value="<?php echo esc_attr( $afectivalab_slug ); ?>" <?php checked( $afectivalab_v['eje'], $afectivalab_slug ); ?><?php echo $afectivalab_primera ? ' required' : ''; ?>>
									<span class="eq-opcion__caja eq-opcion__caja--icono">
										<?php afectivalab_icon( 'eje-' . $afectivalab_slug ); ?>
										<strong><?php echo esc_html( $afectivalab_eje['nombre'] ); ?></strong>
									</span>
								</label>
								<?php $afectivalab_primera = false; ?>
							<?php endforeach; ?>
						</div>
					</fieldset>
				</section>

				<section class="eq-card">
					<header class="eq-card__head">
						<span class="eq-card__icono eq-card__icono--amarillo"><?php afectivalab_icon( 'menu-panel' ); ?></span>
						<div>
							<h3><?php esc_html_e( 'Presentación', 'afectivalab' ); ?></h3>
							<p><?php esc_html_e( 'Lo que lee la familia antes de empezar.', 'afectivalab' ); ?></p>
						</div>
					</header>

					<div class="eq-campo">
						<label for="curso-resumen"><?php esc_html_e( 'Resumen', 'afectivalab' ); ?></label>
						<textarea class="eq-input" id="curso-resumen" name="resumen" rows="2" data-eq-contador="160" placeholder="<?php esc_attr_e( 'Una o dos líneas: qué va a lograr la familia con este curso.', 'afectivalab' ); ?>"><?php echo esc_textarea( $afectivalab_v['resumen'] ); ?></textarea>
						<p class="eq-ayuda"><?php esc_html_e( 'Aparece en las tarjetas del curso y bajo su título. Ideal: menos de 160 caracteres.', 'afectivalab' ); ?> <span class="eq-contador" data-eq-contador-texto></span></p>
					</div>

					<div class="eq-campo">
						<label for="curso_contenido"><?php esc_html_e( 'Descripción completa', 'afectivalab' ); ?></label>
						<?php
						// El id no lleva guiones a propósito: el editor visual de
						// WordPress no arranca si el id del textarea tiene guiones.
						wp_editor(
							$afectivalab_v['contenido'],
							'curso_contenido',
							array(
								'textarea_name' => 'contenido',
								'textarea_rows' => 10,
								'media_buttons' => false,
								'teeny'         => true,
								'quicktags'     => true,
							)
						);
						?>
					</div>
				</section>

				<section class="eq-card">
					<header class="eq-card__head">
						<span class="eq-card__icono eq-card__icono--verde"><?php afectivalab_icon( 'star' ); ?></span>
						<div>
							<h3><?php esc_html_e( 'Recompensa', 'afectivalab' ); ?></h3>
							<p><?php esc_html_e( 'Lo que gana el padre al terminar: da nombre a su insignia y a su certificado.', 'afectivalab' ); ?></p>
						</div>
					</header>

					<div class="eq-campo">
						<label for="curso-habilidad"><?php esc_html_e( 'Habilidad adquirida', 'afectivalab' ); ?></label>
						<input class="eq-input" type="text" id="curso-habilidad" name="habilidad" value="<?php echo esc_attr( $afectivalab_v['habilidad'] ); ?>" placeholder="<?php esc_attr_e( 'Ej. Prevención y manejo del bullying', 'afectivalab' ); ?>" data-eq-habilidad>
					</div>

					<div class="eq-insignia-preview" aria-hidden="true">
						<?php afectivalab_icon( 'juego-insignia' ); ?>
						<div>
							<small><?php esc_html_e( 'Así se verá en su panel', 'afectivalab' ); ?></small>
							<strong data-eq-habilidad-preview data-vacio="<?php esc_attr_e( 'Nombre de la habilidad', 'afectivalab' ); ?>">
								<?php echo esc_html( $afectivalab_v['habilidad'] ? $afectivalab_v['habilidad'] : __( 'Nombre de la habilidad', 'afectivalab' ) ); ?>
							</strong>
						</div>
					</div>
				</section>
			</form>

			<?php get_template_part( 'template-parts/panel/curso-clases', null, array( 'curso' => $afectivalab_curso ) ); ?>
		</div>

		<aside class="eq-editor__lateral">
			<?php
			get_template_part(
				'template-parts/panel/publicar-campo',
				null,
				array(
					'post'   => $afectivalab_curso,
					'form'   => $afectivalab_form,
					'estado' => $afectivalab_v['estado'],
					'tipo'   => 'curso',
				)
			);

			get_template_part(
				'template-parts/panel/imagen-campo',
				null,
				array(
					'post'  => $afectivalab_curso,
					'form'  => $afectivalab_form,
					'ayuda' => __( 'Se ve en las tarjetas y arriba del curso. JPG, PNG o WEBP; mejor horizontal.', 'afectivalab' ),
				)
			);
			?>
		</aside>
	</div>
</div>
