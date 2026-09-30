<?php
/**
 * Edición de curso > sus microclases, en el orden en que las ve la familia.
 *
 * Va fuera del formulario del curso a propósito: cada fila tiene sus propios
 * formularios (subir, bajar, publicar) y un formulario no puede ir dentro
 * de otro. Sin curso guardado todavía, solo explica qué falta.
 *
 * @var array $args { curso: WP_Post|null }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_curso  = $args['curso'] ?? null;
$afectivalab_clases = $afectivalab_curso ? afectivalab_clases_del_curso( $afectivalab_curso->ID, afectivalab_panel_estados_visibles() ) : array();
?>

<section class="eq-card eq-card--clases" data-eq-curso-clases>
	<header class="eq-card__head">
		<span class="eq-card__icono eq-card__icono--verde"><?php afectivalab_icon( 'eq-play' ); ?></span>
		<div>
			<h3><?php esc_html_e( 'Microclases de este curso', 'afectivalab' ); ?></h3>
			<p><?php esc_html_e( 'En este orden las recorre la familia. Usa las flechas para cambiarlo.', 'afectivalab' ); ?></p>
		</div>

		<?php if ( $afectivalab_curso ) : ?>
			<a
				class="btn btn-secondary eq-boton-chico"
				href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'clases', 'accion' => 'nuevo', 'curso' => $afectivalab_curso->ID ) ) ); ?>"
				data-eq-modal-abrir="nueva-clase"
				data-eq-curso="<?php echo esc_attr( $afectivalab_curso->ID ); ?>"
			>
				<?php afectivalab_icon( 'eq-mas' ); ?>
				<?php esc_html_e( 'Añadir microclase', 'afectivalab' ); ?>
			</a>
		<?php endif; ?>
	</header>

	<?php if ( ! $afectivalab_curso ) : ?>
		<p class="eq-nota"><?php esc_html_e( 'Guarda el curso primero; después podrás agregarle sus microclases desde aquí mismo.', 'afectivalab' ); ?></p>
	<?php else : ?>
		<p class="eq-vacio-linea" data-eq-vacio<?php echo $afectivalab_clases ? ' hidden' : ''; ?>>
			<?php esc_html_e( 'Todavía no tiene ninguna. Mientras tanto, las familias lo ven como "en preparación".', 'afectivalab' ); ?>
		</p>

		<ol class="eq-filas eq-filas--ordenables" data-eq-orden-lista>
			<?php foreach ( $afectivalab_clases as $afectivalab_clase ) : ?>
				<?php get_template_part( 'template-parts/panel/fila-clase', null, array( 'clase' => $afectivalab_clase, 'contexto' => 'curso' ) ); ?>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>
</section>
