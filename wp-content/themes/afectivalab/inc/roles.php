<?php
/**
 * Roles propios de la plataforma.
 *
 * Los roles viven en la base de datos, no en el código: add_role() no hace
 * nada si el rol ya existe, así que cambiar capacidades aquí no basta para
 * que se apliquen en un sitio ya instalado. Por eso hay un número de versión:
 * al subirlo, los roles se vuelven a escribir una vez. **Si se tocan las
 * capacidades de abajo, hay que subir AFECTIVALAB_ROLES_VERSION.**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AFECTIVALAB_ROLES_VERSION = 2;

/**
 * Capacidades del instructor sobre un tipo de contenido: puede crear, editar
 * y publicar lo suyo, y editar lo de sus colegas (el contenido se produce en
 * equipo), pero no borrar lo que escribió otro — eso queda para el
 * administrador.
 */
function afectivalab_caps_instructor( $plural ) {
	return array(
		"edit_{$plural}",
		"edit_others_{$plural}",
		"edit_published_{$plural}",
		"edit_private_{$plural}",
		"publish_{$plural}",
		"read_private_{$plural}",
		"delete_{$plural}",
		"delete_published_{$plural}",
	);
}

function afectivalab_sync_roles() {
	if ( (int) get_option( 'afectivalab_roles_version' ) === AFECTIVALAB_ROLES_VERSION ) {
		return;
	}

	// Padre/madre: la cuenta que se crea desde /registro. Necesita
	// upload_files para su foto de perfil y, más adelante, para la evidencia
	// de las misiones tipo taller.
	remove_role( 'afectivalab_padre' );
	add_role(
		'afectivalab_padre',
		__( 'Padre/Madre', 'afectivalab' ),
		array(
			'read'         => true,
			'upload_files' => true,
		)
	);

	// No lleva edit_posts a propósito: el instructor administra cursos y
	// microclases, no las entradas del blog ni las páginas del sitio.
	$caps_instructor = array(
		'read'         => true,
		'upload_files' => true,
	);

	foreach ( array_merge( afectivalab_caps_instructor( 'afectivalab_cursos' ), afectivalab_caps_instructor( 'afectivalab_clases' ) ) as $cap ) {
		$caps_instructor[ $cap ] = true;
	}

	remove_role( 'afectivalab_instructor' );
	add_role( 'afectivalab_instructor', __( 'Instructor', 'afectivalab' ), $caps_instructor );

	// Al usar capacidades propias en vez de las de "post", el administrador
	// tampoco las tiene: sin esto, un admin no vería los menús de Cursos ni
	// de Microclases.
	$admin = get_role( 'administrator' );

	if ( $admin ) {
		$caps_admin = array_merge(
			afectivalab_cpt_caps( 'afectivalab_cursos' ),
			afectivalab_cpt_caps( 'afectivalab_clases' ),
			array( 'manage_afectivalab_contenido' )
		);

		foreach ( $caps_admin as $cap ) {
			$admin->add_cap( $cap );
		}
	}

	update_option( 'afectivalab_roles_version', AFECTIVALAB_ROLES_VERSION );
}
add_action( 'init', 'afectivalab_sync_roles', 10 );
