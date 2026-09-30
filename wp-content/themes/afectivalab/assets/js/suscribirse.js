( function () {
	'use strict';

	// El botón "Suscribirme con PayPal" es un link normal a la página de
	// checkout de PMPro. Antes se dejaba navegar sin más (sin
	// preventDefault) y el overlay de "redirigiendo" solo se veía el
	// instante en que el servidor tardaba en responder — a veces ni eso, si
	// la respuesta llegaba rápido. Ahora se intercepta el clic, se muestra
	// el overlay, y recién después de una espera mínima se navega de
	// verdad — así la persona siempre alcanza a ver el aviso, en vez de que
	// dependa de cuánto tarde el servidor esa vez en particular.
	var ESPERA_MINIMA_MS = 600;

	var boton = document.querySelector( '[data-suscribirse-boton]' );

	if ( ! boton ) {
		return;
	}

	boton.addEventListener( 'click', function ( event ) {
		event.preventDefault();

		var config = window.afectivalabSuscribirse || {};
		var overlay = document.createElement( 'div' );
		overlay.className = 'suscripcion-overlay';
		overlay.innerHTML =
			'<div class="suscripcion-overlay__caja">' +
				'<span class="tarjeta-spinner" aria-hidden="true"></span>' +
				'<p>' + ( config.mensaje || 'Redirigiendo al pago…' ) + '</p>' +
			'</div>';

		document.body.appendChild( overlay );

		var destino = boton.href;

		window.setTimeout( function () {
			window.location.href = destino;
		}, ESPERA_MINIMA_MS );
	} );
} )();
