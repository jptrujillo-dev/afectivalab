<?php
/**
 * La caja "Progreso del curso" de la cabecera de una microclase. Pieza aparte
 * para que la respuesta AJAX de "marcar como vista" o "misión cumplida" la
 * devuelva ya actualizada (ver inc/clase-ajax.php).
 *
 * Args: clase_id, hijo_id.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_curso_id  = (int) get_post_meta( (int) $args['clase_id'], '_afectivalab_curso', true );
$afectivalab_progreso  = afectivalab_progreso_curso( (int) $args['hijo_id'], $afectivalab_curso_id );
$afectivalab_habilidad = get_post_meta( $afectivalab_curso_id, '_afectivalab_habilidad', true );
$afectivalab_faltan    = $afectivalab_progreso['total'] - $afectivalab_progreso['hechas'];

if ( ! $afectivalab_progreso['total'] ) {
	return;
}
?>
<div class="clase-head__progreso" data-clase-progreso>
	<div class="clase-head__progreso-fila">
		<strong><?php esc_html_e( 'Progreso del curso', 'afectivalab' ); ?></strong>
		<span class="clase-head__porcentaje">
			<?php
			printf(
				/* translators: %d: porcentaje completado. */
				esc_html__( '%d%% completado', 'afectivalab' ),
				absint( $afectivalab_progreso['porcentaje'] )
			);
			?>
		</span>
	</div>
	<div class="progreso-bar">
		<span class="progreso-bar__fill" style="width: <?php echo esc_attr( $afectivalab_progreso['porcentaje'] ); ?>%"></span>
	</div>
	<?php if ( $afectivalab_habilidad && $afectivalab_faltan > 0 ) : ?>
		<p class="clase-head__pista">
			<?php
			printf(
				/* translators: 1: clases que faltan, 2: habilidad del curso. */
				esc_html( _n( 'Te falta %1$d clase para la insignia "%2$s".', 'Te faltan %1$d clases para la insignia "%2$s".', $afectivalab_faltan, 'afectivalab' ) ),
				absint( $afectivalab_faltan ),
				esc_html( $afectivalab_habilidad )
			);
			?>
		</p>
	<?php elseif ( $afectivalab_habilidad && $afectivalab_progreso['completo'] ) : ?>
		<p class="clase-head__pista clase-head__pista--logro">
			<?php
			printf(
				/* translators: %s: habilidad del curso. */
				esc_html__( '¡Curso completo! Ganaron la insignia "%s".', 'afectivalab' ),
				esc_html( $afectivalab_habilidad )
			);
			?>
		</p>
	<?php endif; ?>
</div>
