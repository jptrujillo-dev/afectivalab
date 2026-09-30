<?php
/**
 * Caso interactivo: la mecánica de dilema con opciones que el concepto marca
 * como "diferenciadora" (sección 5) — no es un ✅/❌ simple, cada opción trae
 * una explicación razonada, y después de responder el caso **continúa** con
 * una nueva pregunta, como una historia ramificada corta.
 *
 * Alcance elegido: exactamente **dos pasos como máximo** (la situación
 * inicial y una pregunta de seguimiento), con hasta 4 opciones cada uno. Es
 * justo lo que describe el ejemplo del concepto ("tu hijo dice que no quiere
 * volver al colegio" → responde → "¿qué harías ahora?"), no una profundidad
 * arbitraria — construir un editor de árbol de decisión abierto sería
 * resolver un problema que nadie pidió. El segundo paso es opcional: un caso
 * de un solo paso también es válido.
 *
 * A diferencia de la misión, **resolver el caso no completa la clase** — es
 * informativo y queda guardado por hijo, pero la clase se sigue completando
 * por su propio camino (video o misión). No se extendió la regla de "esto
 * completa la clase" sin que el usuario lo pidiera para este elemento.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Las opciones no vacías de un paso guardado.
 *
 * Se guarda espacio para 4 opciones siempre, pero solo cuentan las que
 * tienen texto — así el instructor puede escribir un dilema de 2, 3 o 4
 * opciones sin que hagan falta filas vacías fantasma.
 *
 * @param int    $clase_id
 * @param string $paso 'paso1' o 'paso2'.
 * @return array<int, array{texto: string, retroalimentacion: string, recomendada: bool}>
 */
function afectivalab_caso_opciones( $clase_id, $paso ) {
	$opciones = array();

	for ( $i = 1; $i <= 4; $i++ ) {
		$texto = get_post_meta( $clase_id, "_afectivalab_caso_{$paso}_opcion_{$i}_texto", true );

		if ( '' === trim( (string) $texto ) ) {
			continue;
		}

		$opciones[] = array(
			'texto'              => $texto,
			'retroalimentacion'  => get_post_meta( $clase_id, "_afectivalab_caso_{$paso}_opcion_{$i}_feedback", true ),
			'recomendada'        => (bool) get_post_meta( $clase_id, "_afectivalab_caso_{$paso}_opcion_{$i}_recomendada", true ),
		);
	}

	return $opciones;
}

/**
 * El caso interactivo de una microclase, o null si no tiene (la situación
 * del paso 1 está vacía — el mismo criterio que usa la misión: el texto es
 * el interruptor, no una casilla aparte).
 *
 * @param int $clase_id
 * @return array{paso1: array, paso2: array|null}|null
 */
function afectivalab_clase_caso( $clase_id ) {
	$situacion1 = get_post_meta( $clase_id, '_afectivalab_caso_paso1_situacion', true );

	if ( '' === trim( (string) $situacion1 ) ) {
		return null;
	}

	$opciones1 = afectivalab_caso_opciones( $clase_id, 'paso1' );

	// Un dilema necesita, como mínimo, dos caminos entre los que elegir.
	if ( count( $opciones1 ) < 2 ) {
		return null;
	}

	$caso = array(
		'paso1' => array(
			'situacion' => $situacion1,
			'opciones'  => $opciones1,
		),
		'paso2' => null,
	);

	$situacion2 = get_post_meta( $clase_id, '_afectivalab_caso_paso2_situacion', true );

	if ( '' !== trim( (string) $situacion2 ) ) {
		$opciones2 = afectivalab_caso_opciones( $clase_id, 'paso2' );

		if ( count( $opciones2 ) >= 2 ) {
			$caso['paso2'] = array(
				'situacion' => $situacion2,
				'opciones'  => $opciones2,
			);
		}
	}

	return $caso;
}

/**
 * Qué respondió un hijo, por paso. Vive en su perfil, igual que las
 * misiones: es la familia la que responde, no la cuenta del padre en
 * abstracto.
 *
 * @param int $hijo_id
 * @param int $clase_id
 * @return array{paso1: int|null, paso2: int|null}
 */
function afectivalab_caso_registro( $hijo_id, $clase_id ) {
	$todos    = get_post_meta( $hijo_id, '_afectivalab_casos', true );
	$todos    = is_array( $todos ) ? $todos : array();
	$registro = $todos[ absint( $clase_id ) ] ?? array();

	return array(
		'paso1' => isset( $registro['paso1'] ) ? absint( $registro['paso1'] ) : null,
		'paso2' => isset( $registro['paso2'] ) ? absint( $registro['paso2'] ) : null,
	);
}

/**
 * Guarda qué opción eligió un hijo en un paso del caso. No se puede
 * responder el paso 2 sin haber respondido antes el paso 1 — sería mostrar
 * una pregunta de seguimiento sin la situación que la origina.
 *
 * @param int    $hijo_id
 * @param int    $clase_id
 * @param string $paso 'paso1' o 'paso2'.
 * @param int    $opcion_index Índice (0-based) dentro de las opciones no vacías de ese paso.
 * @return bool
 */
function afectivalab_caso_responder( $hijo_id, $clase_id, $paso, $opcion_index ) {
	$caso = afectivalab_clase_caso( $clase_id );

	if ( ! $caso || ! isset( $caso[ $paso ] ) ) {
		return false;
	}

	$opcion_index = absint( $opcion_index );

	if ( $opcion_index >= count( $caso[ $paso ]['opciones'] ) ) {
		return false;
	}

	if ( 'paso2' === $paso && null === afectivalab_caso_registro( $hijo_id, $clase_id )['paso1'] ) {
		return false;
	}

	$todos = get_post_meta( $hijo_id, '_afectivalab_casos', true );
	$todos = is_array( $todos ) ? $todos : array();

	$todos[ absint( $clase_id ) ][ $paso ] = $opcion_index;

	update_post_meta( $hijo_id, '_afectivalab_casos', $todos );

	return true;
}

/**
 * ¿Ya se resolvió el caso entero? (el paso 1, y el paso 2 si el caso tiene
 * uno).
 *
 * @param int $hijo_id
 * @param int $clase_id
 */
function afectivalab_caso_completo( $hijo_id, $clase_id ) {
	$caso = afectivalab_clase_caso( $clase_id );

	if ( ! $caso ) {
		return false;
	}

	$registro = afectivalab_caso_registro( $hijo_id, $clase_id );

	if ( null === $registro['paso1'] ) {
		return false;
	}

	return ! $caso['paso2'] || null !== $registro['paso2'];
}

/**
 * Procesa el formulario de respuesta de un paso del caso.
 *
 * A diferencia de las misiones o de "marcar como vista", **no redirige**: la
 * respuesta se muestra en la misma página, revelando el paso siguiente (o el
 * cierre del caso) sin navegar a otro sitio. Mismo patrón que
 * afectivalab_handle_hijo_forms() en inc/hijos.php.
 */
function afectivalab_handle_caso_form( $clase_id, $hijo_id ) {
	if ( empty( $_POST['afectivalab_caso_paso'] ) ) {
		return;
	}

	$paso = sanitize_key( wp_unslash( $_POST['afectivalab_caso_paso'] ) );

	if ( ! in_array( $paso, array( 'paso1', 'paso2' ), true ) ) {
		return;
	}

	if ( ! isset( $_POST['afectivalab_caso_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afectivalab_caso_nonce'] ) ), 'afectivalab_caso_' . $clase_id ) ) {
		return;
	}

	if ( ! afectivalab_clase_desbloqueada( $hijo_id, $clase_id ) ) {
		return;
	}

	afectivalab_caso_responder( $hijo_id, $clase_id, $paso, absint( $_POST['afectivalab_caso_opcion'] ?? -1 ) );
}
