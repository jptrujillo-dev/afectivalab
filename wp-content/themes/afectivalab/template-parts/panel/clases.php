<?php
/**
 * Panel > Microclases: el listado, agrupado por curso.
 *
 * Agrupado y no plano a propósito: con decenas de clases de títulos parecidos
 * entre rutas, una lista corrida es imposible de manejar. Cada grupo se
 * puede plegar, y el buscador filtra dentro de todos a la vez.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_clases = get_posts(
	array(
		'post_type'      => AFECTIVALAB_CPT_CLASE,
		'post_status'    => afectivalab_panel_estados_visibles(),
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	)
);

$afectivalab_grupos     = array();
$afectivalab_publicadas = 0;

foreach ( $afectivalab_clases as $afectivalab_clase ) {
	$afectivalab_curso_id = (int) get_post_meta( $afectivalab_clase->ID, '_afectivalab_curso', true );
	$afectivalab_grupos[ $afectivalab_curso_id ][] = $afectivalab_clase;

	if ( 'publish' === $afectivalab_clase->post_status ) {
		$afectivalab_publicadas++;
	}
}

// Por título de curso, y "sin curso" al principio: es lo que hay que arreglar.
uksort(
	$afectivalab_grupos,
	function ( $a, $b ) {
		if ( ! $a || ! $b ) {
			return $a ? 1 : ( $b ? -1 : 0 );
		}

		return strcasecmp( get_the_title( $a ), get_the_title( $b ) );
	}
);
?>

<div class="eq-seccion__head">
	<div>
		<h2><?php esc_html_e( 'Microclases', 'afectivalab' ); ?></h2>
		<p><?php esc_html_e( 'Agrupadas por curso y en el orden en que las ve la familia.', 'afectivalab' ); ?></p>
	</div>
	<a class="btn btn-primary" href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'clases', 'accion' => 'nuevo' ) ) ); ?>" data-eq-modal-abrir="nueva-clase">
		<?php afectivalab_icon( 'eq-mas' ); ?>
		<?php esc_html_e( 'Nueva microclase', 'afectivalab' ); ?>
	</a>
</div>

<?php if ( ! $afectivalab_clases ) : ?>
	<div class="eq-vacio">
		<?php afectivalab_icon( 'leccion-video-principal', 'eq-vacio__icono' ); ?>
		<h3><?php esc_html_e( 'Todavía no hay microclases', 'afectivalab' ); ?></h3>
		<p><?php esc_html_e( 'Crea la primera y asígnala a un curso.', 'afectivalab' ); ?></p>
	</div>
<?php else : ?>
	<div class="eq-herramientas" data-eq-filtros="eq-lista-clases" hidden>
		<label class="eq-buscar">
			<?php afectivalab_icon( 'eq-buscar' ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Buscar microclase', 'afectivalab' ); ?></span>
			<input type="search" placeholder="<?php esc_attr_e( 'Buscar microclase…', 'afectivalab' ); ?>" data-eq-buscar>
		</label>

		<div class="eq-chips" role="group" aria-label="<?php esc_attr_e( 'Filtrar por estado', 'afectivalab' ); ?>">
			<button type="button" class="eq-chip-filtro is-activo" data-eq-filtro="estado" data-valor="" aria-pressed="true">
				<?php esc_html_e( 'Todas', 'afectivalab' ); ?> <span><?php echo absint( count( $afectivalab_clases ) ); ?></span>
			</button>
			<button type="button" class="eq-chip-filtro" data-eq-filtro="estado" data-valor="publish" aria-pressed="false">
				<?php esc_html_e( 'Publicadas', 'afectivalab' ); ?> <span><?php echo absint( $afectivalab_publicadas ); ?></span>
			</button>
			<button type="button" class="eq-chip-filtro" data-eq-filtro="estado" data-valor="draft" aria-pressed="false">
				<?php esc_html_e( 'Borradores', 'afectivalab' ); ?> <span><?php echo absint( count( $afectivalab_clases ) - $afectivalab_publicadas ); ?></span>
			</button>
		</div>
	</div>

	<div id="eq-lista-clases" class="eq-grupos">
		<?php foreach ( $afectivalab_grupos as $afectivalab_curso_id => $afectivalab_lista ) : ?>
			<details class="eq-grupo<?php echo $afectivalab_curso_id ? '' : ' eq-grupo--alerta'; ?>" data-eq-grupo open>
				<summary class="eq-grupo__head">
					<?php afectivalab_icon( 'eq-flecha', 'eq-grupo__flecha' ); ?>
					<span class="eq-grupo__titulo">
						<?php echo $afectivalab_curso_id ? esc_html( get_the_title( $afectivalab_curso_id ) ) : esc_html__( 'Sin curso asignado', 'afectivalab' ); ?>
					</span>
					<span class="eq-grupo__cuenta">
						<?php echo esc_html( sprintf( /* translators: %d: cantidad de microclases. */ _n( '%d microclase', '%d microclases', count( $afectivalab_lista ), 'afectivalab' ), count( $afectivalab_lista ) ) ); ?>
					</span>
					<?php if ( $afectivalab_curso_id && current_user_can( 'edit_post', $afectivalab_curso_id ) ) : ?>
						<a class="eq-grupo__enlace" href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'editar', 'id' => $afectivalab_curso_id ) ) ); ?>">
							<?php esc_html_e( 'Ordenar en el curso', 'afectivalab' ); ?>
						</a>
					<?php endif; ?>
				</summary>

				<?php if ( ! $afectivalab_curso_id ) : ?>
					<p class="eq-nota eq-nota--alerta"><?php esc_html_e( 'Estas microclases no aparecen en ninguna ruta hasta que se les asigne un curso.', 'afectivalab' ); ?></p>
				<?php endif; ?>

				<ul class="eq-filas">
					<?php foreach ( $afectivalab_lista as $afectivalab_clase ) : ?>
						<?php get_template_part( 'template-parts/panel/fila-clase', null, array( 'clase' => $afectivalab_clase, 'contexto' => 'lista' ) ); ?>
					<?php endforeach; ?>
				</ul>
			</details>
		<?php endforeach; ?>
	</div>

	<p class="eq-sin-resultados" data-eq-sin-resultados="eq-lista-clases" hidden>
		<?php esc_html_e( 'Ninguna microclase coincide con la búsqueda.', 'afectivalab' ); ?>
	</p>
<?php endif; ?>
