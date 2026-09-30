( function () {
	'use strict';

	// Filtro por edad dentro de un mundo (/mundos?eje=...). Los chips vienen
	// ocultos en el HTML: solo aparecen si este script carga, así sin JS se
	// ven todos los cursos agrupados por etapa, que es lo mismo que "Todas".
	var filtro = document.querySelector( '[data-mundo-filtro]' );

	if ( ! filtro ) {
		return;
	}

	var grupos = document.querySelectorAll( '.mundo-grupo[data-etapa]' );
	var chips = filtro.querySelectorAll( '[data-etapa]' );

	filtro.hidden = false;

	filtro.addEventListener( 'click', function ( evento ) {
		var chip = evento.target.closest( '[data-etapa]' );

		if ( ! chip || chip.classList.contains( 'is-activo' ) ) {
			return;
		}

		var etapa = chip.getAttribute( 'data-etapa' );

		Array.prototype.forEach.call( chips, function ( c ) {
			var activo = c === chip;
			c.classList.toggle( 'is-activo', activo );
			c.setAttribute( 'aria-pressed', activo ? 'true' : 'false' );
		} );

		Array.prototype.forEach.call( grupos, function ( grupo ) {
			var visible = ! etapa || grupo.getAttribute( 'data-etapa' ) === etapa;

			grupo.hidden = ! visible;

			// Reinicia la entrada suave de los que quedan a la vista.
			if ( visible ) {
				grupo.classList.remove( 'is-entrando' );
				void grupo.offsetWidth;
				grupo.classList.add( 'is-entrando' );
			}
		} );
	} );
} )();
