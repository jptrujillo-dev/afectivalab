<?php
/**
 * Avisos dentro de la plataforma — nada de correo ni WP-Cron todavía
 * (decisión del usuario: solo adentro del sitio, para no sumar una pieza de
 * infraestructura nueva sin necesidad). Dos cosas separadas que pide el
 * concepto:
 *
 * - Recordatorio de curso pendiente: si un curso está empezado pero hace
 *   tiempo que no se avanza, se lo hace notar en /panel en vez de mostrar
 *   siempre el mismo tono neutral de "continúa donde lo dejaron".
 * - "GPS de crianza": aviso de que el hijo está por cambiar de etapa por
 *   cumpleaños, con un adelanto de los temas que se vienen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dejar registrado que el hijo tuvo actividad real (una microclase recién
 * completada). Se llama desde afectivalab_marcar_clase(), el mismo punto que
 * usa inc/estrellas.php — así no hace falta acordarse de tocar esto en cada
 * lugar donde una clase se completa.
 *
 * @param int $hijo_id
 */
function afectivalab_hijo_marcar_actividad( $hijo_id ) {
	update_post_meta( $hijo_id, '_afectivalab_ultima_actividad', current_time( 'timestamp' ) );
}

/**
 * Días desde la última microclase completada de este hijo.
 *
 * @param int $hijo_id
 * @return int|null Null si todavía no completó ninguna clase.
 */
function afectivalab_hijo_dias_inactivo( $hijo_id ) {
	$marca = (int) get_post_meta( $hijo_id, '_afectivalab_ultima_actividad', true );

	if ( ! $marca ) {
		return null;
	}

	return (int) floor( ( current_time( 'timestamp' ) - $marca ) / DAY_IN_SECONDS );
}

/**
 * A partir de cuántos días sin avanzar se considera un curso "abandonado" y
 * se le cambia el tono al recordatorio. Valor de partida, no algo que haya
 * definido el cliente — fácil de ajustar el día que haya datos reales de uso.
 */
function afectivalab_dias_para_recordatorio() {
	return 7;
}

/**
 * "GPS de crianza": si el hijo está a 1 o 2 meses de cumplir años y ese
 * cumpleaños lo cambia de etapa, devuelve el aviso con la etapa que viene y
 * su enfoque — para que el padre pueda ir preparándose antes de que llegue.
 *
 * Solo tiene precisión de mes (no se guarda el día de nacimiento — ver
 * inc/hijos.php), así que la propia edad ya se actualiza desde el primer día
 * del mes de cumpleaños. Por eso la ventana de aviso es 1-2 meses ANTES: en
 * el mes del cumpleaños la etapa ya cambió, ya no es "por venir".
 *
 * @param int $hijo_id
 * @return array{meses: int, etapa: WP_Term, enfoque: string}|null
 */
function afectivalab_hijo_proximo_cambio_etapa( $hijo_id ) {
	$mes  = (int) get_post_meta( $hijo_id, '_afectivalab_nacimiento_mes', true );
	$anio = (int) get_post_meta( $hijo_id, '_afectivalab_nacimiento_anio', true );

	if ( ! $mes || ! $anio ) {
		return null;
	}

	$mes_actual        = (int) current_time( 'n' );
	$meses_para_cumple = ( $mes - $mes_actual + 12 ) % 12;

	if ( $meses_para_cumple < 1 || $meses_para_cumple > 2 ) {
		return null;
	}

	$edad_actual   = afectivalab_hijo_edad( $hijo_id );
	$etapa_actual  = afectivalab_hijo_etapa( $hijo_id );
	$etapa_proxima = afectivalab_etapa_para_edad( $edad_actual + 1 );

	// Nada que avisar si el cumpleaños no lo saca de la etapa donde ya está
	// (dentro del mismo rango de 3 años), o si con la edad nueva sale del
	// rango que cubre la plataforma (17 para arriba).
	if ( ! $etapa_proxima || ( $etapa_actual && $etapa_actual->term_id === $etapa_proxima->term_id ) ) {
		return null;
	}

	$etapas  = afectivalab_etapas();
	$enfoque = $etapas[ $etapa_proxima->slug ]['enfoque'] ?? '';

	return array(
		'meses'   => $meses_para_cumple,
		'etapa'   => $etapa_proxima,
		'enfoque' => $enfoque,
	);
}
