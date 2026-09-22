<?php
/**
 * Panel del equipo de contenido: navegación entre secciones y despacho de la
 * que toca.
 *
 * Lo incluye page-templates/panel.php cuando quien entra puede editar cursos.
 * Las acciones (guardar, eliminar, cambiar rol) las resuelve
 * inc/panel-admin.php antes de que se imprima nada, para poder redirigir.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_seccion = afectivalab_panel_seccion();
$afectivalab_estado  = $args['estado'] ?? array( 'errors' => array(), 'valores' => array() );

$afectivalab_msg     = isset( $_GET['msg'] ) ? sanitize_key( wp_unslash( $_GET['msg'] ) ) : '';
$afectivalab_mensajes = afectivalab_panel_mensajes();

$afectivalab_err     = isset( $_GET['error'] ) ? sanitize_key( wp_unslash( $_GET['error'] ) ) : '';
$afectivalab_errores_url = afectivalab_panel_errores_url();
?>

<header class="panel-saludo reveal">
	<h1><?php esc_html_e( 'Panel de contenido', 'afectivalab' ); ?></h1>
	<p class="panel-saludo__lead">
		<?php
		printf(
			/* translators: %s: nombre de la persona. */
			esc_html__( 'Hola, %s. Desde aquí administras los cursos y su gente.', 'afectivalab' ),
			esc_html( wp_get_current_user()->display_name )
		);
		?>
	</p>
</header>

<nav class="panel-tabs" aria-label="<?php esc_attr_e( 'Secciones del panel', 'afectivalab' ); ?>">
	<?php foreach ( afectivalab_panel_secciones() as $afectivalab_slug => $afectivalab_nombre ) : ?>
		<?php
		// La sección de usuarios solo existe para quien administra el sitio.
		if ( 'usuarios' === $afectivalab_slug && ! current_user_can( 'list_users' ) ) {
			continue;
		}

		$afectivalab_activa = $afectivalab_slug === $afectivalab_seccion;
		?>
		<a
			class="panel-tab <?php echo $afectivalab_activa ? 'is-activa' : ''; ?>"
			href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => $afectivalab_slug ) ) ); ?>"
			<?php echo $afectivalab_activa ? 'aria-current="page"' : ''; ?>
		>
			<?php echo esc_html( $afectivalab_nombre ); ?>
		</a>
	<?php endforeach; ?>
</nav>

<?php if ( $afectivalab_msg && isset( $afectivalab_mensajes[ $afectivalab_msg ] ) ) : ?>
	<div class="form-alert form-alert--success"><?php echo esc_html( $afectivalab_mensajes[ $afectivalab_msg ] ); ?></div>
<?php endif; ?>

<?php if ( $afectivalab_err && isset( $afectivalab_errores_url[ $afectivalab_err ] ) ) : ?>
	<div class="form-alert" role="alert"><?php echo esc_html( $afectivalab_errores_url[ $afectivalab_err ] ); ?></div>
<?php endif; ?>

<?php if ( ! empty( $afectivalab_estado['errors'] ) ) : ?>
	<div class="form-alert" role="alert">
		<ul>
			<?php foreach ( $afectivalab_estado['errors'] as $afectivalab_error ) : ?>
				<li><?php echo esc_html( $afectivalab_error ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

<?php
if ( 'cursos' === $afectivalab_seccion ) {
	$afectivalab_parte = 'editar' === afectivalab_panel_accion() || 'nuevo' === afectivalab_panel_accion()
		? 'template-parts/panel/curso-form'
		: 'template-parts/panel/cursos';
} elseif ( 'clases' === $afectivalab_seccion ) {
	$afectivalab_parte = 'editar' === afectivalab_panel_accion() || 'nuevo' === afectivalab_panel_accion()
		? 'template-parts/panel/clase-form'
		: 'template-parts/panel/clases';
} elseif ( 'usuarios' === $afectivalab_seccion ) {
	$afectivalab_parte = 'template-parts/panel/usuarios';
} else {
	$afectivalab_parte = 'template-parts/panel/resumen';
}

get_template_part( $afectivalab_parte, null, array( 'estado' => $afectivalab_estado ) );
?>

<p class="equipo-cambio">
	<a href="<?php echo esc_url( add_query_arg( 'vista', 'familia', home_url( '/panel' ) ) ); ?>">
		<?php esc_html_e( 'Ver la plataforma como la ve una familia', 'afectivalab' ); ?>
	</a>
</p>
