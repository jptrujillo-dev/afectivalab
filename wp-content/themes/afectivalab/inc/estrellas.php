<?php
/**
 * Estrellas: una por cada microclase terminada, lleve o no misión.
 *
 * Las monedas premian la acción real de una misión resuelta (ver
 * inc/misiones.php); las clases que no llevan misión no daban ningún
 * reconocimiento. Las estrellas llenan ese hueco — es el "aparece un check"
 * que menciona el concepto, aplicado a cualquier microclase.
 *
 * Viven en el perfil del **hijo**, no en la cuenta del padre: a diferencia de
 * monedas/XP (la mochila del padre, compartida entre sus hijos), las
 * estrellas son la colección propia de cada hijo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $hijo_id
 * @return int
 */
function afectivalab_hijo_estrellas( $hijo_id ) {
	return absint( get_post_meta( $hijo_id, '_afectivalab_estrellas', true ) );
}

/**
 * Suma una estrella al hijo. Solo la llama afectivalab_marcar_clase() —
 * nunca directamente — para que no haya forma de dar una estrella sin que la
 * clase quede realmente marcada como completada.
 *
 * @param int $hijo_id
 */
function afectivalab_otorgar_estrella( $hijo_id ) {
	update_post_meta( $hijo_id, '_afectivalab_estrellas', afectivalab_hijo_estrellas( $hijo_id ) + 1 );
}
