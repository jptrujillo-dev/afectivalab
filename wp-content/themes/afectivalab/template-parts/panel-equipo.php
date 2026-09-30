<?php
/**
 * Panel del equipo de contenido: navegación entre secciones y despacho de la
 * que toca.
 *
 * Lo incluye page-templates/panel.php cuando quien entra puede editar cursos.
 * Las acciones (guardar, eliminar, cambiar rol…) las resuelve
 * inc/panel-admin.php antes de que se imprima nada, para poder redirigir; con
 * JavaScript las mismas acciones van por AJAX (inc/panel-ajax.php y
 * assets/js/equipo.js) y la página no se recarga.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_seccion = afectivalab_panel_seccion();
$afectivalab_accion  = afectivalab_panel_accion();
$afectivalab_estado  = $args['estado'] ?? array( 'errors' => array(), 'valores' => array() );
$afectivalab_editor  = in_array( $afectivalab_seccion, array( 'cursos', 'clases' ), true ) && in_array( $afectivalab_accion, array( 'nuevo', 'editar' ), true );

$afectivalab_msg      = isset( $_GET['msg'] ) ? sanitize_key( wp_unslash( $_GET['msg'] ) ) : '';
$afectivalab_mensajes = afectivalab_panel_mensajes();

$afectivalab_err         = isset( $_GET['error'] ) ? sanitize_key( wp_unslash( $_GET['error'] ) ) : '';
$afectivalab_errores_url = afectivalab_panel_errores_url();

$afectivalab_cuenta_cursos = wp_count_posts( AFECTIVALAB_CPT_CURSO );
$afectivalab_cuenta_clases = wp_count_posts( AFECTIVALAB_CPT_CLASE );

$afectivalab_tabs = array(
	'resumen'  => array( 'icono' => 'menu-panel', 'cuenta' => null ),
	'cursos'   => array( 'icono' => 'menu-mundos', 'cuenta' => (int) $afectivalab_cuenta_cursos->publish + (int) $afectivalab_cuenta_cursos->draft ),
	'clases'   => array( 'icono' => 'eq-play', 'cuenta' => (int) $afectivalab_cuenta_clases->publish + (int) $afectivalab_cuenta_clases->draft ),
	'usuarios' => array( 'icono' => 'menu-hijos', 'cuenta' => null ),
);
?>

<div class="eq-panel" data-eq-panel>
	<header class="eq-cabecera reveal">
		<div>
			<span class="eq-cabecera__chip"><?php esc_html_e( 'Equipo de contenido', 'afectivalab' ); ?></span>
			<h1><?php esc_html_e( 'Panel de contenido', 'afectivalab' ); ?></h1>
			<p>
				<?php
				printf(
					/* translators: %s: nombre de la persona. */
					esc_html__( 'Hola, %s. Desde aquí administras los cursos y su gente.', 'afectivalab' ),
					esc_html( wp_get_current_user()->display_name )
				);
				?>
			</p>
		</div>

		<?php if ( ! $afectivalab_editor ) : ?>
			<div class="eq-cabecera__acciones">
				<a class="btn btn-secondary" href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'clases', 'accion' => 'nuevo' ) ) ); ?>" data-eq-modal-abrir="nueva-clase">
					<?php afectivalab_icon( 'eq-mas' ); ?>
					<?php esc_html_e( 'Microclase', 'afectivalab' ); ?>
				</a>
				<a class="btn btn-primary" href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'nuevo' ) ) ); ?>" data-eq-modal-abrir="nuevo-curso">
					<?php afectivalab_icon( 'eq-mas' ); ?>
					<?php esc_html_e( 'Curso', 'afectivalab' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</header>

	<nav class="eq-tabs" aria-label="<?php esc_attr_e( 'Secciones del panel', 'afectivalab' ); ?>">
		<?php foreach ( afectivalab_panel_secciones() as $afectivalab_slug => $afectivalab_nombre ) : ?>
			<?php
			// La sección de usuarios solo existe para quien administra el sitio.
			if ( 'usuarios' === $afectivalab_slug && ! current_user_can( 'list_users' ) ) {
				continue;
			}

			$afectivalab_activa = $afectivalab_slug === $afectivalab_seccion;
			?>
			<a
				class="eq-tab<?php echo $afectivalab_activa ? ' is-activa' : ''; ?>"
				href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => $afectivalab_slug ) ) ); ?>"
				<?php echo $afectivalab_activa ? 'aria-current="page"' : ''; ?>
			>
				<?php afectivalab_icon( $afectivalab_tabs[ $afectivalab_slug ]['icono'] ); ?>
				<?php echo esc_html( $afectivalab_nombre ); ?>
				<?php if ( null !== $afectivalab_tabs[ $afectivalab_slug ]['cuenta'] ) : ?>
					<span class="eq-tab__cuenta"><?php echo absint( $afectivalab_tabs[ $afectivalab_slug ]['cuenta'] ); ?></span>
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
	</nav>

	<?php // Aviso después de una redirección. Con JavaScript se convierte en un aviso flotante. ?>
	<?php if ( $afectivalab_msg && isset( $afectivalab_mensajes[ $afectivalab_msg ] ) ) : ?>
		<div class="eq-alerta eq-alerta--exito" role="status" data-eq-aviso-inicial="exito"><?php echo esc_html( $afectivalab_mensajes[ $afectivalab_msg ] ); ?></div>
	<?php endif; ?>

	<?php if ( $afectivalab_err && isset( $afectivalab_errores_url[ $afectivalab_err ] ) ) : ?>
		<div class="eq-alerta" role="alert" data-eq-aviso-inicial="error"><?php echo esc_html( $afectivalab_errores_url[ $afectivalab_err ] ); ?></div>
	<?php endif; ?>

	<?php // Los editores muestran sus propios errores junto al formulario. ?>
	<?php if ( ! $afectivalab_editor && ! empty( $afectivalab_estado['errors'] ) ) : ?>
		<div class="eq-alerta" role="alert">
			<ul>
				<?php foreach ( $afectivalab_estado['errors'] as $afectivalab_error ) : ?>
					<li><?php echo esc_html( $afectivalab_error ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php
	if ( 'cursos' === $afectivalab_seccion ) {
		$afectivalab_parte = $afectivalab_editor ? 'template-parts/panel/curso-form' : 'template-parts/panel/cursos';
	} elseif ( 'clases' === $afectivalab_seccion ) {
		$afectivalab_parte = $afectivalab_editor ? 'template-parts/panel/clase-form' : 'template-parts/panel/clases';
	} elseif ( 'usuarios' === $afectivalab_seccion ) {
		$afectivalab_parte = 'template-parts/panel/usuarios';
	} else {
		$afectivalab_parte = 'template-parts/panel/resumen';
	}

	get_template_part( $afectivalab_parte, null, array( 'estado' => $afectivalab_estado ) );

	get_template_part( 'template-parts/panel/modales' );
	?>

	<div class="eq-avisos" data-eq-avisos aria-live="polite"></div>
</div>
