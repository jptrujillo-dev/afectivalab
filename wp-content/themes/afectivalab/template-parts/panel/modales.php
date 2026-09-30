<?php
/**
 * Modales de creación rápida del panel: "Nuevo curso" y "Nueva microclase".
 *
 * Piden solo lo mínimo para crear el borrador; lo demás se completa después
 * en el editor. Los botones que los abren son enlaces a la pantalla completa
 * de alta (accion=nuevo), así que sin JavaScript todo sigue funcionando.
 *
 * - Nuevo curso: crea el borrador y abre su editor.
 * - Nueva microclase: si se abre desde un curso, la agrega a su lista ahí
 *   mismo sin salir; si no, crea el borrador y abre su editor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_cursos = get_posts(
	array(
		'post_type'      => AFECTIVALAB_CPT_CURSO,
		'post_status'    => afectivalab_panel_estados_visibles(),
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);
?>

<?php if ( current_user_can( 'edit_afectivalab_cursos' ) ) : ?>
	<div class="eq-modal" data-eq-modal="nuevo-curso" hidden>
		<div class="eq-modal__fondo" data-eq-modal-cerrar></div>
		<div class="eq-modal__caja" role="dialog" aria-modal="true" aria-labelledby="eq-modal-curso-titulo">
			<button type="button" class="eq-modal__cerrar" data-eq-modal-cerrar aria-label="<?php esc_attr_e( 'Cerrar', 'afectivalab' ); ?>"><?php afectivalab_icon( 'close' ); ?></button>

			<header class="eq-modal__head">
				<span class="eq-card__icono"><?php afectivalab_icon( 'menu-mundos' ); ?></span>
				<div>
					<h2 id="eq-modal-curso-titulo"><?php esc_html_e( 'Nuevo curso', 'afectivalab' ); ?></h2>
					<p><?php esc_html_e( 'Empieza con lo básico. Se guarda como borrador y sigues en el editor.', 'afectivalab' ); ?></p>
				</div>
			</header>

			<form method="post" class="eq-modal__form" data-eq-ajax="guardar" data-eq-destino="editar">
				<?php wp_nonce_field( 'afectivalab_panel_curso', 'afectivalab_panel_nonce', false ); ?>
				<input type="hidden" name="afectivalab_panel_accion" value="guardar_curso">
				<input type="hidden" name="curso_id" value="0">
				<input type="hidden" name="estado" value="draft">

				<div class="eq-alerta" role="alert" data-eq-alerta hidden></div>

				<div class="eq-campo">
					<label for="eq-nuevo-curso-titulo"><?php esc_html_e( 'Título del curso', 'afectivalab' ); ?></label>
					<input class="eq-input" type="text" id="eq-nuevo-curso-titulo" name="titulo" required placeholder="<?php esc_attr_e( 'Ej. Bullying: prevenir y actuar', 'afectivalab' ); ?>" data-eq-foco>
				</div>

				<div class="eq-fila-campos">
					<div class="eq-campo eq-campo--ancho">
						<label for="eq-nuevo-curso-etapa"><?php esc_html_e( 'Etapa de edad', 'afectivalab' ); ?></label>
						<select class="eq-input" id="eq-nuevo-curso-etapa" name="etapa" required>
							<option value=""><?php esc_html_e( 'Elige una etapa', 'afectivalab' ); ?></option>
							<?php foreach ( afectivalab_etapas() as $afectivalab_slug => $afectivalab_etapa ) : ?>
								<option value="<?php echo esc_attr( $afectivalab_slug ); ?>">
									<?php echo esc_html( sprintf( '%1$s (%2$d–%3$d)', $afectivalab_etapa['nombre'], $afectivalab_etapa['edad_min'], $afectivalab_etapa['edad_max'] ) ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="eq-campo eq-campo--ancho">
						<label for="eq-nuevo-curso-eje"><?php esc_html_e( 'Mundo temático', 'afectivalab' ); ?></label>
						<select class="eq-input" id="eq-nuevo-curso-eje" name="eje" required>
							<option value=""><?php esc_html_e( 'Elige un mundo', 'afectivalab' ); ?></option>
							<?php foreach ( afectivalab_ejes() as $afectivalab_slug => $afectivalab_eje ) : ?>
								<option value="<?php echo esc_attr( $afectivalab_slug ); ?>"><?php echo esc_html( $afectivalab_eje['nombre'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="eq-modal__botones">
					<button type="button" class="btn btn-secondary" data-eq-modal-cerrar><?php esc_html_e( 'Cancelar', 'afectivalab' ); ?></button>
					<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Crear y seguir editando', 'afectivalab' ); ?></button>
				</div>
			</form>
		</div>
	</div>
<?php endif; ?>

<?php if ( current_user_can( 'edit_afectivalab_clases' ) ) : ?>
	<div class="eq-modal" data-eq-modal="nueva-clase" hidden>
		<div class="eq-modal__fondo" data-eq-modal-cerrar></div>
		<div class="eq-modal__caja" role="dialog" aria-modal="true" aria-labelledby="eq-modal-clase-titulo">
			<button type="button" class="eq-modal__cerrar" data-eq-modal-cerrar aria-label="<?php esc_attr_e( 'Cerrar', 'afectivalab' ); ?>"><?php afectivalab_icon( 'close' ); ?></button>

			<header class="eq-modal__head">
				<span class="eq-card__icono eq-card__icono--verde"><?php afectivalab_icon( 'eq-play' ); ?></span>
				<div>
					<h2 id="eq-modal-clase-titulo"><?php esc_html_e( 'Nueva microclase', 'afectivalab' ); ?></h2>
					<p data-eq-modal-texto-curso hidden><?php esc_html_e( 'Se agrega como borrador al final del curso. Video, misión y caso los completas después.', 'afectivalab' ); ?></p>
					<p data-eq-modal-texto-general><?php esc_html_e( 'Se guarda como borrador y sigues en el editor para completar video, misión y caso.', 'afectivalab' ); ?></p>
				</div>
			</header>

			<form method="post" class="eq-modal__form" data-eq-ajax="guardar" data-eq-destino="editar">
				<?php wp_nonce_field( 'afectivalab_panel_clase', 'afectivalab_panel_nonce', false ); ?>
				<input type="hidden" name="afectivalab_panel_accion" value="guardar_clase">
				<input type="hidden" name="clase_id" value="0">
				<input type="hidden" name="estado" value="draft">
				<input type="hidden" name="video_tipo" value="ninguno">
				<input type="hidden" name="mision_tipo" value="ninguna">
				<input type="hidden" name="desde" value="" data-eq-desde>

				<div class="eq-alerta" role="alert" data-eq-alerta hidden></div>

				<div class="eq-campo">
					<label for="eq-nueva-clase-titulo"><?php esc_html_e( 'Título de la microclase', 'afectivalab' ); ?></label>
					<input class="eq-input" type="text" id="eq-nueva-clase-titulo" name="titulo" required placeholder="<?php esc_attr_e( 'Ej. Señales de alerta en casa', 'afectivalab' ); ?>" data-eq-foco>
				</div>

				<div class="eq-fila-campos">
					<div class="eq-campo eq-campo--ancho">
						<label for="eq-nueva-clase-curso"><?php esc_html_e( 'Curso', 'afectivalab' ); ?></label>
						<select class="eq-input" id="eq-nueva-clase-curso" name="curso" data-eq-curso-select>
							<option value="0"><?php esc_html_e( 'Sin asignar', 'afectivalab' ); ?></option>
							<?php foreach ( $afectivalab_cursos as $afectivalab_curso ) : ?>
								<option value="<?php echo esc_attr( $afectivalab_curso->ID ); ?>"><?php echo esc_html( $afectivalab_curso->post_title ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="eq-campo">
						<label for="eq-nueva-clase-duracion"><?php esc_html_e( 'Minutos', 'afectivalab' ); ?></label>
						<input class="eq-input" type="number" id="eq-nueva-clase-duracion" name="duracion" min="1" max="120" step="1" placeholder="10">
					</div>
				</div>

				<div class="eq-modal__botones">
					<button type="button" class="btn btn-secondary" data-eq-modal-cerrar><?php esc_html_e( 'Cancelar', 'afectivalab' ); ?></button>
					<button type="submit" class="btn btn-primary" data-eq-modal-submit><?php esc_html_e( 'Crear microclase', 'afectivalab' ); ?></button>
				</div>
			</form>
		</div>
	</div>
<?php endif; ?>
