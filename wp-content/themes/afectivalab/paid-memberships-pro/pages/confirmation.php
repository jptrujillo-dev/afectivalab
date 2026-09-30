<?php
/**
 * Template: Confirmation
 * Version: 3.8
 *
 * Override del theme sobre pages/confirmation.php de Paid Memberships Pro
 * (mecanismo oficial: pmpro_get_template_path_to_load() busca primero en
 * wp-content/themes/afectivalab/paid-memberships-pro/{type}/{page}.php
 * antes de caer al plugin — ver includes/page-templates.php).
 *
 * A diferencia de checkout.php (que solo agrega clases sobre el HTML
 * genérico de PMPro), acá se reemplaza el marcado del bloque de bienvenida
 * por uno propio: una tarjeta con ícono de éxito, en vez del texto suelto
 * sin fondo que imprime el plugin — y el recibo (pmpro_loadTemplate
 * 'invoice', que trae su propio wrapper .pmpro con su propio padding) se
 * separa con un margin-top que se controla acá mismo, no dejado a que dos
 * paddings de wrappers ajenos colapsen bien solos (eso fue lo que generaba
 * el hueco grande y descontrolado entre el texto y la tarjeta del recibo
 * en el intento anterior). Toda la lógica PHP de PMPro (armado del mensaje,
 * mensaje de nivel personalizado, estado "pago pendiente", nivel gratis)
 * se conserva igual que el original — solo cambia el HTML alrededor.
 *
 * Importante para el futuro: si PMPro actualiza esta plantilla en una
 * versión mayor del plugin, hay que revisar si esos cambios (lógica PHP)
 * también aplican acá y, si corresponde, subir el número de "Version" de
 * este header al de la nueva versión — mientras no coincidan, PMPro deja
 * de usar esta copia por su cuenta y vuelve al original (ver
 * afectivalab_pmpro_forzar_plantilla_confirmacion() en inc/suscripciones.php,
 * que además fuerza que la copia se use aunque la opción de PMPro para
 * esto esté en 'no').
 *
 * @link https://www.paidmembershipspro.com/documentation/templates/
 *
 * @version 3.8
 *
 * @author Paid Memberships Pro
 */
global $wpdb, $pmpro_invoice, $pmpro_msg, $pmpro_msgt;

// If this file is loaded, $pmpro_invoice should have been set by preheaders/confirmation.php. If not, show an error.
// Below, we should still check if $pmpro_invoice is empty as there are edge cases such as saving a page with the Confirmation block in the block editor.
if ( empty( $pmpro_invoice ) ) {
	$pmpro_msg = __( 'There was an error retrieving your order. Please contact the site owner.', 'paid-memberships-pro' );
	$pmpro_msgt = 'pmpro_error';
}

// Output page contents.
?>
<div class="suscripcion-confirmacion">
	<div class="suscripcion-confirmacion__tarjeta">
		<?php
			// Show message if it was passed in.
			if ( $pmpro_msg ) {
				?>
				<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ) ); ?>"><?php echo wp_kses_post( $pmpro_msg );?></div>
				<?php
			}

			// Check that we have an order.
			if ( ! empty( $pmpro_invoice ) ) {
				$pmpro_invoice->getUser();
				$pmpro_invoice->getMembershipLevel();
				if ( ! empty( $pmpro_invoice->user ) && ! empty( $pmpro_invoice->membership_level ) ) {
					$pmpro_invoice->user->membership_level = $pmpro_invoice->membership_level; // Backwards compatibility.
				}

				if ( 'success' == $pmpro_invoice->status ) {
					?>
					<div class="suscripcion-confirmacion__icono" aria-hidden="true">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5L9.5 17L19 7"/></svg>
					</div>
					<?php
				}

				// Start building the confirmation message.
				if ( 'success' != $pmpro_invoice->status ) {
					$confirmation_message = '<p>' . sprintf(__('Thank you for your membership to %1$s. Your %2$s membership will be activated once the payment has been completed.', 'paid-memberships-pro' ), get_bloginfo("name"), $pmpro_invoice->membership_level->name) . '</p>';
				} else {
					$confirmation_message = '<p>' . sprintf(__('Thank you for your membership to %s. Your %s membership is now active.', 'paid-memberships-pro' ), get_bloginfo("name"), $pmpro_invoice->membership_level->name) . '</p>';
				}

				// Add the level confirmation message if set and the order is successful.
				if ( 'success' == $pmpro_invoice->status ) {
					$level_message = $wpdb->get_var("SELECT confirmation FROM $wpdb->pmpro_membership_levels WHERE id = '" . intval( $pmpro_invoice->membership_id ) . "' LIMIT 1");
					if ( ! empty( $level_message ) ) {
						$confirmation_message .= '<div class="' . esc_attr( pmpro_get_element_class( 'pmpro_confirmation-level-message' ) ) . '">';
						$confirmation_message .= wpautop( stripslashes( $level_message ) );
						$confirmation_message .= '</div>';
					}
				}

				// Add some details to the confirmation message about the order.
				// Texto propio en vez del original de PMPro ("Below are details...
				// below") — acá el recibo ya no queda "debajo", queda detrás del
				// botón "Ver pedido" (ver el modal más abajo en este archivo).
				if ( ! pmpro_isLevelFree( $pmpro_invoice->membership_level ) ) {
					$confirmation_message .= '<p>' . sprintf( __( 'Te enviamos por correo una copia de tu pedido a %s. También podés verlo cuando quieras tocando el botón de abajo.', 'afectivalab' ), $pmpro_invoice->user->user_email ) . '</p>';
				} else {
					$confirmation_message .= '<p>' . sprintf( __( 'Below are details about your membership account. A welcome email has been sent to %s.', 'paid-memberships-pro' ), $pmpro_invoice->user->user_email ) . '</p>';
				}

				/**
				 * Allow devs to filter the confirmation message.
				 * We also have a function in includes/filters.php that applies the the_content filters to this message.
				 * @param string $confirmation_message The confirmation message.
				 * @param object $pmpro_invoice The PMPro Order object.
				 */
				$confirmation_message = apply_filters( "pmpro_confirmation_message", $confirmation_message, $pmpro_invoice );
				?>
				<div class="suscripcion-confirmacion__texto">
					<?php echo wp_kses_post( $confirmation_message ); ?>
				</div>
				<?php

				// Botón que abre el recibo completo en un modal (ver
				// assets/js/pmpro-confirmacion.js) en vez de mostrarlo siempre
				// debajo — deja la página de confirmación como un solo bloque
				// central, con el detalle del pedido como algo opcional. El
				// href real a la página de "invoice" de PMPro es el fallback si
				// JS no llega a cargar (mismo patrón que suscribirse.js: link
				// real interceptado, nunca un botón sin destino).
				if ( 'success' == $pmpro_invoice->status && ! pmpro_isLevelFree( $pmpro_invoice->membership_level ) ) {
					?>
					<a href="<?php echo esc_url( pmpro_url( 'invoice', '?invoice=' . $pmpro_invoice->code ) ); ?>" class="btn btn-primary" data-recibo-abrir><?php esc_html_e( 'Ver pedido', 'afectivalab' ); ?></a>
					<?php
				}

				// Show a message about account activation if the order is not yet successful.
				if ( 'success' != $pmpro_invoice->status ) {
					?>
					<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_message pmpro_alert' ) ); ?>">
						<?php
							/**
							 * Filter to change the message shown when the order is not successful.
							 *
							 * @since 3.1
							 *
							 * @param string $message The message to show.
							 * @param object $pmpro_invoice The PMProOrder object.
							 *
							 * @return string $message The message to show.
							 */
							echo wp_kses_post( apply_filters( 'pmpro_confirmation_payment_incomplete_message', __( 'We are waiting for your payment to be completed.', 'paid-memberships-pro' ), $pmpro_invoice ) );
						?>
					</div> <!-- pmpro_message -->
					<?php
				}

				if ( pmpro_isLevelFree( $pmpro_invoice->membership_level ) ) {
					// The invoice is free, so we don't need to show a full order.
					?>
					<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card' ) ); ?>">
						<h3 class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_title pmpro_font-large pmpro_heading-with-avatar' ) ); ?>">
							<?php echo get_avatar( $pmpro_invoice->user->ID, 48 ); ?>
							<?php
								/* translators: the current user's display name */
								printf( esc_html__( 'Welcome, %s', 'paid-memberships-pro' ), esc_html( $pmpro_invoice->user->display_name ) );
							?>
						</h3>
						<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_content' ) ); ?>">
							<ul class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_list pmpro_list-plain' ) ); ?>">
								<li class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_list_item' ) ); ?>"><strong><?php esc_html_e( 'Username', 'paid-memberships-pro' ); ?>:</strong> <?php echo esc_html( $pmpro_invoice->user->user_login ); ?></li>
								<li class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_list_item' ) ); ?>"><strong><?php esc_html_e( 'Email', 'paid-memberships-pro' );?>:</strong> <?php echo esc_html( $pmpro_invoice->user->user_email ); ?></li>
								<li class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_list_item' ) ); ?>">
									<strong><?php esc_html_e('Membership Level', 'paid-memberships-pro' );?>:</strong>
									<?php echo empty( $pmpro_invoice->membership_level ) ? esc_html__( 'Pending', 'paid-memberships-pro' ) : esc_html( $pmpro_invoice->membership_level->name ); ?>
								</li>
								<?php
									/**
									 * Filter to show/hide the expiration date on the confirmation page if membership is hourly.
									 *
									 * @param bool $show_expiration_date True to show the expiration date, false to hide it.
									 * @param object $user The user object.
									 * @return bool $show_expiration_date True to show the expiration date, false to hide it.
									 */
									if ( ! empty( $pmpro_invoice->membership_level->expiration_period ) && $pmpro_invoice->membership_level->expiration_period == 'Hour' && apply_filters( 'pmpro_confirmation_display_hour_expiration', true, $pmpro_invoice->user ) ) {
										?>
										<li class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_list_item' ) ); ?>"><strong><?php esc_html_e( 'Expires In', 'paid-memberships-pro' );?>:</strong> <?php echo esc_html( $pmpro_invoice->membership_level->expiration_number . ' ' . pmpro_translate_billing_period( $pmpro_invoice->membership_level->expiration_period, $pmpro_invoice->membership_level->expiration_number ) ); ?></li>
										<?php
									}
								?>
							</ul>
						</div> <!-- end pmpro_card_content -->
					</div> <!-- end pmpro_card -->
					<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_actions_nav' ) ); ?>">
						<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_actions_nav-right' ) ); ?>"><a href="<?php echo esc_url( pmpro_url( 'account' ) ); ?>"><?php esc_html_e( 'View Your Membership Account &rarr;', 'paid-memberships-pro' ); ?></a></span>
					</div> <!-- end pmpro_actions_nav -->
					<?php
				}
			}
		?>
	</div> <!-- end suscripcion-confirmacion__tarjeta -->
</div> <!-- end suscripcion-confirmacion -->
<?php
	if ( ! empty( $pmpro_invoice ) && ! pmpro_isLevelFree( $pmpro_invoice->membership_level ) ) {
		// If the order is not free, show the full order, but make sure we don't show $pmpro_msg again.
		$pmpro_msg = false;
		$pmpro_msgt = false;
		?>
		<div class="suscripcion-recibo-modal" data-recibo-modal hidden>
			<div class="suscripcion-recibo-modal__caja" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Detalle del pedido', 'afectivalab' ); ?>">
				<button type="button" class="suscripcion-recibo-modal__cerrar" data-recibo-cerrar aria-label="<?php esc_attr_e( 'Cerrar', 'afectivalab' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 5L19 19M19 5L5 19"/></svg>
				</button>
				<?php
				$afectivalab_recibo_html = pmpro_loadTemplate( 'invoice' );
				// invoice.php termina con su propio <div class="pmpro_actions_nav">
				// con links ("View Your Membership Account" / "View All Orders") a
				// las páginas nativas de cuenta/pedidos de PMPro — acá no se usan
				// esas, la cuenta real del sitio es /mi-cuenta, con su propia
				// sección "Tus pedidos" (ver page-templates/mi-cuenta.php y
				// afectivalab_suscripcion_pedidos() en inc/suscripciones.php). Se
				// quita ese bloque directamente del HTML (no alcanza con
				// ocultarlo por CSS: si esa hoja de estilos no llega a cargar por
				// el motivo que sea, no queda un link roto a mitad de camino) y
				// se reemplaza por los dos de .suscripcion-recibo-modal__cuenta
				// más abajo.
				$afectivalab_recibo_html = preg_replace( '/<div class="[^"]*pmpro_actions_nav[^"]*">.*?<\/div>/s', '', $afectivalab_recibo_html, 1 );
				echo $afectivalab_recibo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				<div class="suscripcion-recibo-modal__cuenta">
					<a href="<?php echo esc_url( home_url( '/mi-cuenta#pedidos' ) ); ?>" class="btn btn-secondary"><?php esc_html_e( 'Ver todos mis pedidos', 'afectivalab' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/mi-cuenta' ) ); ?>" class="btn btn-secondary"><?php esc_html_e( 'Ir a mi cuenta', 'afectivalab' ); ?></a>
				</div>
			</div>
		</div>
		<?php
	}
?>
