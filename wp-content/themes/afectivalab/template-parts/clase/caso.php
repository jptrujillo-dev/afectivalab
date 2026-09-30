<?php
/**
 * El caso práctico de una microclase.
 *
 * Pieza aparte porque la pintan dos lados: la página de la clase y la
 * respuesta AJAX al elegir una opción (ver inc/clase-ajax.php), que devuelve
 * este mismo HTML ya con la retroalimentación — así nunca hay dos versiones
 * del marcado.
 *
 * Args: clase_id, hijo_id.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_clase_id = (int) $args['clase_id'];
$afectivalab_hijo_id  = (int) $args['hijo_id'];
$afectivalab_caso     = afectivalab_clase_caso( $afectivalab_clase_id );

if ( ! $afectivalab_caso ) {
	return;
}

$afectivalab_caso_registro = afectivalab_caso_registro( $afectivalab_hijo_id, $afectivalab_clase_id );
?>
<section class="clase-bloque caso-demo" data-clase-caso>
	<div class="clase-bloque__cabecera">
		<span class="clase-bloque__icono"><?php afectivalab_icon( 'leccion-caso-interactivo' ); ?></span>
		<div>
			<h2 class="caso-demo__titulo"><?php esc_html_e( 'Caso práctico', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Elige qué harías tú: cada respuesta trae su explicación.', 'afectivalab' ); ?></p>
		</div>
	</div>

	<?php foreach ( array( 'paso1', 'paso2' ) as $afectivalab_paso_slug ) : ?>
		<?php
		if ( ! $afectivalab_caso[ $afectivalab_paso_slug ] ) {
			continue; // El caso puede no tener paso 2.
		}

		// El paso 2 (si existe) solo se muestra una vez respondido el 1:
		// es la continuación de la historia, no una pregunta suelta.
		if ( 'paso2' === $afectivalab_paso_slug && null === $afectivalab_caso_registro['paso1'] ) {
			continue;
		}

		$afectivalab_paso    = $afectivalab_caso[ $afectivalab_paso_slug ];
		$afectivalab_elegida = $afectivalab_caso_registro[ $afectivalab_paso_slug ];
		?>
		<div class="caso-paso-card" data-caso-paso="<?php echo esc_attr( $afectivalab_paso_slug ); ?>">
			<p class="caso-paso-card__situacion"><?php echo esc_html( $afectivalab_paso['situacion'] ); ?></p>

			<?php if ( null === $afectivalab_elegida ) : ?>
				<?php // Todavía no respondió este paso: se muestran las opciones. ?>
				<form method="post" class="caso-opciones" data-clase-ajax="caso" data-clase="<?php echo esc_attr( $afectivalab_clase_id ); ?>" data-hijo="<?php echo esc_attr( $afectivalab_hijo_id ); ?>">
					<?php wp_nonce_field( 'afectivalab_caso_' . $afectivalab_clase_id, 'afectivalab_caso_nonce', false ); ?>
					<input type="hidden" name="afectivalab_caso_paso" value="<?php echo esc_attr( $afectivalab_paso_slug ); ?>">

					<?php foreach ( $afectivalab_paso['opciones'] as $afectivalab_i => $afectivalab_opcion ) : ?>
						<button type="submit" name="afectivalab_caso_opcion" value="<?php echo esc_attr( $afectivalab_i ); ?>" class="caso-opcion-boton">
							<span class="caso-opcion-boton__letra"><?php echo esc_html( chr( 65 + $afectivalab_i ) ); ?></span>
							<span><?php echo esc_html( $afectivalab_opcion['texto'] ); ?></span>
						</button>
					<?php endforeach; ?>

					<div class="form-alert" role="alert" data-clase-alerta hidden></div>
				</form>
			<?php else : ?>
				<?php
				// Ya respondió: no un simple ✅/❌, cada opción trae su propia
				// explicación razonada (ver inc/casos.php).
				$afectivalab_opcion_elegida = $afectivalab_paso['opciones'][ $afectivalab_elegida ] ?? null;
				?>
				<?php if ( $afectivalab_opcion_elegida ) : ?>
					<div class="caso-feedback <?php echo $afectivalab_opcion_elegida['recomendada'] ? 'is-recomendada' : 'is-guia'; ?>">
						<?php afectivalab_icon( $afectivalab_opcion_elegida['recomendada'] ? 'check' : 'star' ); ?>
						<div>
							<strong><?php echo esc_html( $afectivalab_opcion_elegida['texto'] ); ?></strong>
							<p><?php echo esc_html( $afectivalab_opcion_elegida['retroalimentacion'] ); ?></p>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</section>
