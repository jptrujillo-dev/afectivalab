<?php
/**
 * Misiones: la acción fuera de la pantalla que pide el concepto ("provocar
 * acciones reales, no solo consumo de contenido").
 *
 * Una microclase puede llevar, como mucho, una misión. Hay dos tipos:
 * - "casa": una actividad en familia que solo se marca como hecha.
 * - "taller": además pide subir una foto como evidencia.
 *
 * Decisión de producto (2026, confirmada por el usuario): cuando una clase
 * lleva misión, **resolver la misión es lo que completa la clase** — no
 * alcanza con ver el video. Si el padre elige "lo haré después", la clase
 * se queda pendiente y no desbloquea la siguiente; puede volver cuando
 * quiera. Las clases sin misión se siguen completando con el botón de
 * "marcar como vista" de siempre (ver inc/progreso.php).
 *
 * Las recompensas (10 monedas la de casa, 20 la de taller) son valores de
 * partida, no un balance de juego definido por el cliente — fáciles de
 * ajustar el día que haya una conversación real sobre economía del juego.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Los dos tipos de misión, con su ícono y su recompensa en monedas.
 */
function afectivalab_mision_tipos() {
	return array(
		'casa'   => array(
			'nombre'             => __( 'Misión en casa', 'afectivalab' ),
			'icono'              => 'leccion-mision-casa',
			'recompensa'         => 10,
			'requiere_evidencia' => false,
		),
		'taller' => array(
			'nombre'             => __( 'Misión tipo taller', 'afectivalab' ),
			'icono'              => 'leccion-mision-taller',
			'recompensa'         => 20,
			'requiere_evidencia' => true,
		),
	);
}

/**
 * La misión de una microclase, o null si no lleva ninguna.
 *
 * @param int $clase_id
 * @return array{tipo: string, texto: string, nombre: string, icono: string, recompensa: int, requiere_evidencia: bool}|null
 */
function afectivalab_clase_mision( $clase_id ) {
	$tipo  = get_post_meta( $clase_id, '_afectivalab_mision_tipo', true );
	$tipos = afectivalab_mision_tipos();

	if ( ! isset( $tipos[ $tipo ] ) ) {
		return null;
	}

	$texto = get_post_meta( $clase_id, '_afectivalab_mision_texto', true );

	if ( '' === trim( (string) $texto ) ) {
		return null;
	}

	return array_merge( $tipos[ $tipo ], array( 'tipo' => $tipo, 'texto' => $texto ) );
}

/**
 * El registro de una misión para un hijo: en qué quedó y, si aplica, la
 * evidencia subida. Vive en el perfil del hijo, igual que el progreso — es
 * la familia la que hace la misión, no la cuenta del padre en abstracto.
 *
 * @param int $hijo_id
 * @param int $clase_id
 * @return array{estado: string, evidencia_id: int, fecha: string}
 */
function afectivalab_mision_registro( $hijo_id, $clase_id ) {
	$todas = get_post_meta( $hijo_id, '_afectivalab_misiones', true );
	$todas = is_array( $todas ) ? $todas : array();

	$registro = $todas[ absint( $clase_id ) ] ?? array();

	return array(
		'estado'       => $registro['estado'] ?? 'pendiente',
		'evidencia_id' => absint( $registro['evidencia_id'] ?? 0 ),
		'fecha'        => $registro['fecha'] ?? '',
	);
}

/**
 * @param int $hijo_id
 * @param int $clase_id
 * @return string 'pendiente' | 'despues' | 'hecha'
 */
function afectivalab_mision_estado( $hijo_id, $clase_id ) {
	return afectivalab_mision_registro( $hijo_id, $clase_id )['estado'];
}

/**
 * Guarda el registro de una misión.
 */
function afectivalab_guardar_mision_registro( $hijo_id, $clase_id, $registro ) {
	$todas = get_post_meta( $hijo_id, '_afectivalab_misiones', true );
	$todas = is_array( $todas ) ? $todas : array();

	$todas[ absint( $clase_id ) ] = $registro;

	update_post_meta( $hijo_id, '_afectivalab_misiones', $todas );
}

/**
 * Monedas y XP acumulados por el padre. **A nivel de cuenta, no de hijo** —
 * el concepto dice explícitamente que la experiencia es "del padre": si
 * tiene tres hijos, hace las tres rutas con la misma mochila de logros.
 */
function afectivalab_padre_monedas( $user_id ) {
	return absint( get_user_meta( $user_id, '_afectivalab_monedas', true ) );
}

function afectivalab_padre_xp( $user_id ) {
	return absint( get_user_meta( $user_id, '_afectivalab_xp', true ) );
}

/**
 * Suma monedas y XP a la cuenta del padre. Nunca resta: no hay nada que
 * "gastar" todavía, así que el único movimiento posible es hacia arriba.
 *
 * @param int $user_id
 * @param int $cantidad
 */
function afectivalab_otorgar_recompensa( $user_id, $cantidad ) {
	$cantidad = absint( $cantidad );

	if ( ! $cantidad ) {
		return;
	}

	update_user_meta( $user_id, '_afectivalab_monedas', afectivalab_padre_monedas( $user_id ) + $cantidad );
	update_user_meta( $user_id, '_afectivalab_xp', afectivalab_padre_xp( $user_id ) + $cantidad );
}

/**
 * Marca la misión de un hijo como "la haremos después". No completa la
 * clase ni da recompensa — solo dice que la familia vio la misión y todavía
 * no la hizo. Se puede volver a marcar como hecha más adelante.
 */
function afectivalab_posponer_mision( $hijo_id, $clase_id ) {
	// Si ya estaba hecha, posponerla la desharía y le quitaría al padre una
	// recompensa que ya cobró — eso no debe poder pasar desde este botón.
	if ( 'hecha' === afectivalab_mision_estado( $hijo_id, $clase_id ) ) {
		return;
	}

	afectivalab_guardar_mision_registro(
		$hijo_id,
		$clase_id,
		array(
			'estado'       => 'despues',
			'evidencia_id' => 0,
			'fecha'        => current_time( 'mysql' ),
		)
	);
}

/**
 * Completa la misión de un hijo: guarda el registro, marca la clase como
 * completada (una misión resuelta es lo que avanza el camino) y le da al
 * padre su recompensa — **solo la primera vez**, para que reenviar el
 * formulario o volver a visitar la clase no duplique monedas.
 *
 * @param int $hijo_id
 * @param int $clase_id
 * @param int $evidencia_id ID del attachment de la foto, si la misión es de taller.
 */
function afectivalab_completar_mision( $hijo_id, $clase_id, $evidencia_id = 0 ) {
	$ya_estaba_hecha = 'hecha' === afectivalab_mision_estado( $hijo_id, $clase_id );

	afectivalab_guardar_mision_registro(
		$hijo_id,
		$clase_id,
		array(
			'estado'       => 'hecha',
			'evidencia_id' => absint( $evidencia_id ),
			'fecha'        => current_time( 'mysql' ),
		)
	);

	afectivalab_marcar_clase( $hijo_id, $clase_id, true );

	if ( $ya_estaba_hecha ) {
		return;
	}

	$mision = afectivalab_clase_mision( $clase_id );
	$hijo   = get_post( $hijo_id );

	if ( $mision && $hijo ) {
		afectivalab_otorgar_recompensa( (int) $hijo->post_author, $mision['recompensa'] );
	}
}

/**
 * A dónde volver después de resolver la misión: la siguiente clase del
 * curso, o el curso mismo si esa clase era la última. Mismo criterio que
 * afectivalab_handle_clase_form() en inc/progreso.php.
 */
function afectivalab_mision_redirect_destino( $hijo_id, $clase_id ) {
	$curso_id = (int) get_post_meta( $clase_id, '_afectivalab_curso', true );
	$progreso = afectivalab_progreso_curso( $hijo_id, $curso_id );

	return $progreso['siguiente'] ? get_permalink( $progreso['siguiente'] ) : get_permalink( $curso_id );
}

/**
 * Procesa el formulario de la misión de una microclase: "ya la hicimos" (con
 * la foto, si la misión es de taller) o "la haremos después".
 *
 * Es el equivalente, para clases con misión, de
 * afectivalab_handle_clase_form() — pasa por las mismas comprobaciones
 * (nonce propio de la clase, que el hijo la tenga desbloqueada) antes de
 * tocar nada.
 */
function afectivalab_handle_mision_form( $clase_id, $hijo_id ) {
	$accion = isset( $_POST['afectivalab_mision_accion'] ) ? sanitize_key( wp_unslash( $_POST['afectivalab_mision_accion'] ) ) : '';

	if ( ! in_array( $accion, array( 'hecha', 'despues' ), true ) ) {
		return;
	}

	if ( ! isset( $_POST['afectivalab_mision_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_mision_nonce'] ) ), 'afectivalab_mision_' . $clase_id ) ) {
		return;
	}

	if ( ! afectivalab_clase_desbloqueada( $hijo_id, $clase_id ) ) {
		return;
	}

	$mision = afectivalab_clase_mision( $clase_id );

	if ( ! $mision ) {
		return;
	}

	if ( 'despues' === $accion ) {
		afectivalab_posponer_mision( $hijo_id, $clase_id );
		return;
	}

	$evidencia_id = 0;

	if ( $mision['requiere_evidencia'] ) {
		$subida = afectivalab_subir_evidencia_mision( $clase_id );

		// Sin evidencia no hay "ya la hicimos" en una misión de taller: es
		// justamente lo que la distingue de una misión de casa. Se deja la
		// clase como estaba (el padre sigue viendo el error abajo, en vez
		// de una clase completada sin foto).
		if ( is_wp_error( $subida ) || ! $subida ) {
			return;
		}

		$evidencia_id = $subida;
	}

	afectivalab_completar_mision( $hijo_id, $clase_id, $evidencia_id );

	wp_safe_redirect( afectivalab_mision_redirect_destino( $hijo_id, $clase_id ) );
	exit;
}

/**
 * Sube la foto de evidencia de una misión de taller.
 *
 * Mismas reglas que la foto de perfil en inc/account.php: JPG/PNG/WEBP,
 * máximo 3MB — es la familia subiendo una foto desde el celular, el mismo
 * caso de uso.
 *
 * @param int $clase_id
 * @return int|false|WP_Error
 */
function afectivalab_subir_evidencia_mision( $clase_id ) {
	if ( empty( $_FILES['evidencia'] ) || empty( $_FILES['evidencia']['name'] ) ) {
		return false;
	}

	$allowed_types = array( 'image/jpeg', 'image/png', 'image/webp' );
	$file_type     = wp_check_filetype( $_FILES['evidencia']['name'] );

	if ( ! in_array( $file_type['type'], $allowed_types, true ) ) {
		return new WP_Error( 'tipo_invalido', __( 'La evidencia debe ser una foto JPG, PNG o WEBP.', 'afectivalab' ) );
	}

	if ( $_FILES['evidencia']['size'] > 3 * MB_IN_BYTES ) {
		return new WP_Error( 'muy_pesado', __( 'La foto no debe pesar más de 3MB.', 'afectivalab' ) );
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$attachment_id = media_handle_upload( 'evidencia', $clase_id );

	return is_wp_error( $attachment_id ) ? new WP_Error( 'subida_fallida', __( 'No pudimos subir la foto, intenta de nuevo.', 'afectivalab' ) ) : $attachment_id;
}
