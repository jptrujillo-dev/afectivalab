<?php
/**
 * Imagen destacada con vista previa, para la barra lateral del editor.
 *
 * Los campos van asociados al formulario principal con el atributo form=,
 * porque la barra lateral queda fuera de él (ver curso-form.php).
 *
 * @var array $args { post: WP_Post|null, form: string, ayuda: string }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_post  = $args['post'] ?? null;
$afectivalab_form  = $args['form'];
$afectivalab_tiene = $afectivalab_post && has_post_thumbnail( $afectivalab_post->ID );
?>

<section class="eq-card">
	<header class="eq-card__head eq-card__head--simple">
		<h3><?php esc_html_e( 'Imagen destacada', 'afectivalab' ); ?></h3>
	</header>

	<div class="eq-imagen<?php echo $afectivalab_tiene ? ' tiene-imagen' : ''; ?>" data-eq-imagen>
		<div class="eq-imagen__preview" data-eq-imagen-preview>
			<?php if ( $afectivalab_tiene ) : ?>
				<?php echo get_the_post_thumbnail( $afectivalab_post->ID, 'medium' ); ?>
			<?php endif; ?>
			<span class="eq-imagen__vacia"><?php afectivalab_icon( 'camera' ); ?><?php esc_html_e( 'Sin imagen', 'afectivalab' ); ?></span>
		</div>

		<label class="eq-imagen__subir">
			<input type="file" name="imagen" form="<?php echo esc_attr( $afectivalab_form ); ?>" accept="image/jpeg,image/png,image/webp" data-eq-imagen-input>
			<span class="eq-imagen__boton" data-eq-imagen-texto><?php echo $afectivalab_tiene ? esc_html__( 'Cambiar imagen', 'afectivalab' ) : esc_html__( 'Elegir imagen', 'afectivalab' ); ?></span>
		</label>

		<label class="eq-imagen__quitar">
			<input type="checkbox" name="quitar_imagen" value="1" form="<?php echo esc_attr( $afectivalab_form ); ?>" data-eq-imagen-quitar>
			<?php esc_html_e( 'Quitar la imagen al guardar', 'afectivalab' ); ?>
		</label>

		<p class="eq-imagen__error" data-eq-imagen-error hidden></p>
		<p class="eq-ayuda"><?php echo esc_html( $args['ayuda'] ?? '' ); ?></p>
	</div>
</section>
