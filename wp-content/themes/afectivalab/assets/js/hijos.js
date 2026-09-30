( function () {
	'use strict';

	// /mis-hijos: al guardar el perfil, el botón muestra un spinner y se
	// deshabilita para que no se envíe dos veces. Va en un setTimeout: si el
	// botón se deshabilita dentro del mismo 'submit', el navegador deja
	// fuera su name/value y el servidor no sabe que fue "guardar".
	var form = document.querySelector( '[data-hijo-form]' );

	if ( form ) {
		form.addEventListener( 'submit', function () {
			var boton = form.querySelector( 'button[type="submit"]' );

			if ( ! boton || boton.disabled ) {
				return;
			}

			window.setTimeout( function () {
				boton.disabled = true;

				var spinner = document.createElement( 'span' );
				spinner.className = 'btn__spinner';
				boton.insertBefore( spinner, boton.firstChild );
			}, 0 );
		} );
	}

	// Cuántos temas van elegidos, al lado del título de la sección.
	var cuenta = document.querySelector( '[data-temas-cuenta]' );

	if ( ! cuenta ) {
		return;
	}

	var selector = '[name="afectivalab_hijo_preocupaciones[]"]';

	function actualizar() {
		var n = document.querySelectorAll( selector + ':checked' ).length;

		cuenta.textContent = 0 === n ? 'Ninguno elegido' : ( 1 === n ? '1 elegido' : n + ' elegidos' );
		cuenta.classList.toggle( 'is-activa', n > 0 );
	}

	Array.prototype.forEach.call( document.querySelectorAll( selector ), function ( casilla ) {
		casilla.addEventListener( 'change', actualizar );
	} );

	actualizar();
} )();
