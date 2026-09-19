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
