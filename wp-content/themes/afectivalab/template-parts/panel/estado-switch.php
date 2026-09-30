<?php
/**
 * Interruptor Publicado / Borrador de un curso o microclase en los listados.
 *
 * Es un formulario de verdad: sin JavaScript se envía y lo procesa
 * afectivalab_panel_handle(); con JavaScript (assets/js/equipo.js) se envía
 * por AJAX y el interruptor cambia en su lugar. Quien no puede publicar ve
 * solo la etiqueta del estado.
 *
 * @var array $args { post: WP_Post }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_post     = $args['post'];
$afectivalab_es_curso = AFECTIVALAB_CPT_CURSO === $afectivalab_post->post_type;
$afectivalab_on       = 'publish' === $afectivalab_post->post_status;
$afectivalab_etiqueta = afectivalab_panel_estado_etiqueta( $afectivalab_post->post_status, $afectivalab_post->post_type );
$afectivalab_puede    = current_user_can( 'edit_post', $afectivalab_post->ID )
	&& current_user_can( $afectivalab_es_curso ? 'publish_afectivalab_cursos' : 'publish_afectivalab_clases' );
?>

<?php if ( $afectivalab_puede ) : ?>
	<form method="post" class="eq-switch-form" data-eq-ajax="estado">
		<?php wp_nonce_field( 'afectivalab_panel_estado', 'afectivalab_panel_nonce', false ); ?>
		<input type="hidden" name="afectivalab_panel_accion" value="cambiar_estado">
		<input type="hidden" name="post_id" value="<?php echo esc_attr( $afectivalab_post->ID ); ?>">
		<input type="hidden" name="estado" value="<?php echo $afectivalab_on ? 'draft' : 'publish'; ?>" data-eq-estado-siguiente>
		<button
			type="submit"
			class="eq-switch<?php echo $afectivalab_on ? ' is-on' : ''; ?>"
			role="switch"
			aria-checked="<?php echo $afectivalab_on ? 'true' : 'false'; ?>"
			title="<?php echo $afectivalab_on ? esc_attr__( 'Pasar a borrador', 'afectivalab' ) : esc_attr__( 'Publicar', 'afectivalab' ); ?>"
		>
			<span class="eq-switch__pista" aria-hidden="true"><span class="eq-switch__bola"></span></span>
			<span class="eq-switch__texto" data-eq-estado-texto><?php echo esc_html( $afectivalab_etiqueta ); ?></span>
		</button>
	</form>
<?php else : ?>
	<span class="eq-estado<?php echo $afectivalab_on ? ' is-publicado' : ''; ?>"><?php echo esc_html( $afectivalab_etiqueta ); ?></span>
<?php endif; ?>
