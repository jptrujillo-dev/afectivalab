<?php
/**
 * Certificado de finalización de un curso — /certificado?hijo=ID&curso=ID.
 *
 * No usa header.php/footer.php: un certificado no es "una página del sitio",
 * es un documento independiente pensado para imprimirse o guardarse como
 * PDF, así que arma su propio HTML mínimo (sin menú, sin footer) en vez de
 * tener que esconder toda la navegación del sitio con CSS de impresión.
 *
 * Diseño: pantalla 5 de la referencia Stitch (docs/rediseno-stitch.md) —
 * felicitación arriba, el diploma y las acciones abajo. Solo con datos
 * reales: nada de firmas de terceros ni códigos de verificación que no
 * existen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/ingresar' ) );
	exit;
}

$afectivalab_hijo_id  = isset( $_GET['hijo'] ) ? absint( $_GET['hijo'] ) : 0;
$afectivalab_curso_id = isset( $_GET['curso'] ) ? absint( $_GET['curso'] ) : 0;
$afectivalab_datos    = afectivalab_datos_certificado( $afectivalab_hijo_id, $afectivalab_curso_id );

if ( ! $afectivalab_datos ) {
	// El hijo no es suyo, el curso no existe, o todavía no lo completó: en
	// cualquiera de los tres casos no hay nada que mostrar, y no conviene
	// decir cuál fue para no revelar de más.
	wp_safe_redirect( home_url( '/panel' ) );
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo esc_html( sprintf( __( 'Certificado de %1$s — %2$s', 'afectivalab' ), $afectivalab_datos['hijo'], $afectivalab_datos['curso'] ) ); ?></title>
	<?php wp_head(); ?>
</head>
<body class="certificado-body">

	<div class="certificado-barra no-imprimir">
		<a class="certificado-volver" href="<?php echo esc_url( add_query_arg( array( 'hijo' => $afectivalab_hijo_id ), home_url( '/panel' ) ) ); ?>">
			<span class="certificado-volver__flecha" aria-hidden="true"><?php afectivalab_icon( 'arrow-right', 'certificado-volver__icon' ); ?></span>
			<?php esc_html_e( 'Volver al panel', 'afectivalab' ); ?>
		</a>
	</div>

	<header class="certificado-celebra no-imprimir">
		<span class="certificado-celebra__pill">
			<?php afectivalab_icon( 'juego-insignia' ); ?>
			<?php esc_html_e( '¡Curso completado!', 'afectivalab' ); ?>
		</span>
		<h1 class="certificado-celebra__titulo">
			<?php
			printf(
				/* translators: %s: nombre del hijo. */
				esc_html__( '¡Felicidades, %s!', 'afectivalab' ),
				esc_html( $afectivalab_datos['hijo'] )
			);
			?>
		</h1>
		<p class="certificado-celebra__texto">
			<?php
			printf(
				/* translators: %s: nombre del curso. */
				esc_html__( 'Terminaron juntos "%s". Su certificado ya está listo para guardar, imprimir y celebrar en familia.', 'afectivalab' ),
				esc_html( $afectivalab_datos['curso'] )
			);
			?>
		</p>
	</header>

	<main class="certificado-hoja">
		<div class="certificado-marco">
			<?php afectivalab_icon( 'doodle-leaf', 'certificado-doodle certificado-doodle--1' ); ?>
			<?php afectivalab_icon( 'doodle-heart', 'certificado-doodle certificado-doodle--2' ); ?>
			<?php afectivalab_icon( 'doodle-leaf', 'certificado-doodle certificado-doodle--3' ); ?>

			<?php afectivalab_icon( 'logo-color', 'certificado-logo' ); ?>

			<span class="certificado-etiqueta"><?php esc_html_e( 'Certificado de logro', 'afectivalab' ); ?></span>

			<p class="certificado-otorga"><?php esc_html_e( 'Se otorga con orgullo y cariño a', 'afectivalab' ); ?></p>

			<h1 class="certificado-nombre"><?php echo esc_html( $afectivalab_datos['hijo'] ); ?></h1>

			<p class="certificado-texto"><?php esc_html_e( 'por completar el curso', 'afectivalab' ); ?></p>

			<p class="certificado-curso">&ldquo;<?php echo esc_html( $afectivalab_datos['curso'] ); ?>&rdquo;</p>

			<div class="certificado-insignia">
				<?php afectivalab_icon( 'juego-insignia', 'certificado-insignia__icon' ); ?>
				<span>
					<small><?php esc_html_e( 'Habilidad adquirida', 'afectivalab' ); ?></small>
					<?php echo esc_html( $afectivalab_datos['habilidad'] ); ?>
				</span>
			</div>

			<div class="certificado-pie">
				<div class="certificado-firma">
					<span class="certificado-firma__nombre"><?php echo esc_html( $afectivalab_datos['padre'] ); ?></span>
					<p class="certificado-acompanamiento">
						<?php
						printf(
							/* translators: %s: nombre del padre o madre. */
							esc_html__( 'Acompañado por %s', 'afectivalab' ),
							esc_html( $afectivalab_datos['padre'] )
						);
						?>
					</p>
				</div>

				<span class="certificado-sello" aria-hidden="true">
					<?php afectivalab_icon( 'star', 'certificado-sello__icon' ); ?>
				</span>

				<div class="certificado-firma">
					<span class="certificado-firma__nombre"><?php echo esc_html( $afectivalab_datos['fecha'] ); ?></span>
					<p class="certificado-fecha"><?php esc_html_e( 'Fecha en que lo completó', 'afectivalab' ); ?></p>
				</div>
			</div>
		</div>
	</main>

	<div class="certificado-acciones no-imprimir">
		<button type="button" class="btn btn-primary btn-lg" data-certificado-imprimir>
			<?php afectivalab_icon( 'juego-certificado' ); ?>
			<?php esc_html_e( 'Descargar (guardar como PDF)', 'afectivalab' ); ?>
		</button>
		<a class="btn btn-secondary btn-lg" href="<?php echo esc_url( get_permalink( $afectivalab_curso_id ) ); ?>">
			<?php esc_html_e( 'Volver al curso', 'afectivalab' ); ?>
			<?php afectivalab_icon( 'arrow-right' ); ?>
		</a>
		<p class="certificado-acciones__ayuda">
			<?php esc_html_e( 'En la ventana que se abre, elige "Guardar como PDF" para descargarlo, o tu impresora para imprimirlo.', 'afectivalab' ); ?>
		</p>
	</div>

	<?php wp_footer(); ?>
</body>
</html>
