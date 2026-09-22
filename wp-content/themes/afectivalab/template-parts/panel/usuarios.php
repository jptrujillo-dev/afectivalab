<?php
/**
 * Panel > Usuarios.
 *
 * Solo para quien administra el sitio. Deja ver las cuentas y cambiar entre
 * los dos roles de la plataforma; **no borra cuentas ni reparte el rol de
 * administrador** — ver afectivalab_panel_roles_asignables() en
 * inc/panel-admin.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( 'list_users' ) ) {
	return;
}

$afectivalab_usuarios = get_users(
	array(
		'orderby' => 'registered',
		'order'   => 'DESC',
		'number'  => 200,
	)
);

$afectivalab_roles   = afectivalab_panel_roles_asignables();
$afectivalab_puede   = current_user_can( 'promote_users' );
$afectivalab_yo      = get_current_user_id();
?>

<div class="panel-seccion__head">
	<h2 class="panel-seccion__titulo"><?php esc_html_e( 'Usuarios', 'afectivalab' ); ?></h2>
	<a class="btn btn-ghost" href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>">
		<?php esc_html_e( 'Añadir usuario', 'afectivalab' ); ?>
	</a>
</div>

<p class="equipo-nota">
	<?php esc_html_e( 'Desde aquí puedes cambiar entre Padre/Madre e Instructor. Crear administradores o borrar cuentas se hace en el escritorio, a propósito: son acciones difíciles de deshacer.', 'afectivalab' ); ?>
</p>

<ul class="tabla">
	<?php foreach ( $afectivalab_usuarios as $afectivalab_usuario ) : ?>
		<?php
		$afectivalab_es_admin_este = user_can( $afectivalab_usuario, 'manage_options' );
		$afectivalab_rol_actual    = '';

		foreach ( (array) $afectivalab_usuario->roles as $afectivalab_rol ) {
			if ( isset( $afectivalab_roles[ $afectivalab_rol ] ) ) {
				$afectivalab_rol_actual = $afectivalab_rol;
				break;
			}
		}

		$afectivalab_nhijos = count( afectivalab_get_hijos( $afectivalab_usuario->ID ) );

		// Ni a uno mismo ni a un administrador: cambiarse el propio rol es la
		// forma más fácil de quedarse fuera, y degradar a un admin desde el
		// front no debería ser posible.
		$afectivalab_editable = $afectivalab_puede
			&& ! $afectivalab_es_admin_este
			&& (int) $afectivalab_usuario->ID !== $afectivalab_yo;
		?>
		<li class="tabla__fila">
			<div class="tabla__principal">
				<strong><?php echo esc_html( $afectivalab_usuario->display_name ); ?></strong>
				<span class="tabla__meta">
					<?php echo esc_html( $afectivalab_usuario->user_email ); ?>
					<?php if ( $afectivalab_nhijos ) : ?>
						<span aria-hidden="true">&middot;</span>
						<?php
						printf(
							/* translators: %d: cantidad de perfiles de hijo. */
							esc_html( _n( '%d hijo', '%d hijos', $afectivalab_nhijos, 'afectivalab' ) ),
							absint( $afectivalab_nhijos )
						);
					endif;
					?>
				</span>
			</div>

			<?php if ( $afectivalab_es_admin_este ) : ?>
				<span class="estado estado--admin"><?php esc_html_e( 'Administrador', 'afectivalab' ); ?></span>
			<?php elseif ( ! $afectivalab_editable ) : ?>
				<span class="estado">
					<?php
					echo $afectivalab_rol_actual
						? esc_html( $afectivalab_roles[ $afectivalab_rol_actual ] )
						: esc_html__( 'Sin rol de la plataforma', 'afectivalab' );
					?>
				</span>
			<?php endif; ?>

			<div class="tabla__acciones">
				<?php if ( $afectivalab_editable ) : ?>
					<form method="post" class="tabla__rol">
						<?php wp_nonce_field( 'afectivalab_panel_usuario', 'afectivalab_panel_nonce' ); ?>
						<input type="hidden" name="afectivalab_panel_accion" value="cambiar_rol">
						<input type="hidden" name="user_id" value="<?php echo esc_attr( $afectivalab_usuario->ID ); ?>">

						<label class="screen-reader-text" for="rol-<?php echo esc_attr( $afectivalab_usuario->ID ); ?>">
							<?php esc_html_e( 'Rol', 'afectivalab' ); ?>
						</label>
						<select id="rol-<?php echo esc_attr( $afectivalab_usuario->ID ); ?>" name="rol">
							<?php foreach ( $afectivalab_roles as $afectivalab_slug => $afectivalab_nombre ) : ?>
								<option value="<?php echo esc_attr( $afectivalab_slug ); ?>" <?php selected( $afectivalab_rol_actual, $afectivalab_slug ); ?>>
									<?php echo esc_html( $afectivalab_nombre ); ?>
								</option>
							<?php endforeach; ?>
						</select>

						<button type="submit" class="tabla__rol-boton"><?php esc_html_e( 'Cambiar', 'afectivalab' ); ?></button>
					</form>
				<?php endif; ?>

				<a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $afectivalab_usuario->ID ) ); ?>">
					<?php esc_html_e( 'Ver ficha', 'afectivalab' ); ?>
				</a>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
