( function () {
	'use strict';

	// Colapsa el "segundo clic" del checkout de Paid Memberships Pro: la
	// persona ya tocó "Suscribirme" en /suscribirse, así que acá se dispara
	// un clic real (no form.submit()/requestSubmit() — ya nos mordió una vez
	// no mandar el name/value del botón real) sobre el botón verdadero de
	// PMPro ("Check Out with PayPal"), mostrando un estado de carga en vez
	// de obligar a un segundo toque humano.
	//
	// Progressive enhancement: si este script no llega a cargar, la persona
	// simplemente ve el formulario normal de PMPro y toca el botón a mano —
	// nada de esto es necesario para que el pago funcione.
	//
	// ⚠️ Tiene que entrar a la MISMA cola que usa el script de PMPro
	// (jQuery(document).ready()), no a un DOMContentLoaded nativo aparte:
	// el script de PMPro agrega ahí un campo oculto (`javascriptok`) que el
	// servidor exige para no rechazar el envío como sospechoso, y jQuery
	// ejecuta esa cola en el orden en que se registró cada callback — un
	// primer intento con `document.addEventListener('DOMContentLoaded', ...)`
	// en paralelo no garantizaba ir después del de jQuery (dos colas
	// separadas, sin orden asegurado entre ellas) y en producción seguía
	// disparando el clic antes de que el campo existiera. Encolándolo con
	// jQuery mismo, y con inc/enqueue.php ya declarando 'pmpro_checkout'
	// como dependencia (así el script de PMPro se registra primero), el
	// orden queda garantizado de verdad.
	function iniciar() {
		var form = document.getElementById( 'pmpro_form' );
		var botonPaypal = document.getElementById( 'pmpro_btn-submit-paypal' );

		if ( ! form || ! botonPaypal ) {
			return;
		}

		// Si la página volvió a mostrarse con un error (ej. el pago anterior
		// falló), no hay que auto-enviar de nuevo — se repetiría el mismo
		// error en bucle. Se deja el formulario visible para que la persona
		// vea qué pasó.
		var mensaje = document.getElementById( 'pmpro_message' );
		if ( mensaje && mensaje.classList.contains( 'pmpro_error' ) ) {
			return;
		}

		// Si todavía hace falta completar una contraseña (alguien llegó acá
		// sin sesión, un caso raro ya que /suscribirse exige login antes),
		// no se puede saltar ese paso.
		if ( form.querySelector( '#pmpro_user_fields input[type="password"]' ) ) {
			return;
		}

		var config = window.afectivalabPmproCheckout || {};
		var overlay = document.createElement( 'div' );
		overlay.className = 'suscripcion-overlay';
		overlay.innerHTML =
			'<div class="suscripcion-overlay__caja">' +
				'<span class="tarjeta-spinner" aria-hidden="true"></span>' +
				'<p>' + ( config.mensaje || 'Confirmando tu plan…' ) + '</p>' +
			'</div>';

		form.hidden = true;
		document.body.appendChild( overlay );

		botonPaypal.click();
	}

	// jQuery está disponible sí o sí acá: 'pmpro_checkout' (la dependencia
	// declarada en inc/enqueue.php) a su vez depende de 'jquery'. Si por
	// algún motivo no llegara a estar (CDN caído, etc.), no hay nada seguro
	// que hacer — se deja el formulario normal de PMPro tal cual, sin
	// auto-enviar nada.
	if ( 'undefined' !== typeof jQuery ) {
		jQuery( iniciar );
	}
} )();
