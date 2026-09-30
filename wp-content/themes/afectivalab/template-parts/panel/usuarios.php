<?php
/**
 * Panel > Usuarios.
 *
 * Solo para quien administra el sitio. Deja ver las cuentas y cambiar entre
 * los dos roles de la plataforma; **no borra cuentas ni reparte el rol de
 * administrador** — ver afectivalab_panel_roles_asignables() en
 * inc/panel-admin.php.
 *
 * El rol se guarda solo al elegirlo (assets/js/equipo.js); sin JavaScript
 * queda el botón "Cambiar" de siempre.
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

$afectivalab_roles = afectivalab_panel_roles_asignables();
$afectivalab_puede = current_user_can( 'promote_users' );
$afectivalab_yo    = get_current_user_id();

// Primero se clasifica a cada uno, para poder contar los filtros.
$afectivalab_filas  = array();
$afectivalab_conteo = array( 'padre' => 0, 'instructor' => 0, 'admin' => 0 );

foreach ( $afectivalab_usuarios as $afectivalab_usuario ) {
	$afectivalab_es_admin_este = user_can( $afectivalab_usuario, 'manage_options' );
	$afectivalab_rol_actual    = '';

	foreach ( (array) $afectivalab_usuario->roles as $afectivalab_rol ) {
		if ( isset( $afectivalab_roles[ $afectivalab_rol ] ) ) {
			$afectivalab_rol_actual = $afectivalab_rol;
			break;
		}
	}

	if ( $afectivalab_es_admin_este ) {
		$afectivalab_tipo = 'admin';
	} elseif ( 'afectivalab_instructor' === $afectivalab_rol_actual ) {
		$afectivalab_tipo = 'instructor';
	} elseif ( 'afectivalab_padre' === $afectivalab_rol_actual ) {
		$afectivalab_tipo = 'padre';
	} else {
		$afectivalab_tipo = 'otro';
	}

	if ( isset( $afectivalab_conteo[ $afectivalab_tipo ] ) ) {
		$afectivalab_conteo[ $afectivalab_tipo ]++;
	}

	$afectivalab_filas[] = array(
		'usuario'  => $afectivalab_usuario,
		'admin'    => $afectivalab_es_admin_este,
		'rol'      => $afectivalab_rol_actual,
		'tipo'     => $afectivalab_tipo,
		// Ni a uno mismo ni a un administrador: cambiarse el propio rol es la
		// forma más fácil de quedarse fuera, y degradar a un admin desde el
		// front no debería ser posible.
		'editable' => $afectivalab_puede && ! $afectivalab_es_admin_este && (int) $afectivalab_usuario->ID !== $afectivalab_yo,
	);
}

$afectivalab_filtros = array(
	''           => array( __( 'Todos', 'afectivalab' ), count( $afectivalab_filas ) ),
	'padre'      => array( __( 'Familias', 'afectivalab' ), $afectivalab_conteo['padre'] ),
	'instructor' => array( __( 'Instructores', 'afectivalab' ), $afectivalab_conteo['instructor'] ),
	'admin'      => array( __( 'Administradores', 'afectivalab' ), $afectivalab_conteo['admin'] ),
);
?>

<div class="eq-seccion__head">
	<div>
		<h2><?php esc_html_e( 'Usuarios', 'afectivalab' ); ?></h2>
		<p><?php esc_html_e( 'Puedes cambiar entre Familia e Instructor. Crear administradores o borrar cuentas se hace en el escritorio, a propósito: son acciones difíciles de deshacer.', 'afectivalab' ); ?></p>
	</div>
	<a class="btn btn-secondary" href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>">
		<?php afectivalab_icon( 'eq-mas' ); ?>
		<?php esc_html_e( 'Añadir usuario', 'afectivalab' ); ?>
	</a>
</div>

<div class="eq-herramientas" data-eq-filtros="eq-lista-usuarios" hidden>
	<label class="eq-buscar">
		<?php afectivalab_icon( 'eq-buscar' ); ?>
		<span class="screen-reader-text"><?php esc_html_e( 'Buscar usuario', 'afectivalab' ); ?></span>
		<input type="search" placeholder="<?php esc_attr_e( 'Buscar por nombre o correo…', 'afectivalab' ); ?>" data-eq-buscar>
	</label>

	<div class="eq-chips" role="group" aria-label="<?php esc_attr_e( 'Filtrar por rol', 'afectivalab' ); ?>">
		<?php foreach ( $afectivalab_filtros as $afectivalab_valor => $afectivalab_filtro ) : ?>
			<button type="button" class="eq-chip-filtro<?php echo '' === $afectivalab_valor ? ' is-activo' : ''; ?>" data-eq-filtro="rol" data-valor="<?php echo esc_attr( $afectivalab_valor ); ?>" aria-pressed="<?php echo '' === $afectivalab_valor ? 'true' : 'false'; ?>">
				<?php echo esc_html( $afectivalab_filtro[0] ); ?> <span><?php echo absint( $afectivalab_filtro[1] ); ?></span>
			</button>
		<?php endforeach; ?>
	</div>
</div>

<ul class="eq-filas" id="eq-lista-usuarios">
	<?php foreach ( $afectivalab_filas as $afectivalab_fila ) : ?>
		<?php
		$afectivalab_usuario = $afectivalab_fila['usuario'];
		$afectivalab_nhijos  = count( afectivalab_get_hijos( $afectivalab_usuario->ID ) );
		?>
		<li
			class="eq-fila eq-fila--usuario"
			data-eq-item
			data-texto="<?php echo esc_attr( remove_accents( mb_strtolower( $afectivalab_usuario->display_name . ' ' . $afectivalab_usuario->user_email ) ) ); ?>"
			data-rol="<?php echo esc_attr( $afectivalab_fila['tipo'] ); ?>"
		>
			<?php echo afectivalab_get_avatar_html( $afectivalab_usuario, 40 ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado dentro del helper. ?>

			<div class="eq-fila__principal">
				<strong class="eq-fila__titulo"><?php echo esc_html( $afectivalab_usuario->display_name ); ?></strong>
				<span class="eq-fila__meta">
					<span class="eq-fila__dato"><?php echo esc_html( $afectivalab_usuario->user_email ); ?></span>
					<?php if ( $afectivalab_nhijos ) : ?>
						<span class="eq-chip"><?php echo esc_html( sprintf( /* translators: %d: cantidad de perfiles de hijo. */ _n( '%d hijo', '%d hijos', $afectivalab_nhijos, 'afectivalab' ), $afectivalab_nhijos ) ); ?></span>
					<?php endif; ?>
					<?php
					// Solo quien puede tener una suscripción de verdad — no tiene
					// sentido mostrar "sin suscripción" al lado de una cuenta de
					// administrador o instructor.
					if ( in_array( 'afectivalab_padre', (array) $afectivalab_usuario->roles, true ) ) :
						$afectivalab_susc = afectivalab_suscripcion_resumen( $afectivalab_usuario->ID );
						?>
						<span class="eq-chip eq-chip--suscripcion <?php echo $afectivalab_susc['activa'] ? 'eq-chip--verde' : 'eq-chip--amarillo'; ?>"><?php echo esc_html( $afectivalab_susc['label'] ); ?></span>
					<?php endif; ?>
				</span>
			</div>

			<?php if ( $afectivalab_fila['editable'] ) : ?>
				<form method="post" class="eq-rol" data-eq-ajax="rol">
					<?php wp_nonce_field( 'afectivalab_panel_usuario', 'afectivalab_panel_nonce', false ); ?>
					<input type="hidden" name="afectivalab_panel_accion" value="cambiar_rol">
					<input type="hidden" name="user_id" value="<?php echo esc_attr( $afectivalab_usuario->ID ); ?>">

					<label class="screen-reader-text" for="rol-<?php echo esc_attr( $afectivalab_usuario->ID ); ?>">
						<?php esc_html_e( 'Rol', 'afectivalab' ); ?>
					</label>
					<select class="eq-input eq-input--chico" id="rol-<?php echo esc_attr( $afectivalab_usuario->ID ); ?>" name="rol" data-eq-auto data-anterior="<?php echo esc_attr( $afectivalab_fila['rol'] ); ?>">
						<?php foreach ( $afectivalab_roles as $afectivalab_slug => $afectivalab_nombre ) : ?>
							<option value="<?php echo esc_attr( $afectivalab_slug ); ?>" <?php selected( $afectivalab_fila['rol'], $afectivalab_slug ); ?>>
								<?php echo esc_html( 'afectivalab_padre' === $afectivalab_slug ? __( 'Familia', 'afectivalab' ) : $afectivalab_nombre ); ?>
							</option>
						<?php endforeach; ?>
					</select>

					<button type="submit" class="btn btn-secondary eq-boton-chico eq-solo-sin-js"><?php esc_html_e( 'Cambiar', 'afectivalab' ); ?></button>
				</form>
			<?php else : ?>
				<span class="eq-estado<?php echo $afectivalab_fila['admin'] ? ' is-admin' : ''; ?>">
					<?php
					if ( $afectivalab_fila['admin'] ) {
						esc_html_e( 'Administrador', 'afectivalab' );
					} elseif ( $afectivalab_fila['rol'] ) {
						echo esc_html( 'afectivalab_padre' === $afectivalab_fila['rol'] ? __( 'Familia', 'afectivalab' ) : $afectivalab_roles[ $afectivalab_fila['rol'] ] );
					} else {
						esc_html_e( 'Sin rol de la plataforma', 'afectivalab' );
					}
					?>
				</span>
			<?php endif; ?>

			<div class="eq-fila__acciones">
				<a class="eq-boton-icono" href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $afectivalab_usuario->ID ) ); ?>" title="<?php esc_attr_e( 'Ver ficha en el escritorio', 'afectivalab' ); ?>">
					<?php afectivalab_icon( 'menu-cuenta' ); ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Ver ficha', 'afectivalab' ); ?></span>
				</a>
			</div>
		</li>
	<?php endforeach; ?>
</ul>

<p class="eq-sin-resultados" data-eq-sin-resultados="eq-lista-usuarios" hidden>
	<?php esc_html_e( 'Ningún usuario coincide con la búsqueda.', 'afectivalab' ); ?>
</p>
