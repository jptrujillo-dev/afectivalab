( function () {
	'use strict';

	// Confirmación antes de quitar un perfil. Es solo una red de seguridad:
	// sin JS el botón sigue funcionando, y el perfil va a la papelera (no se
	// borra definitivamente), así que un clic por error se puede deshacer.
	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '[data-confirm]' );

		if ( ! button ) {
			return;
		}

		if ( ! window.confirm( button.getAttribute( 'data-confirm' ) ) ) {
			event.preventDefault();
		}
	} );
} )();
