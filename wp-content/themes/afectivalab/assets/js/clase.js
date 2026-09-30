( function () {
	'use strict';

	// Microclase sin recargas: caso práctico, misión (con su foto) y "marcar
	// como vista" se envían por AJAX a inc/clase-ajax.php, que responde con
	// el HTML ya actualizado de cada bloque (template-parts/clase/). Aquí
	// solo se reemplaza ese bloque en su lugar, así la persona no pierde
	// dónde estaba.
	//
	// Si esto no carga (o el navegador no tiene fetch), los formularios se
	// envían normal y los procesa la plantilla: nada se rompe.
	var cfg = window.afectivalabClase;

	if ( ! cfg || ! window.fetch || ! window.FormData ) {
		return;
	}

	var TIPOS_FOTO = [ 'image/jpeg', 'image/png', 'image/webp' ];

	function alerta( contenedor, mensaje ) {
		var el = contenedor && contenedor.querySelector( '[data-clase-alerta]' );

		if ( ! el ) {
			return;
		}

		if ( ! mensaje ) {
			el.hidden = true;
			el.textContent = '';
			return;
		}

		el.textContent = mensaje;
		el.hidden = false;

		// Reinicia la animación de "sacudida" aunque el error se repita.
		el.classList.remove( 'is-sacude' );
		void el.offsetWidth;
		el.classList.add( 'is-sacude' );
	}

	function megas( bytes ) {
		return ( bytes / 1048576 ).toFixed( 1 ).replace( '.0', '' );
	}

	function validarFoto( archivo ) {
		if ( ! archivo ) {
			return cfg.errorFalta;
		}

		var porTipo = TIPOS_FOTO.indexOf( archivo.type ) !== -1;
		var porNombre = /\.(jpe?g|png|webp)$/i.test( archivo.name || '' );

		if ( ! porTipo && ! ( '' === archivo.type && porNombre ) ) {
			return cfg.errorTipo;
		}

		if ( archivo.size > cfg.maxBytes ) {
			return cfg.errorPeso.replace( '%s', megas( archivo.size ) );
		}

		return '';
	}

	// --- Foto de la misión: validación y vista previa al elegirla ---
	function reiniciarZona( zona ) {
		var preview = zona.querySelector( '[data-mision-preview]' );
		var nombre = zona.querySelector( '[data-mision-archivo]' );
		var ayuda = zona.querySelector( '[data-mision-ayuda]' );

		if ( preview && preview.dataset.url ) {
			URL.revokeObjectURL( preview.dataset.url );
			delete preview.dataset.url;
		}

		if ( preview ) {
			preview.hidden = true;
			preview.removeAttribute( 'src' );
		}

		if ( nombre && nombre.dataset.original ) {
			nombre.textContent = nombre.dataset.original;
		}

		if ( ayuda && ayuda.dataset.original ) {
			ayuda.textContent = ayuda.dataset.original;
		}

		zona.classList.remove( 'is-elegida' );
	}

	document.addEventListener( 'change', function ( evento ) {
		var input = evento.target;

		if ( ! input.matches || ! input.matches( '[data-mision-subir] input[type="file"]' ) ) {
			return;
		}

		var zona = input.closest( '[data-mision-subir]' );
		var form = input.closest( 'form' );
		var archivo = input.files && input.files[0];
		var nombre = zona.querySelector( '[data-mision-archivo]' );
		var ayuda = zona.querySelector( '[data-mision-ayuda]' );
		var preview = zona.querySelector( '[data-mision-preview]' );

		if ( nombre && ! nombre.dataset.original ) {
			nombre.dataset.original = nombre.textContent;
		}

		if ( ayuda && ! ayuda.dataset.original ) {
			ayuda.dataset.original = ayuda.textContent;
		}

		reiniciarZona( zona );

		if ( ! archivo ) {
			return;
		}

		var error = validarFoto( archivo );

		if ( error ) {
			input.value = '';
			zona.classList.add( 'is-error' );
			alerta( form, error );
			return;
		}

		zona.classList.remove( 'is-error' );
		alerta( form, '' );

		if ( preview ) {
			preview.dataset.url = URL.createObjectURL( archivo );
			preview.src = preview.dataset.url;
			preview.hidden = false;
		}

		if ( nombre ) {
			nombre.textContent = archivo.name;
		}

		if ( ayuda ) {
			ayuda.textContent = megas( archivo.size ) + ' MB · ' + cfg.cambiarFoto;
		}

		zona.classList.add( 'is-elegida' );
	} );

	// --- Envío ---
	function cargando( form, boton, activo ) {
		form.classList.toggle( 'is-enviando', activo );

		Array.prototype.forEach.call( form.querySelectorAll( 'button' ), function ( b ) {
			b.disabled = activo;
		} );

		var spinner = form.querySelector( '.btn__spinner' );

		if ( spinner ) {
			spinner.remove();
		}

		if ( activo && boton ) {
			spinner = document.createElement( 'span' );
			spinner.className = 'btn__spinner';
			boton.insertBefore( spinner, boton.firstChild );
			boton.classList.add( 'is-elegida' );
		} else if ( boton ) {
			boton.classList.remove( 'is-elegida' );
		}
	}

	function crearNodo( html ) {
		var tmp = document.createElement( 'div' );
		tmp.innerHTML = html.trim();
		return tmp.firstElementChild;
	}

	// Reemplaza un bloque por su versión nueva, con una entrada suave.
	function reemplazar( viejo, html ) {
		var nuevo = html ? crearNodo( html ) : null;

		if ( ! viejo || ! nuevo ) {
			return null;
		}

		nuevo.classList.add( 'is-entrando' );
		viejo.replaceWith( nuevo );

		return nuevo;
	}

	// La barra de progreso crece desde donde estaba, no aparece de golpe.
	function reemplazarProgreso( html ) {
		var viejo = document.querySelector( '[data-clase-progreso]' );
		var anterior = viejo && viejo.querySelector( '.progreso-bar__fill' );
		var anchoAnterior = anterior ? anterior.style.width : '';
		var nuevo = html ? crearNodo( html ) : null;

		if ( ! viejo || ! nuevo ) {
			return;
		}

		var barra = nuevo.querySelector( '.progreso-bar__fill' );
		var anchoNuevo = barra ? barra.style.width : '';

		if ( barra && anchoAnterior ) {
			barra.style.width = anchoAnterior;
		}

		viejo.replaceWith( nuevo );

		if ( barra ) {
			window.requestAnimationFrame( function () {
				window.requestAnimationFrame( function () {
					barra.style.width = anchoNuevo;
				} );
			} );
		}
	}

	function aplicar( tipo, datos ) {
		if ( 'caso' === tipo ) {
			var seccion = document.querySelector( '[data-clase-caso]' );
			var antes = seccion ? seccion.querySelectorAll( '[data-caso-paso]' ).length : 0;
			var nueva = reemplazar( seccion, datos.caso );

			// Si apareció la pregunta de seguimiento y quedó fuera de la
			// pantalla, se acerca con suavidad (sin saltar al inicio).
			if ( nueva ) {
				var pasos = nueva.querySelectorAll( '[data-caso-paso]' );

				if ( pasos.length > antes ) {
					pasos[ pasos.length - 1 ].scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
				}
			}

			return;
		}

		reemplazar( document.querySelector( '[data-clase-accion]' ), datos.accion );

		if ( datos.progreso ) {
			reemplazarProgreso( datos.progreso );
		}
	}

	document.addEventListener( 'submit', function ( evento ) {
		var form = evento.target;

		if ( ! form.matches || ! form.matches( 'form[data-clase-ajax]' ) ) {
			return;
		}

		evento.preventDefault();

		if ( form.classList.contains( 'is-enviando' ) ) {
			return;
		}

		var tipo = form.getAttribute( 'data-clase-ajax' );
		var boton = evento.submitter || form.querySelector( 'button[type="submit"]' );
		var input = form.querySelector( 'input[type="file"]' );
		var esHecha = 'mision' === tipo && boton && 'hecha' === boton.value;

		if ( esHecha && form.hasAttribute( 'data-requiere-foto' ) ) {
			var error = validarFoto( input && input.files && input.files[0] );

			if ( error ) {
				var zona = form.querySelector( '[data-mision-subir]' );

				if ( zona ) {
					zona.classList.add( 'is-error' );
				}

				alerta( form, error );
				return;
			}
		}

		var datos = new FormData( form );

		if ( boton && boton.name ) {
			datos.set( boton.name, boton.value );
		}

		// "La haremos después" no necesita subir la foto.
		if ( 'mision' === tipo && ! esHecha ) {
			datos.delete( 'evidencia' );
		}

		datos.set( 'action', 'afectivalab_clase_' + tipo );
		datos.set( 'clase', form.getAttribute( 'data-clase' ) );
		datos.set( 'hijo', form.getAttribute( 'data-hijo' ) );

		alerta( form, '' );
		cargando( form, boton, true );

		fetch( cfg.ajaxUrl, { method: 'POST', body: datos, credentials: 'same-origin' } )
			.then( function ( respuesta ) {
				return respuesta.json().catch( function () {
					return null;
				} );
			} )
			.then( function ( res ) {
				if ( ! res || ! res.success ) {
					cargando( form, boton, false );
					alerta( form, ( res && res.data && res.data.mensaje ) || cfg.errorRed );
					return;
				}

				aplicar( tipo, res.data );
			} )
			.catch( function () {
				cargando( form, boton, false );
				alerta( form, cfg.errorRed );
			} );
	} );
} )();
