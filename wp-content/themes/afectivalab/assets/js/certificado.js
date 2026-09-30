( function () {
	'use strict';

	// El botón solo abre el diálogo de impresión del navegador — es la
	// persona quien elige "Guardar como PDF" ahí. Sin JS el botón no hace
	// nada, pero el certificado sigue siendo perfectamente imprimible con
	// Ctrl/Cmd+P, así que no hace falta un plan B.
	var boton = document.querySelector( '[data-certificado-imprimir]' );

	if ( ! boton ) {
		return;
	}

	boton.addEventListener( 'click', function () {
		window.print();
	} );
} )();
