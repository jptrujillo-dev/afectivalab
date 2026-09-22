( function () {
	'use strict';

	/**
	 * Todo lo de acá es ayuda visual. La validación que realmente decide si
	 * una cuenta se crea o una contraseña cambia vive en PHP (inc/auth.php)
	 * y corre igual sin JS — esto solo evita que la persona tenga que
	 * enviar el formulario para enterarse de un error.
	 */

	// Mostrar/ocultar contraseña
	document.querySelectorAll( '[data-password-toggle]' ).forEach( function ( button ) {
		var input = document.getElementById( button.getAttribute( 'data-password-toggle' ) );

		if ( ! input ) {
			return;
		}

		button.addEventListener( 'click', function () {
			var showing = input.type === 'text';
			input.type = showing ? 'password' : 'text';
			button.classList.toggle( 'is-visible', ! showing );
			button.setAttribute( 'aria-label', showing ? button.dataset.labelShow : button.dataset.labelHide );
		} );
	} );

	// Medidor de fortaleza de contraseña
	function passwordScore( value ) {
		var score = 0;

		if ( value.length >= 8 ) {
			score++;
		}
		if ( value.length >= 12 ) {
			score++;
		}
		if ( /[a-z]/.test( value ) && /[A-Z]/.test( value ) ) {
			score++;
		}
		if ( /[0-9]/.test( value ) ) {
			score++;
		}
		if ( /[^A-Za-z0-9]/.test( value ) ) {
			score++;
		}

		return score;
	}

	document.querySelectorAll( '[data-password-strength]' ).forEach( function ( meter ) {
		var input = document.getElementById( meter.getAttribute( 'data-password-strength' ) );
		var fill = meter.querySelector( '.password-strength__fill' );
		var label = meter.querySelector( '.password-strength__label' );

		if ( ! input || ! fill || ! label ) {
			return;
		}

		var levels = [
			{ max: 2, className: 'is-weak', text: 'Baja' },
			{ max: 4, className: 'is-medium', text: 'Media' },
			{ max: 5, className: 'is-strong', text: 'Alta' }
		];

		input.addEventListener( 'input', function () {
			meter.classList.toggle( 'is-visible', input.value.length > 0 );

			if ( ! input.value.length ) {
				return;
			}

			var score = passwordScore( input.value );
			var level = levels[ 0 ];

			for ( var i = 0; i < levels.length; i++ ) {
				if ( score <= levels[ i ].max ) {
					level = levels[ i ];
					break;
				}
			}

			fill.className = 'password-strength__fill ' + level.className;
			label.textContent = level.text;
		} );
	} );

	// Validación en vivo de campos (nombre, email, contraseña, confirmación)
	var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	function setFieldState( input, valid, message ) {
		var field = input.closest( '.form-field' );

		if ( ! field ) {
			return;
		}

		var hint = field.querySelector( '.form-field__hint' );

		field.classList.toggle( 'is-invalid', ! valid );
		field.classList.toggle( 'is-valid', valid && input.value.length > 0 );

		if ( hint ) {
			hint.textContent = valid ? '' : message;
		}
	}

	function validateField( input ) {
		var kind = input.getAttribute( 'data-validate-field' );

		if ( ! input.value.length ) {
			// Todavía no escribió nada: no lo marcamos como error mientras
			// no interactúe (eso lo maneja el "required" nativo al enviar).
			setFieldState( input, true, '' );
			return true;
		}

		if ( 'nombre' === kind ) {
			var valid = input.value.trim().length >= 2;
			setFieldState( input, valid, 'Cuéntanos tu nombre completo.' );
			return valid;
		}

		if ( 'email' === kind ) {
			var validEmail = emailPattern.test( input.value.trim() );
			setFieldState( input, validEmail, 'Ese correo no parece válido.' );
			return validEmail;
		}

		if ( 'password' === kind ) {
			var validPassword = input.value.length >= 8;
			setFieldState( input, validPassword, 'Mínimo 8 caracteres.' );
			return validPassword;
		}

		if ( 'password-match' === kind ) {
			var otherInput = document.getElementById( input.getAttribute( 'data-match' ) );
			var validMatch = ! otherInput || input.value === otherInput.value;
			setFieldState( input, validMatch, 'Las contraseñas no coinciden.' );
			return validMatch;
		}

		return true;
	}

	// Inicio de sesión sin recargar la página.
	//
	// Si el servidor tarda o la contraseña está mal, recargar significaba
	// perder lo que la persona ya había escrito. Enviando por AJAX el
	// formulario se queda como está y solo aparece el mensaje.
	//
	// Solo se activa si el tema pudo pasar la URL de AJAX; si no, el
	// formulario se envía como siempre y PHP lo resuelve igual.
	var loginForm = document.querySelector( 'form[data-ajax-login]' );
	var ajaxUrl = window.afectivalabAuth && window.afectivalabAuth.ajaxUrl;

	if ( loginForm && ajaxUrl && window.fetch ) {
		var alertBox = document.querySelector( '[data-login-alert]' );
		var submit = loginForm.querySelector( 'button[type="submit"]' );
		var submitHtml = submit ? submit.innerHTML : '';

		var mostrarError = function ( mensaje ) {
			if ( ! alertBox ) {
				return;
			}

			alertBox.textContent = mensaje;
			alertBox.hidden = false;
		};

		var ocupado = function ( si ) {
			if ( ! submit ) {
				return;
			}

			submit.disabled = si;
			submit.innerHTML = si ? ( window.afectivalabAuth.entrando || 'Entrando…' ) : submitHtml;
		};

		loginForm.addEventListener( 'submit', function ( event ) {
			event.preventDefault();

			if ( alertBox ) {
				alertBox.hidden = true;
			}

			ocupado( true );

			var datos = new FormData( loginForm );
			datos.append( 'action', 'afectivalab_login' );

			window.fetch( ajaxUrl, {
				method: 'POST',
				body: datos,
				credentials: 'same-origin'
			} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( payload ) {
					if ( payload && payload.success && payload.data && payload.data.redirect ) {
						window.location.assign( payload.data.redirect );
						return;
					}

					ocupado( false );
					mostrarError( ( payload && payload.data && payload.data.message ) || 'No pudimos iniciar tu sesión, intenta de nuevo.' );
				} )
				.catch( function () {
					// Si la petición falló (sin red, o el servidor cortó),
					// se envía el formulario a la antigua en vez de dejar a
					// la persona mirando un botón que no hace nada.
					//
					// form.submit() no incluye el valor del botón, y PHP se
					// apoya justamente en ese campo para saber que el
					// formulario se envió: hay que agregarlo a mano.
					var marca = document.createElement( 'input' );
					marca.type = 'hidden';
					marca.name = 'afectivalab_login_submit';
					marca.value = '1';
					loginForm.appendChild( marca );
					loginForm.submit();
				} );
		} );
	}

	document.querySelectorAll( 'form[data-validate]' ).forEach( function ( form ) {
		var fields = form.querySelectorAll( '[data-validate-field]' );

		fields.forEach( function ( input ) {
			input.addEventListener( 'input', function () {
				validateField( input );

				// La confirmación depende del campo de contraseña: si esa
				// cambia después de ya haber escrito la confirmación, hay
				// que revalidarla también.
				var dependent = form.querySelector( '[data-match="' + input.id + '"]' );
				if ( dependent && dependent.value.length ) {
					validateField( dependent );
				}
			} );

			input.addEventListener( 'blur', function () {
				validateField( input );
			} );
		} );
	} );
} )();
