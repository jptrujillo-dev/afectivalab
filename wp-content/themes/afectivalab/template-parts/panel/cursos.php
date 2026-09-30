<?php
/**
 * Panel > Cursos: el listado, con buscador y filtros por estado y etapa.
 *
 * El buscador y los filtros son de JavaScript (assets/js/equipo.js) y vienen
 * ocultos: sin JavaScript se ve la lista completa, que es lo mismo que
 * "Todos".
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

$afectivalab_publicados = count(
	array_filter(
		$afectivalab_cursos,
		function ( $curso ) {
			return 'publish' === $curso->post_status;
		}
	)
);
?>

<div class="eq-seccion__head">
	<div>
		<h2><?php esc_html_e( 'Cursos', 'afectivalab' ); ?></h2>
		<p><?php esc_html_e( 'Cada curso es un tema para una etapa de edad, con sus microclases en orden.', 'afectivalab' ); ?></p>
	</div>
	<a class="btn btn-primary" href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'nuevo' ) ) ); ?>" data-eq-modal-abrir="nuevo-curso">
		<?php afectivalab_icon( 'eq-mas' ); ?>
		<?php esc_html_e( 'Nuevo curso', 'afectivalab' ); ?>
	</a>
</div>

<?php if ( ! $afectivalab_cursos ) : ?>
	<div class="eq-vacio">
		<?php afectivalab_icon( 'juego-mapa-desbloqueable', 'eq-vacio__icono' ); ?>
		<h3><?php esc_html_e( 'Todavía no hay cursos', 'afectivalab' ); ?></h3>
		<p><?php esc_html_e( 'Crea el primero y después agrégale sus microclases.', 'afectivalab' ); ?></p>
	</div>
<?php else : ?>
	<div class="eq-herramientas" data-eq-filtros="eq-lista-cursos" hidden>
		<label class="eq-buscar">
			<?php afectivalab_icon( 'eq-buscar' ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Buscar curso', 'afectivalab' ); ?></span>
			<input type="search" placeholder="<?php esc_attr_e( 'Buscar por título o mundo…', 'afectivalab' ); ?>" data-eq-buscar>
		</label>

		<div class="eq-chips" role="group" aria-label="<?php esc_attr_e( 'Filtrar por estado', 'afectivalab' ); ?>">
			<button type="button" class="eq-chip-filtro is-activo" data-eq-filtro="estado" data-valor="" aria-pressed="true">
				<?php esc_html_e( 'Todos', 'afectivalab' ); ?> <span><?php echo absint( count( $afectivalab_cursos ) ); ?></span>
			</button>
			<button type="button" class="eq-chip-filtro" data-eq-filtro="estado" data-valor="publish" aria-pressed="false">
				<?php esc_html_e( 'Publicados', 'afectivalab' ); ?> <span><?php echo absint( $afectivalab_publicados ); ?></span>
			</button>
			<button type="button" class="eq-chip-filtro" data-eq-filtro="estado" data-valor="draft" aria-pressed="false">
				<?php esc_html_e( 'Borradores', 'afectivalab' ); ?> <span><?php echo absint( count( $afectivalab_cursos ) - $afectivalab_publicados ); ?></span>
			</button>
		</div>

		<label class="eq-select">
			<span class="screen-reader-text"><?php esc_html_e( 'Filtrar por etapa', 'afectivalab' ); ?></span>
			<select data-eq-filtro-select="etapa">
				<option value=""><?php esc_html_e( 'Todas las etapas', 'afectivalab' ); ?></option>
				<?php foreach ( afectivalab_etapas() as $afectivalab_slug => $afectivalab_etapa ) : ?>
					<option value="<?php echo esc_attr( $afectivalab_slug ); ?>"><?php echo esc_html( $afectivalab_etapa['nombre'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	</div>

	<ul class="eq-filas" id="eq-lista-cursos">
		<?php foreach ( $afectivalab_cursos as $afectivalab_curso ) : ?>
			<?php get_template_part( 'template-parts/panel/fila-curso', null, array( 'curso' => $afectivalab_curso ) ); ?>
		<?php endforeach; ?>
	</ul>

	<p class="eq-sin-resultados" data-eq-sin-resultados="eq-lista-cursos" hidden>
		<?php esc_html_e( 'Ningún curso coincide con la búsqueda.', 'afectivalab' ); ?>
	</p>
<?php endif; ?>
