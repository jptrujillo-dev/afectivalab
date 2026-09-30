( function () {
	'use strict';

	// Vista previa de la foto de perfil elegida y activación del botón
	// "Guardar foto" (se deja deshabilitado hasta que en verdad haya un
	// archivo elegido, para no invitar a enviar el formulario vacío).
	//
	// El tipo/tamaño se valida acá también, no solo en el servidor
	// (inc/account.php, misma regla: JPG/PNG/WEBP, 1MB): antes, si el
	// archivo no servía, la persona no se enteraba hasta después de tocar
	// "Guardar" y esperar la recarga completa de la página — esto avisa
	// apenas elige el archivo, sin sacarle a PHP la validación real (si
	// este script no llega a cargar, el servidor la sigue aplicando igual).
	function iniciarAvatar() {
		var input = document.querySelector( '[data-avatar-input]' );
		var preview = document.querySelector( '[data-avatar-preview]' );
		var submit = document.querySelector( '[data-avatar-submit]' );
		var errorBox = document.querySelector( '[data-avatar-error-cliente]' );

		if ( ! input || ! preview || ! submit ) {
			return;
		}

		var TIPOS_VALIDOS = [ 'image/jpeg', 'image/png', 'image/webp' ];
		var TAMANO_MAXIMO = 1024 * 1024; // 1MB, igual que MB_IN_BYTES en inc/account.php.

		// El botón solo se deshabilita acá, en JS: si el script no llega a
		// cargar, el botón se queda como vino del servidor (habilitado) y el
		// formulario se puede enviar igual — la validación real sigue siendo
		// la del servidor en inc/account.php.
		submit.disabled = true;

		var mostrarError = function ( mensaje ) {
			if ( ! errorBox ) {
				return;
			}
			errorBox.textContent = mensaje;
			errorBox.hidden = false;
		};

		input.addEventListener( 'change', function () {
			if ( errorBox ) {
				errorBox.hidden = true;
			}

			var file = input.files && input.files[ 0 ];

			if ( ! file ) {
				return;
			}

			if ( TIPOS_VALIDOS.indexOf( file.type ) === -1 ) {
				mostrarError( 'La foto debe ser JPG, PNG o WEBP.' );
				input.value = '';
				return;
			}

			if ( file.size > TAMANO_MAXIMO ) {
				mostrarError( 'La foto no debe pesar más de 1MB.' );
				input.value = '';
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

		// Guardar por AJAX (afectivalab_ajax_avatar() en inc/account.php):
		// la página no se recarga y la foto nueva se ve al instante también
		// en el header. El spinner lo pone iniciarLoaderFormularios() (mismo
		// listener genérico de todos los formularios); aquí solo se quita al
		// terminar, como en el modal de contraseña. Sin fetch/FormData, el
		// formulario se envía normal.
		var form = document.querySelector( '[data-avatar-form]' );
		var exito = document.querySelector( '[data-avatar-exito]' );
		var cfg = window.afectivalabCuenta;

		if ( ! form || ! cfg || ! window.fetch || ! window.FormData ) {
			return;
		}

		var terminar = function ( habilitar ) {
			var spinner = submit.querySelector( '.btn__spinner' );

			if ( spinner ) {
				spinner.remove();
			}

			submit.disabled = ! habilitar;
		};

		var reemplazarAvatar = function ( selector, html ) {
			Array.prototype.forEach.call( document.querySelectorAll( selector ), function ( viejo ) {
				var tmp = document.createElement( 'div' );
				tmp.innerHTML = html;

				if ( tmp.firstElementChild ) {
					viejo.replaceWith( tmp.firstElementChild );
				}
			} );
		};

		form.addEventListener( 'submit', function ( event ) {
			event.preventDefault();

			if ( errorBox ) {
				errorBox.hidden = true;
			}

			if ( exito ) {
				exito.hidden = true;
			}

			var datos = new FormData( form );
			datos.set( 'action', 'afectivalab_avatar' );

			fetch( cfg.ajaxUrl, { method: 'POST', body: datos, credentials: 'same-origin' } )
				.then( function ( respuesta ) {
					return respuesta.json().catch( function () {
						return null;
					} );
				} )
				.then( function ( res ) {
					if ( ! res || ! res.success ) {
						terminar( true );
						mostrarError( ( res && res.data && res.data.mensaje ) || 'No pudimos subir la foto, intenta de nuevo.' );
						return;
					}

					terminar( false );
					input.value = '';
					preview.innerHTML = res.data.avatar;
					reemplazarAvatar( '.user-menu__trigger .user-avatar', res.data.header );
					reemplazarAvatar( '.user-menu__cabecera .user-avatar', res.data.menu );
					reemplazarAvatar( '.mobile-nav__user .user-avatar', res.data.movil );

					if ( exito ) {
						exito.textContent = res.data.mensaje;
						exito.hidden = false;
					}
				} )
				.catch( function () {
					terminar( true );
					mostrarError( 'No pudimos conectar. Revisa tu conexión e intenta de nuevo.' );
				} );
		} );
	}

	// Slider de "Insignias desbloqueadas": las flechas solo dan un atajo
	// sobre el mismo scroll horizontal nativo (con scroll-snap en CSS) que
	// ya funciona con swipe/rueda del mouse aunque este script no cargue —
	// avanzan de a una tarjeta por clic, calculando su ancho real en vez de
	// asumir un valor fijo (el ancho depende de --space-3, que puede
	// cambiar).
	function iniciarSliderInsignias() {
		var lista = document.querySelector( '[data-insignias-lista]' );
		var prev = document.querySelector( '[data-insignias-prev]' );
		var next = document.querySelector( '[data-insignias-next]' );

		if ( ! lista || ! prev || ! next ) {
			return;
		}

		function pasoScroll() {
			var item = lista.querySelector( '.account-insignias__item' );
			if ( ! item ) {
				return lista.clientWidth;
			}
			var estilo = window.getComputedStyle( lista );
			var gap = parseFloat( estilo.columnGap || estilo.gap || '0' ) || 0;
			return item.getBoundingClientRect().width + gap;
		}

		function actualizarFlechas() {
			var maxScroll = lista.scrollWidth - lista.clientWidth;
			prev.disabled = lista.scrollLeft <= 1;
			next.disabled = lista.scrollLeft >= maxScroll - 1;
		}

		prev.addEventListener( 'click', function () {
			lista.scrollBy( { left: -pasoScroll(), behavior: 'smooth' } );
		} );

		next.addEventListener( 'click', function () {
			lista.scrollBy( { left: pasoScroll(), behavior: 'smooth' } );
		} );

		lista.addEventListener( 'scroll', actualizarFlechas );
		window.addEventListener( 'resize', actualizarFlechas );
		actualizarFlechas();
	}

	// Mostrar/ocultar contraseña y medidor de fortaleza en los campos del
	// modal de "Cambiar contraseña" — mismo comportamiento (y el mismo
	// cálculo de puntaje) que ya usa /registro en auth.js; ver el
	// comentario sobre .form-input__toggle/.password-strength en
	// cuenta.css para el porqué de duplicarlo acá en vez de cargar
	// auth.js entero. Es solo ayuda visual: la validación real de la
	// contraseña sigue siendo la del servidor en inc/account.php.
	function iniciarPasswordExtras() {
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
	}

	// Feedback de "guardando" en cualquiera de los formularios de la página
	// (foto, datos, suscripción, preferencias): al enviarse de verdad,
	// deshabilita el botón y le agrega un spinner. Un solo listener por
	// formulario en vez de repetir la misma lógica en cada uno.
	//
	// El botón "Cancelar renovación automática" tiene data-confirm (ver
	// main.js): el primer clic se previene y abre el modal de confirmación,
	// así que el "submit" solo llega a dispararse si la persona acepta — este
	// loader no se activa si cancela, sin ningún manejo especial acá.
	//
	// ⚠️ El disabled se difiere con setTimeout(fn, 0) a propósito: si se
	// deshabilita el botón de submit de forma síncrona todavía *dentro*
	// del propio handler de "submit", el navegador puede terminar de
	// serializar el formulario después de verlo ya deshabilitado y dejar
	// afuera su name/value (afectivalab_avatar_submit, por ejemplo) — el
	// servidor recibía el POST sin ese campo y afectivalab_handle_avatar_upload()
	// cortaba de entrada, en silencio, sin ningún error que mostrar (así
	// se vio en producción: la foto nunca cambiaba y no aparecía ningún
	// aviso). Encolando la deshabilitación para el siguiente tick, el
	// navegador ya alcanzó a capturar los datos del envío real primero.
	function iniciarLoaderFormularios() {
		var formularios = document.querySelectorAll( '.account-page form' );

		formularios.forEach( function ( formulario ) {
			formulario.addEventListener( 'submit', function () {
				var boton = formulario.querySelector( 'button[type="submit"]' );

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
		} );
	}

	// Modal "Cambiar contraseña": valida en vivo, envía por AJAX (fetch a
	// admin-ajax.php, afectivalab_ajax_cambiar_password() en
	// inc/account.php — mismo patrón que el login en auth.js) y muestra
	// "contraseña actualizada" adentro del modal antes de cerrarlo solo,
	// en vez de recargar toda la página para un cambio tan chico.
	//
	// El spinner del botón lo pone iniciarLoaderFormularios() (mismo
	// listener genérico, registrado antes que este) — acá solo hace falta
	// quitarlo al terminar, porque a diferencia de los demás formularios
	// de la página este no recarga sola para "resetearlo".
	function iniciarModalPassword() {
		var abrir = document.querySelector( '[data-password-abrir]' );
		var modal = document.querySelector( '[data-password-modal]' );
		var cerrarBtn = document.querySelector( '[data-password-cerrar]' );
		var form = document.querySelector( '[data-password-form]' );

		if ( ! abrir || ! modal || ! cerrarBtn || ! form ) {
			return;
		}

		var campoActual = form.querySelector( '#afectivalab-password-actual' );
		var campoNueva = form.querySelector( '#afectivalab-password-nueva' );
		var campoRepetir = form.querySelector( '#afectivalab-password-nueva-2' );
		var campoNuevaWrap = form.querySelector( '[data-password-campo-nueva]' );
		var campoRepetirWrap = form.querySelector( '[data-password-campo-repetir]' );
		var hintNueva = form.querySelector( '[data-password-hint-nueva]' );
		var hintRepetir = form.querySelector( '[data-password-hint-repetir]' );
		var cajaError = document.querySelector( '[data-password-error]' );
		var cajaExito = document.querySelector( '[data-password-exito]' );
		var config = window.afectivalabCuenta || {};

		var quitarSpinner = function () {
			var boton = form.querySelector( 'button[type="submit"]' );
			if ( ! boton ) {
				return;
			}
			boton.disabled = false;
			var spinner = boton.querySelector( '.btn__spinner' );
			if ( spinner ) {
				spinner.remove();
			}
		};

		var limpiarMensajes = function () {
			cajaError.hidden = true;
			cajaExito.hidden = true;
		};

		var resetearForm = function () {
			form.reset();
			campoNuevaWrap.classList.remove( 'is-invalid', 'is-valid' );
			campoRepetirWrap.classList.remove( 'is-invalid', 'is-valid' );
			hintNueva.textContent = '';
			hintRepetir.textContent = '';
			limpiarMensajes();
		};

		var abrirModal = function () {
			resetearForm();
			modal.hidden = false;
			campoActual.focus();
		};

		var cerrarModal = function () {
			modal.hidden = true;
		};

		abrir.addEventListener( 'click', abrirModal );
		cerrarBtn.addEventListener( 'click', cerrarModal );

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

		// Validación en vivo: largo mínimo y que las dos contraseñas nuevas
		// coincidan, con el mismo patrón visual (form-field.is-invalid /
		// is-valid) que ya usa /registro.
		var validarNueva = function () {
			var valor = campoNueva.value;

			if ( ! valor ) {
				campoNuevaWrap.classList.remove( 'is-invalid', 'is-valid' );
				hintNueva.textContent = '';
				return true;
			}

			if ( valor.length < 8 ) {
				campoNuevaWrap.classList.add( 'is-invalid' );
				campoNuevaWrap.classList.remove( 'is-valid' );
				hintNueva.textContent = 'Debe tener al menos 8 caracteres.';
				return false;
			}

			campoNuevaWrap.classList.add( 'is-valid' );
			campoNuevaWrap.classList.remove( 'is-invalid' );
			hintNueva.textContent = '';
			return true;
		};

		var validarRepetir = function () {
			var valor = campoRepetir.value;

			if ( ! valor ) {
				campoRepetirWrap.classList.remove( 'is-invalid', 'is-valid' );
				hintRepetir.textContent = '';
				return true;
			}

			if ( valor !== campoNueva.value ) {
				campoRepetirWrap.classList.add( 'is-invalid' );
				campoRepetirWrap.classList.remove( 'is-valid' );
				hintRepetir.textContent = 'Las contraseñas no coinciden.';
				return false;
			}

			campoRepetirWrap.classList.add( 'is-valid' );
			campoRepetirWrap.classList.remove( 'is-invalid' );
			hintRepetir.textContent = '';
			return true;
		};

		campoNueva.addEventListener( 'input', function () {
			validarNueva();
			if ( campoRepetir.value ) {
				validarRepetir();
			}
		} );
		campoRepetir.addEventListener( 'input', validarRepetir );

		form.addEventListener( 'submit', function ( event ) {
			event.preventDefault();
			limpiarMensajes();

			var nuevaValida = campoNueva.value.length >= 8;
			var repetirValida = campoRepetir.value === campoNueva.value;

			if ( ! nuevaValida ) {
				validarNueva();
			}
			if ( ! repetirValida ) {
				validarRepetir();
			}

			if ( ! nuevaValida || ! repetirValida || ! config.ajaxUrl || ! window.fetch ) {
				quitarSpinner();
				if ( ! config.ajaxUrl || ! window.fetch ) {
					cajaError.textContent = 'No pudimos conectar, intenta de nuevo.';
					cajaError.hidden = false;
				}
				return;
			}

			var datos = new FormData( form );
			datos.append( 'action', 'afectivalab_cambiar_password' );

			window.fetch( config.ajaxUrl, {
				method: 'POST',
				body: datos,
				credentials: 'same-origin'
			} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( payload ) {
					quitarSpinner();

					if ( payload && payload.success ) {
						cajaExito.textContent = ( payload.data && payload.data.message ) || 'Tu contraseña se actualizó.';
						cajaExito.hidden = false;
						form.hidden = true;

						window.setTimeout( function () {
							form.hidden = false;
							cerrarModal();
						}, 1600 );
						return;
					}

					cajaError.textContent = ( payload && payload.data && payload.data.message ) || 'No pudimos actualizar tu contraseña, intenta de nuevo.';
					cajaError.hidden = false;
				} )
				.catch( function () {
					quitarSpinner();
					cajaError.textContent = 'No pudimos conectar, intenta de nuevo.';
					cajaError.hidden = false;
				} );
		} );
	}

	iniciarAvatar();
	iniciarSliderInsignias();
	iniciarPasswordExtras();
	iniciarLoaderFormularios();
	iniciarModalPassword();
} )();
