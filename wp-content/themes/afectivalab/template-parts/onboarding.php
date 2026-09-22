<?php
/**
 * La guía de primeros pasos. La incluyen /panel y /mis-hijos mientras quede
 * algo por hacer — ver afectivalab_mostrar_onboarding() en inc/onboarding.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_pasos = afectivalab_onboarding_pasos();
$afectivalab_hechos = count(
	array_filter(
		$afectivalab_pasos,
		function ( $paso ) {
			return $paso['hecho'];
		}
	)
);
$afectivalab_total = count( $afectivalab_pasos );
?>

<section class="guia reveal" aria-labelledby="guia-titulo">
	<div class="guia__head">
		<div>
			<h2 class="guia__titulo" id="guia-titulo"><?php esc_html_e( 'Cómo funciona Afectivalab', 'afectivalab' ); ?></h2>
			<p class="guia__lead"><?php esc_html_e( 'Cuatro pasos. Te acompañamos en cada uno.', 'afectivalab' ); ?></p>
		</div>

		<span class="guia__contador">
			<?php
			printf(
				/* translators: 1: pasos hechos, 2: total de pasos. */
				esc_html__( '%1$d de %2$d', 'afectivalab' ),
				absint( $afectivalab_hechos ),
				absint( $afectivalab_total )
			);
			?>
		</span>
	</div>

	<div class="progreso-bar guia__barra">
		<span class="progreso-bar__fill" style="width: <?php echo esc_attr( round( $afectivalab_hechos / $afectivalab_total * 100 ) ); ?>%"></span>
	</div>

	<ol class="guia__pasos">
		<?php
		$afectivalab_numero = 1;
		$afectivalab_toca   = true;

		foreach ( $afectivalab_pasos as $afectivalab_paso ) :
			// "El que toca" es el primero sin hacer: es el único que muestra
			// su botón, para que no haya dudas de por dónde seguir.
			$afectivalab_actual = ! $afectivalab_paso['hecho'] && $afectivalab_toca;

			if ( $afectivalab_actual ) {
				$afectivalab_toca = false;
			}

			$afectivalab_clase = $afectivalab_paso['hecho'] ? 'is-hecho' : ( $afectivalab_actual ? 'is-actual' : 'is-pendiente' );
			?>
			<li class="guia__paso <?php echo esc_attr( $afectivalab_clase ); ?>">
				<span class="guia__marca" aria-hidden="true">
					<?php
					if ( $afectivalab_paso['hecho'] ) {
						afectivalab_icon( 'check' );
					} else {
						echo esc_html( $afectivalab_numero );
					}
					?>
				</span>

				<div class="guia__texto">
					<strong><?php echo esc_html( $afectivalab_paso['titulo'] ); ?></strong>
					<p><?php echo esc_html( $afectivalab_paso['texto'] ); ?></p>

					<?php if ( $afectivalab_actual && $afectivalab_paso['enlace'] ) : ?>
						<a class="btn btn-primary guia__accion" href="<?php echo esc_url( $afectivalab_paso['enlace'] ); ?>">
							<?php echo esc_html( $afectivalab_paso['accion'] ); ?>
							<?php afectivalab_icon( 'arrow-right' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</li>
			<?php
			$afectivalab_numero++;
		endforeach;
		?>
	</ol>

	<p class="guia__ocultar">
		<a href="<?php echo esc_url( afectivalab_onboarding_url_ocultar() ); ?>">
			<?php esc_html_e( 'Ya entendí, no mostrar esta guía', 'afectivalab' ); ?>
		</a>
	</p>
</section>
