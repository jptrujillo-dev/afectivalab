( function () {
	'use strict';

	// Vista previa de la foto de perfil elegida y activación del botón
	// "Guardar foto" (se deja deshabilitado hasta que en verdad haya un
	// archivo elegido, para no invitar a enviar el formulario vacío).
	var input = document.querySelector( '[data-avatar-input]' );
	var preview = document.querySelector( '[data-avatar-preview]' );
	var submit = document.querySelector( '[data-avatar-submit]' );

	if ( ! input || ! preview || ! submit ) {
		return;
	}

	// El botón solo se deshabilita acá, en JS: si el script no llega a
	// cargar, el botón se queda como vino del servidor (habilitado) y el
	// formulario se puede enviar igual — la validación real sigue siendo la
	// del servidor en inc/account.php.
	submit.disabled = true;

	input.addEventListener( 'change', function () {
		var file = input.files && input.files[ 0 ];

		if ( ! file ) {
			return;
		}

		submit.disabled = false;

		if ( 'FileReader' in window ) {
			var reader = new FileReader();

			reader.onload = function ( event ) {
				var existingAvatar = preview.querySelector( '.user-avatar' );
				var size = existingAvatar ? existingAvatar.style.width : '96px';

				preview.innerHTML =
					'<span class="user-avatar" style="width:' + size + ';height:' + size + '">' +
					'<img src="' + event.target.result + '" alt="" width="96" height="96">' +
					'</span>';
			};

			reader.readAsDataURL( file );
		}
	} );
} )();
