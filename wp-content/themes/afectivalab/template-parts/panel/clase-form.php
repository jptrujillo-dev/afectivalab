<?php
/**
 * Panel > Microclases: alta y edición.
 *
 * Mismo esquema que curso-form.php: contenido a la izquierda, estado e
 * imagen a la derecha, guardado por AJAX sin salir de la página.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_estado_form = $args['estado'] ?? array();

$afectivalab_id    = ! empty( $afectivalab_estado_form['id'] ) ? (int) $afectivalab_estado_form['id'] : ( isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0 );
$afectivalab_clase = $afectivalab_id ? afectivalab_panel_post_editable( $afectivalab_id, AFECTIVALAB_CPT_CLASE ) : null;

if ( $afectivalab_id && ! $afectivalab_clase ) {
	echo '<div class="eq-alerta" role="alert">' . esc_html__( 'No encontramos esa microclase, o no puedes editarla.', 'afectivalab' ) . '</div>';
	return;
}

$afectivalab_v = $afectivalab_estado_form['valores'] ?? array();

if ( ! $afectivalab_v ) {
	$afectivalab_v = array(
		'titulo'       => $afectivalab_clase ? $afectivalab_clase->post_title : '',
		// Al llegar desde "Añadir microclase" de un curso, viene preelegido.
		'curso'        => $afectivalab_clase
			? (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_curso', true )
			: ( isset( $_GET['curso'] ) ? absint( $_GET['curso'] ) : 0 ),
		'orden'        => $afectivalab_clase ? (int) $afectivalab_clase->menu_order : 0,
		'duracion'     => $afectivalab_clase ? (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_duracion', true ) : 0,
		'video_tipo'   => $afectivalab_clase ? get_post_meta( $afectivalab_clase->ID, '_afectivalab_video_tipo', true ) : 'ninguno',
		'video_url'    => $afectivalab_clase ? get_post_meta( $afectivalab_clase->ID, '_afectivalab_video_url', true ) : '',
		'contenido'    => $afectivalab_clase ? $afectivalab_clase->post_content : '',
		'mision_tipo'  => $afectivalab_clase ? get_post_meta( $afectivalab_clase->ID, '_afectivalab_mision_tipo', true ) : 'ninguna',
		'mision_texto' => $afectivalab_clase ? get_post_meta( $afectivalab_clase->ID, '_afectivalab_mision_texto', true ) : '',
		'caso'         => $afectivalab_clase ? afectivalab_panel_caso_valores_desde_clase( $afectivalab_clase->ID ) : array( 'paso1' => afectivalab_panel_caso_paso_vacio(), 'paso2' => afectivalab_panel_caso_paso_vacio() ),
		'estado'       => $afectivalab_clase ? $afectivalab_clase->post_status : 'draft',
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
		'post_status'    => afectivalab_panel_estados_visibles(),
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);

$afectivalab_form   = 'eq-form-clase';
$afectivalab_errors = $afectivalab_estado_form['errors'] ?? array();
$afectivalab_on     = $afectivalab_clase && 'publish' === $afectivalab_clase->post_status;
$afectivalab_volver = $afectivalab_v['curso']
	? afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'editar', 'id' => $afectivalab_v['curso'] ) )
	: afectivalab_panel_url( array( 'seccion' => 'clases' ) );
?>

<div class="eq-editor" data-eq-editor="clase">
	<div class="eq-editor__barra">
		<a class="eq-volver" href="<?php echo esc_url( $afectivalab_volver ); ?>">
			<?php afectivalab_icon( 'arrow-right', 'eq-volver__icono' ); ?>
			<?php echo $afectivalab_v['curso'] ? esc_html( get_the_title( $afectivalab_v['curso'] ) ) : esc_html__( 'Microclases', 'afectivalab' ); ?>
		</a>

		<div class="eq-editor__titulo">
			<span class="eq-editor__tipo"><?php echo $afectivalab_clase ? esc_html__( 'Editando microclase', 'afectivalab' ) : esc_html__( 'Nueva microclase', 'afectivalab' ); ?></span>
			<h2 data-eq-titulo data-vacio="<?php esc_attr_e( 'Microclase sin título', 'afectivalab' ); ?>">
				<?php echo esc_html( $afectivalab_clase ? $afectivalab_clase->post_title : __( 'Microclase sin título', 'afectivalab' ) ); ?>
			</h2>
		</div>

		<span class="eq-estado<?php echo $afectivalab_on ? ' is-publicado' : ''; ?>" data-eq-estado-chip>
			<?php echo esc_html( afectivalab_panel_estado_etiqueta( $afectivalab_clase ? $afectivalab_clase->post_status : 'draft', AFECTIVALAB_CPT_CLASE ) ); ?>
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
				<?php wp_nonce_field( 'afectivalab_panel_clase', 'afectivalab_panel_nonce' ); ?>
				<input type="hidden" name="afectivalab_panel_accion" value="guardar_clase">
				<input type="hidden" name="clase_id" value="<?php echo esc_attr( $afectivalab_clase ? $afectivalab_clase->ID : 0 ); ?>" data-eq-id>

				<section class="eq-card">
					<header class="eq-card__head">
						<span class="eq-card__icono"><?php afectivalab_icon( 'eq-play' ); ?></span>
						<div>
							<h3><?php esc_html_e( 'Lo básico', 'afectivalab' ); ?></h3>
							<p><?php esc_html_e( 'A qué curso pertenece y en qué lugar del camino va.', 'afectivalab' ); ?></p>
						</div>
					</header>

					<div class="eq-campo">
						<label for="clase-titulo"><?php esc_html_e( 'Título de la microclase', 'afectivalab' ); ?></label>
						<input class="eq-input eq-input--grande" type="text" id="clase-titulo" name="titulo" value="<?php echo esc_attr( $afectivalab_v['titulo'] ); ?>" placeholder="<?php esc_attr_e( 'Ej. Señales de alerta en casa', 'afectivalab' ); ?>" required data-eq-titulo-input>
					</div>

					<div class="eq-fila-campos">
						<div class="eq-campo eq-campo--ancho">
							<label for="clase-curso"><?php esc_html_e( 'Curso', 'afectivalab' ); ?></label>
							<select class="eq-input" id="clase-curso" name="curso">
								<option value="0"><?php esc_html_e( 'Sin asignar (no aparece en ninguna ruta)', 'afectivalab' ); ?></option>
								<?php foreach ( $afectivalab_cursos as $afectivalab_curso ) : ?>
									<option value="<?php echo esc_attr( $afectivalab_curso->ID ); ?>" <?php selected( $afectivalab_v['curso'], $afectivalab_curso->ID ); ?>>
										<?php echo esc_html( $afectivalab_curso->post_title ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="eq-campo">
							<label for="clase-orden"><?php esc_html_e( 'Lugar', 'afectivalab' ); ?></label>
							<input class="eq-input" type="number" id="clase-orden" name="orden" value="<?php echo esc_attr( $afectivalab_v['orden'] ? $afectivalab_v['orden'] : '' ); ?>" min="0" step="1" placeholder="<?php esc_attr_e( 'Al final', 'afectivalab' ); ?>">
						</div>

						<div class="eq-campo">
							<label for="clase-duracion"><?php esc_html_e( 'Minutos', 'afectivalab' ); ?></label>
							<input class="eq-input" type="number" id="clase-duracion" name="duracion" value="<?php echo esc_attr( $afectivalab_v['duracion'] ? $afectivalab_v['duracion'] : '' ); ?>" min="1" max="120" step="1" placeholder="10">
						</div>
					</div>
				</section>

				<section class="eq-card">
					<header class="eq-card__head">
						<span class="eq-card__icono eq-card__icono--verde"><?php afectivalab_icon( 'leccion-video-principal' ); ?></span>
						<div>
							<h3><?php esc_html_e( 'Video principal', 'afectivalab' ); ?></h3>
							<p><?php esc_html_e( 'Para videos largos conviene YouTube o Vimeo: pesan menos y se ven mejor en el celular.', 'afectivalab' ); ?></p>
						</div>
					</header>

					<div class="eq-opciones eq-opciones--tres">
						<?php
						$afectivalab_videos = array(
							'ninguno' => array( __( 'Sin video', 'afectivalab' ), __( 'Solo texto e imagen', 'afectivalab' ) ),
							'url'     => array( __( 'Enlace', 'afectivalab' ), __( 'YouTube o Vimeo', 'afectivalab' ) ),
							'media'   => array( __( 'Subir archivo', 'afectivalab' ), __( 'MP4 desde tu equipo', 'afectivalab' ) ),
						);
						?>
						<?php foreach ( $afectivalab_videos as $afectivalab_slug => $afectivalab_txt ) : ?>
							<label class="eq-opcion">
								<input type="radio" name="video_tipo" value="<?php echo esc_attr( $afectivalab_slug ); ?>" <?php checked( $afectivalab_v['video_tipo'], $afectivalab_slug ); ?>>
								<span class="eq-opcion__caja">
									<strong><?php echo esc_html( $afectivalab_txt[0] ); ?></strong>
									<small><?php echo esc_html( $afectivalab_txt[1] ); ?></small>
								</span>
							</label>
						<?php endforeach; ?>
					</div>

					<div class="eq-subpanel" data-video-panel="url">
						<label for="clase-video-url"><?php esc_html_e( 'Enlace del video', 'afectivalab' ); ?></label>
						<input class="eq-input" type="url" id="clase-video-url" name="video_url" value="<?php echo esc_attr( $afectivalab_v['video_url'] ); ?>" placeholder="https://www.youtube.com/watch?v=...">
					</div>

					<div class="eq-subpanel" data-video-panel="media">
						<?php if ( $afectivalab_video_id ) : ?>
							<p class="eq-archivo-actual">
								<?php afectivalab_icon( 'eq-play' ); ?>
								<?php
								printf(
									/* translators: %s: nombre del archivo subido. */
									esc_html__( 'Archivo actual: %s. Si eliges otro, lo reemplaza.', 'afectivalab' ),
									'<strong>' . esc_html( get_the_title( $afectivalab_video_id ) ) . '</strong>'
								);
								?>
							</p>
						<?php endif; ?>
						<input class="eq-input-archivo" type="file" name="video_archivo" accept="video/*">
					</div>
				</section>

				<section class="eq-card">
					<header class="eq-card__head">
						<span class="eq-card__icono eq-card__icono--amarillo"><?php afectivalab_icon( 'menu-panel' ); ?></span>
						<div>
							<h3><?php esc_html_e( 'Contenido de la clase', 'afectivalab' ); ?></h3>
							<p><?php esc_html_e( 'Las ideas clave, frases que ayudan y qué practicar en la semana.', 'afectivalab' ); ?></p>
						</div>
					</header>

					<?php
					// El id no lleva guiones a propósito: el editor visual de
					// WordPress no arranca si el id del textarea tiene guiones.
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
				</section>

				<section class="eq-card">
					<header class="eq-card__head">
						<span class="eq-card__icono eq-card__icono--verde"><?php afectivalab_icon( 'juego-monedas' ); ?></span>
						<div>
							<h3><?php esc_html_e( 'Misión', 'afectivalab' ); ?></h3>
							<p><?php esc_html_e( 'La acción fuera de la pantalla que hace el padre con su hijo. Si la clase lleva misión, resolverla es lo que la completa.', 'afectivalab' ); ?></p>
						</div>
					</header>

					<div class="eq-opciones eq-opciones--tres">
						<label class="eq-opcion">
							<input type="radio" name="mision_tipo" value="ninguna" <?php checked( $afectivalab_v['mision_tipo'], 'ninguna' ); ?>>
							<span class="eq-opcion__caja">
								<strong><?php esc_html_e( 'Sin misión', 'afectivalab' ); ?></strong>
								<small><?php esc_html_e( 'Se completa al marcarla como vista', 'afectivalab' ); ?></small>
							</span>
						</label>

						<?php foreach ( afectivalab_mision_tipos() as $afectivalab_slug => $afectivalab_tipo ) : ?>
							<label class="eq-opcion">
								<input type="radio" name="mision_tipo" value="<?php echo esc_attr( $afectivalab_slug ); ?>" <?php checked( $afectivalab_v['mision_tipo'], $afectivalab_slug ); ?>>
								<span class="eq-opcion__caja eq-opcion__caja--icono">
									<?php afectivalab_icon( $afectivalab_tipo['icono'] ); ?>
									<strong><?php echo esc_html( $afectivalab_tipo['nombre'] ); ?></strong>
									<small>
										<?php
										echo esc_html(
											sprintf(
												/* translators: %d: monedas que otorga. */
												$afectivalab_tipo['requiere_evidencia'] ? __( '+%d monedas · el padre sube una foto', 'afectivalab' ) : __( '+%d monedas', 'afectivalab' ),
												$afectivalab_tipo['recompensa']
											)
										);
										?>
									</small>
								</span>
							</label>
						<?php endforeach; ?>
					</div>

					<div class="eq-subpanel" data-mision-panel="<?php echo esc_attr( implode( ',', array_keys( afectivalab_mision_tipos() ) ) ); ?>">
						<label for="clase-mision"><?php esc_html_e( 'Qué debe hacer la familia', 'afectivalab' ); ?></label>
						<textarea class="eq-input" id="clase-mision" name="mision_texto" rows="3" placeholder="<?php esc_attr_e( 'Ej. Pregúntale a tu hijo: ¿qué fue lo que mejor hiciste esta semana aunque te haya costado?', 'afectivalab' ); ?>"><?php echo esc_textarea( $afectivalab_v['mision_texto'] ); ?></textarea>
					</div>
				</section>

				<section class="eq-card">
					<header class="eq-card__head">
						<span class="eq-card__icono"><?php afectivalab_icon( 'leccion-caso-interactivo' ); ?></span>
						<div>
							<h3><?php esc_html_e( 'Caso interactivo', 'afectivalab' ); ?></h3>
							<p><?php esc_html_e( 'Un dilema con opciones, cada una con su explicación (no un simple correcto o incorrecto). Déjalo vacío si esta clase no lleva caso.', 'afectivalab' ); ?></p>
						</div>
					</header>

					<?php
					$afectivalab_caso_pasos = array(
						'paso1' => __( 'Situación inicial', 'afectivalab' ),
						'paso2' => __( 'Pregunta de seguimiento', 'afectivalab' ),
					);
					?>

					<?php foreach ( $afectivalab_caso_pasos as $afectivalab_paso_slug => $afectivalab_paso_nombre ) : ?>
						<?php
						$afectivalab_paso_v   = $afectivalab_v['caso'][ $afectivalab_paso_slug ];
						$afectivalab_es_paso2 = 'paso2' === $afectivalab_paso_slug;
						$afectivalab_abierto  = ! $afectivalab_es_paso2 || '' !== $afectivalab_paso_v['situacion'];
						?>

						<details class="eq-caso-paso"<?php echo $afectivalab_abierto ? ' open' : ''; ?>>
							<summary>
								<span class="eq-caso-paso__numero"><?php echo $afectivalab_es_paso2 ? '2' : '1'; ?></span>
								<?php echo esc_html( $afectivalab_paso_nombre ); ?>
								<?php if ( $afectivalab_es_paso2 ) : ?>
									<small><?php esc_html_e( 'Opcional', 'afectivalab' ); ?></small>
								<?php endif; ?>
								<?php afectivalab_icon( 'eq-flecha', 'eq-caso-paso__flecha' ); ?>
							</summary>

							<div class="eq-caso-paso__cuerpo">
								<div class="eq-campo">
									<label for="caso-<?php echo esc_attr( $afectivalab_paso_slug ); ?>"><?php esc_html_e( 'La situación', 'afectivalab' ); ?></label>
									<textarea
										class="eq-input"
										id="caso-<?php echo esc_attr( $afectivalab_paso_slug ); ?>"
										name="caso_<?php echo esc_attr( $afectivalab_paso_slug ); ?>_situacion"
										rows="2"
										placeholder="<?php echo $afectivalab_es_paso2 ? esc_attr__( 'Ej. ¿Qué le preguntarías a continuación?', 'afectivalab' ) : esc_attr__( 'Ej. Tu hijo de 8 años dice: "No quiero volver al colegio mañana." ¿Qué harías primero?', 'afectivalab' ); ?>"
									><?php echo esc_textarea( $afectivalab_paso_v['situacion'] ); ?></textarea>
								</div>

								<div class="eq-caso-opciones">
									<?php foreach ( $afectivalab_paso_v['opciones'] as $afectivalab_i => $afectivalab_opcion ) : ?>
										<?php
										$afectivalab_n     = $afectivalab_i + 1;
										$afectivalab_campo = 'caso_' . $afectivalab_paso_slug . '_opcion_' . $afectivalab_n;
										?>
										<div class="eq-caso-opcion<?php echo $afectivalab_opcion['recomendada'] ? ' is-recomendada' : ''; ?>" data-eq-caso-opcion>
											<span class="eq-caso-opcion__letra"><?php echo esc_html( chr( 64 + $afectivalab_n ) ); ?></span>

											<div class="eq-caso-opcion__campos">
												<input class="eq-input" type="text" name="<?php echo esc_attr( $afectivalab_campo ); ?>_texto" value="<?php echo esc_attr( $afectivalab_opcion['texto'] ); ?>" placeholder="<?php esc_attr_e( 'Qué puede elegir la familia', 'afectivalab' ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: letra de la opción. */ __( 'Opción %s', 'afectivalab' ), chr( 64 + $afectivalab_n ) ) ); ?>">
												<textarea class="eq-input" name="<?php echo esc_attr( $afectivalab_campo ); ?>_feedback" rows="2" placeholder="<?php esc_attr_e( 'Por qué: la explicación que ve la familia al elegirla', 'afectivalab' ); ?>" aria-label="<?php esc_attr_e( 'Explicación', 'afectivalab' ); ?>"><?php echo esc_textarea( $afectivalab_opcion['feedback'] ); ?></textarea>

												<label class="eq-toggle">
													<input type="checkbox" name="<?php echo esc_attr( $afectivalab_campo ); ?>_recomendada" value="1" <?php checked( $afectivalab_opcion['recomendada'] ); ?> data-eq-recomendada>
													<span class="eq-toggle__pista" aria-hidden="true"><span class="eq-toggle__bola"></span></span>
													<?php esc_html_e( 'Opción recomendada', 'afectivalab' ); ?>
												</label>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</details>
					<?php endforeach; ?>
				</section>
			</form>
		</div>

		<aside class="eq-editor__lateral">
			<?php
			get_template_part(
				'template-parts/panel/publicar-campo',
				null,
				array(
					'post'   => $afectivalab_clase,
					'form'   => $afectivalab_form,
					'estado' => $afectivalab_v['estado'],
					'tipo'   => 'clase',
				)
			);

			get_template_part(
				'template-parts/panel/imagen-campo',
				null,
				array(
					'post'  => $afectivalab_clase,
					'form'  => $afectivalab_form,
					'ayuda' => __( 'Con video subido, es la portada antes de darle play. Sin video, se muestra en su lugar.', 'afectivalab' ),
				)
			);
			?>
		</aside>
	</div>
</div>
