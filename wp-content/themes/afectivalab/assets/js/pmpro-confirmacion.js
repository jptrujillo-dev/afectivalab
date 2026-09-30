( function () {
	'use strict';

	// El botón "Ver pedido" es un link real a la página de invoice de PMPro
	// (fallback si este script no llega a cargar). Con JS disponible, en vez
	// de navegar se abre el mismo recibo ya renderizado en esta página
	// dentro de un modal — mismo patrón de abrir/cerrar (clic afuera y
	// Escape) que el menú de usuario del header en main.js.
	var modal = document.querySelector( '[data-recibo-modal]' );
	var abrir = document.querySelector( '[data-recibo-abrir]' );
	var cerrar = document.querySelector( '[data-recibo-cerrar]' );

	if ( ! modal || ! abrir || ! cerrar ) {
		return;
	}

	var cerrarModal = function () {
		modal.hidden = true;
	};

	abrir.addEventListener( 'click', function ( event ) {
		event.preventDefault();
		modal.hidden = false;
	} );

	cerrar.addEventListener( 'click', cerrarModal );

	modal.addEventListener( 'click', function ( event ) {
		if ( event.target === modal ) {
			cerrarModal();
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && ! modal.hidden ) {
			cerrarModal();
		}
	} );
} )();
