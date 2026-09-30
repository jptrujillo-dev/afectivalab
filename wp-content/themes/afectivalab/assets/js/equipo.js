( function () {
	'use strict';

	// Panel del equipo de contenido sin recargas.
	//
	// Todo formulario con data-eq-ajax se envía por AJAX a inc/panel-ajax.php,
	// que usa exactamente la misma lógica y los mismos permisos que el envío
	// normal (inc/panel-admin.php). Cada tipo tiene su manejador abajo, que
	// actualiza la pantalla en su lugar. Si esto no carga, los formularios se
	// envían como siempre y nada se rompe.
	//
	// Además: avisos flotantes, modales de creación rápida, buscador y
	// filtros de los listados, y en el editor el aviso de "cambios sin
	// guardar", Ctrl+S y la vista previa de la imagen.
	var cfg = window.afectivalabEquipo;
	var panel = document.querySelector( '[data-eq-panel]' );

	if ( ! cfg || ! panel ) {
		return;
	}

	var T = cfg.textos;
	var puedeAjax = !! ( window.fetch && window.FormData );
	var cadaUno = function ( lista, fn ) {
		Array.prototype.forEach.call( lista, fn );
	};

	document.documentElement.classList.add( 'eq-js' );

	function normalizar( texto ) {
		texto = String( texto || '' ).toLowerCase();

		if ( texto.normalize ) {
			texto = texto.normalize( 'NFD' ).replace( /[̀-ͯ]/g, '' );
		}

		return texto.trim();
	}

	function nodoDesde( html ) {
		var tmp = document.createElement( 'div' );
		tmp.innerHTML = String( html || '' ).trim();
		return tmp.firstElementChild;
	}

	// ------------------------------------------------------------------
	// Avisos flotantes
	// ------------------------------------------------------------------
	var avisos = panel.querySelector( '[data-eq-avisos]' );
	var avisosPorClave = {};

	function aviso( mensaje, tipo, clave ) {
		if ( ! avisos || ! mensaje ) {
			return;
		}

		var el = clave && avisosPorClave[ clave ];

		if ( ! el ) {
			el = document.createElement( 'div' );
			el.innerHTML = '<span class="eq-aviso-flotante__icono" aria-hidden="true"></span><span class="eq-aviso-flotante__texto"></span>';
			avisos.appendChild( el );
			window.requestAnimationFrame( function () {
				el.classList.add( 'is-visible' );
			} );

			if ( clave ) {
				avisosPorClave[ clave ] = el;
			}
		}

		el.className = 'eq-aviso-flotante is-visible' + ( 'error' === tipo ? ' is-error' : '' );
		el.setAttribute( 'role', 'error' === tipo ? 'alert' : 'status' );
		el.querySelector( '.eq-aviso-flotante__texto' ).textContent = mensaje;

		window.clearTimeout( el.temporizador );
		el.temporizador = window.setTimeout( function () {
			el.classList.remove( 'is-visible' );

			if ( clave ) {
				delete avisosPorClave[ clave ];
			}

			window.setTimeout( function () {
				el.remove();
			}, 300 );
		}, 'error' === tipo ? 6500 : 3500 );
	}

	// El aviso que dejó una redirección se muestra flotante, y se limpia la
	// URL para que recargar no lo repita.
	cadaUno( panel.querySelectorAll( '[data-eq-aviso-inicial]' ), function ( el ) {
		aviso( el.textContent.trim(), el.getAttribute( 'data-eq-aviso-inicial' ) );
		el.remove();
	} );

	if ( window.history && window.history.replaceState && window.URL && /[?&](msg|error)=/.test( window.location.search ) ) {
		var limpia = new URL( window.location.href );
		limpia.searchParams.delete( 'msg' );
		limpia.searchParams.delete( 'error' );
		window.history.replaceState( null, '', limpia.toString() );
	}

	// ------------------------------------------------------------------
	// Errores dentro de un formulario (editor o modal)
	// ------------------------------------------------------------------
	function alertaDe( form ) {
		var contenedor = form.closest( '[data-eq-editor]' ) || form;
		return contenedor.querySelector( '[data-eq-alerta]' );
	}

	function mostrarErrores( form, errores ) {
		var alerta = alertaDe( form );

		if ( ! alerta ) {
			return;
		}

		if ( ! errores || ! errores.length ) {
			alerta.hidden = true;
			alerta.innerHTML = '';
			return;
		}

		var ul = document.createElement( 'ul' );

		errores.forEach( function ( error ) {
			var li = document.createElement( 'li' );
			li.textContent = error;
			ul.appendChild( li );
		} );

		alerta.innerHTML = '';
		alerta.appendChild( ul );
		alerta.hidden = false;
		alerta.classList.remove( 'is-sacude' );
		void alerta.offsetWidth;
		alerta.classList.add( 'is-sacude' );

		if ( form.closest( '[data-eq-editor]' ) ) {
			alerta.scrollIntoView( { behavior: 'smooth', block: 'center' } );
		}
	}

	// ------------------------------------------------------------------
	// Envío
	// ------------------------------------------------------------------
	function enviar( datos ) {
		datos.set( 'action', 'afectivalab_panel' );

		return fetch( cfg.ajaxUrl, { method: 'POST', body: datos, credentials: 'same-origin' } )
			.then( function ( respuesta ) {
				return respuesta.json().catch( function () {
					return null;
				} );
			} )
			.then( function ( res ) {
				if ( ! res ) {
					throw new Error( 'red' );
				}

				return res;
			} );
	}

	function cargando( boton, activo ) {
		if ( ! boton ) {
			return;
		}

		boton.disabled = activo;
		boton.classList.toggle( 'is-cargando', activo );

		if ( ! boton.classList.contains( 'btn' ) ) {
			return;
		}

		var spinner = boton.querySelector( '.btn__spinner' );

		if ( activo && ! spinner ) {
			spinner = document.createElement( 'span' );
			spinner.className = 'btn__spinner';
			boton.insertBefore( spinner, boton.firstChild );
		} else if ( ! activo && spinner ) {
			spinner.remove();
		}
	}

	// Mover microclases va en fila: si alguien pulsa varias flechas seguidas,
	// cada orden llega al servidor después de la anterior.
	var colaSerie = Promise.resolve();

	document.addEventListener( 'submit', function ( evento ) {
		var form = evento.target;

		if ( ! puedeAjax || ! form.matches || ! form.matches( 'form[data-eq-ajax]' ) ) {
			return;
		}

		var tipo = form.getAttribute( 'data-eq-ajax' );
		var manejador = manejadores[ tipo ];

		if ( ! manejador ) {
			return;
		}

		evento.preventDefault();

		if ( form.classList.contains( 'is-enviando' ) && ! manejador.serie ) {
			return;
		}

		if ( 'guardar' === tipo && window.tinyMCE && window.tinyMCE.triggerSave ) {
			window.tinyMCE.triggerSave();
		}

		var boton = evento.submitter || form.querySelector( '[type="submit"]' );
		var datos = new FormData( form );

		if ( manejador.antes && false === manejador.antes( form, datos, boton ) ) {
			return;
		}

		if ( ! manejador.serie ) {
			form.classList.add( 'is-enviando' );
			cargando( boton, true );
		}

		var terminar = function () {
			form.classList.remove( 'is-enviando' );

			if ( ! manejador.serie ) {
				cargando( boton, false );
			}
		};

		var peticion = manejador.serie
			? ( colaSerie = colaSerie.then( function () {
				return enviar( datos );
			}, function () {
				return enviar( datos );
			} ) )
			: enviar( datos );

		peticion
			.then( function ( res ) {
				terminar();

				if ( res.success ) {
					manejador.ok( form, res.data || {}, boton );
				} else {
					manejador.error( form, res.data || {}, boton );
				}
			} )
			.catch( function () {
				terminar();
				manejador.error( form, { mensaje: T.red }, boton );
			} );
	} );

	// ------------------------------------------------------------------
	// Manejadores por tipo de formulario
	// ------------------------------------------------------------------
	function pintarSwitch( form, encendido, etiqueta ) {
		var boton = form.querySelector( '.eq-switch' );
		var siguiente = form.querySelector( '[data-eq-estado-siguiente]' );
		var texto = form.querySelector( '[data-eq-estado-texto]' );
		var fila = form.closest( '[data-eq-item]' );

		boton.classList.toggle( 'is-on', encendido );
		boton.setAttribute( 'aria-checked', encendido ? 'true' : 'false' );
		siguiente.value = encendido ? 'draft' : 'publish';

		if ( etiqueta ) {
			texto.textContent = etiqueta;
		}

		if ( fila ) {
			fila.setAttribute( 'data-estado', encendido ? 'publish' : 'draft' );
		}
	}

	function quitarFila( fila ) {
		var lista = fila.parentNode;
		var grupo = fila.closest( '[data-eq-grupo]' );

		fila.style.height = fila.offsetHeight + 'px';
		fila.classList.add( 'is-saliendo' );

		window.setTimeout( function () {
			fila.remove();

			if ( grupo && ! grupo.querySelector( '[data-eq-item]' ) ) {
				grupo.remove();
			}

			if ( lista && lista.hasAttribute( 'data-eq-orden-lista' ) ) {
				renumerar( lista );
			}
		}, 320 );
	}

	function renumerar( lista ) {
		cadaUno( lista.querySelectorAll( ':scope > [data-eq-item]' ), function ( fila, i ) {
			var numero = fila.querySelector( '[data-eq-numero]' );

			if ( numero ) {
				numero.textContent = i + 1;
			}
		} );
	}

	// Intercambia una fila con su vecina, con una animación de deslizamiento
	// (se mide dónde estaban, se mueven, y se animan desde ahí).
	function moverFila( fila, direccion ) {
		var lista = fila.parentNode;
		var vecina = 'arriba' === direccion ? fila.previousElementSibling : fila.nextElementSibling;

		if ( ! vecina ) {
			return false;
		}

		var filas = [ fila, vecina ];
		var antes = filas.map( function ( f ) {
			return f.getBoundingClientRect().top;
		} );

		if ( 'arriba' === direccion ) {
			lista.insertBefore( fila, vecina );
		} else {
			lista.insertBefore( vecina, fila );
		}

		filas.forEach( function ( f, i ) {
			f.style.transition = 'none';
			f.style.transform = 'translateY(' + ( antes[ i ] - f.getBoundingClientRect().top ) + 'px)';
		} );

		window.requestAnimationFrame( function () {
			window.requestAnimationFrame( function () {
				filas.forEach( function ( f ) {
					f.style.transition = 'transform 0.25s ease';
					f.style.transform = '';
				} );
			} );
		} );

		fila.classList.remove( 'is-movida' );
		void fila.offsetWidth;
		fila.classList.add( 'is-movida' );
		renumerar( lista );

		return true;
	}

	var manejadores = {
		estado: {
			antes: function ( form ) {
				// Se ve el cambio al instante; si el servidor lo rechaza, se
				// vuelve atrás.
				var boton = form.querySelector( '.eq-switch' );
				form.previo = {
					on: boton.classList.contains( 'is-on' ),
					texto: form.querySelector( '[data-eq-estado-texto]' ).textContent
				};
				pintarSwitch( form, ! form.previo.on );
			},
			ok: function ( form, datos ) {
				pintarSwitch( form, 'publish' === datos.estado, datos.etiqueta );
				aviso( datos.mensaje );
			},
			error: function ( form, datos ) {
				if ( form.previo ) {
					pintarSwitch( form, form.previo.on, form.previo.texto );
				}
				aviso( datos.mensaje || T.red, 'error' );
			}
		},

		eliminar: {
			ok: function ( form, datos ) {
				if ( 'lista' === form.getAttribute( 'data-eq-despues' ) ) {
					editor.sucio = false;
					window.location.href = datos.url_lista;
					return;
				}

				var fila = form.closest( '[data-eq-item]' );

				if ( fila ) {
					quitarFila( fila );
				}

				aviso( datos.mensaje );
			},
			error: function ( form, datos ) {
				aviso( datos.mensaje || T.red, 'error' );
			}
		},

		mover: {
			serie: true,
			antes: function ( form, datos, boton ) {
				var fila = form.closest( '[data-eq-item]' );

				if ( ! fila || ! moverFila( fila, datos.get( 'direccion' ) ) ) {
					return false;
				}

				if ( boton ) {
					boton.focus();
				}
			},
			ok: function ( form, datos ) {
				aviso( datos.mensaje, 'exito', 'orden' );
			},
			error: function ( form, datos ) {
				var fila = form.closest( '[data-eq-item]' );
				var direccion = form.querySelector( '[name="direccion"]' ).value;

				if ( fila ) {
					moverFila( fila, 'arriba' === direccion ? 'abajo' : 'arriba' );
				}

				aviso( datos.mensaje || T.red, 'error', 'orden' );
			}
		},

		rol: {
			ok: function ( form, datos ) {
				var select = form.querySelector( 'select' );
				var fila = form.closest( '[data-eq-item]' );

				select.setAttribute( 'data-anterior', select.value );

				if ( fila ) {
					fila.setAttribute( 'data-rol', 'afectivalab_instructor' === select.value ? 'instructor' : 'padre' );
					fila.classList.remove( 'is-destello' );
					void fila.offsetWidth;
					fila.classList.add( 'is-destello' );
				}

				aviso( datos.mensaje );
			},
			error: function ( form, datos ) {
				var select = form.querySelector( 'select' );
				select.value = select.getAttribute( 'data-anterior' );
				aviso( datos.mensaje || T.red, 'error' );
			}
		},

		guardar: {
			antes: function ( form ) {
				mostrarErrores( form, [] );

				if ( form === editor.form ) {
					editor.indicador( T.guardando, 'is-guardando' );
				}
			},
			ok: function ( form, datos ) {
				var modal = form.closest( '[data-eq-modal]' );

				if ( modal ) {
					guardadoDesdeModal( form, modal, datos );
					return;
				}

				editor.guardado( datos );
			},
			error: function ( form, datos ) {
				mostrarErrores( form, datos.errores && datos.errores.length ? datos.errores : [ datos.mensaje || T.red ] );
				aviso( datos.mensaje || T.red, 'error' );

				if ( form === editor.form ) {
					editor.indicador( T.sinGuardar, 'is-sucio' );
				}
			}
		}
	};

	// El rol se guarda apenas se elige: no hace falta el botón "Cambiar".
	document.addEventListener( 'change', function ( evento ) {
		var select = evento.target;

		if ( ! select.matches || ! select.matches( 'select[data-eq-auto]' ) || ! puedeAjax ) {
			return;
		}

		if ( select.form.requestSubmit ) {
			select.form.requestSubmit();
		} else {
			select.form.dispatchEvent( new Event( 'submit', { cancelable: true, bubbles: true } ) );
		}
	} );

	// ------------------------------------------------------------------
	// Modales de creación rápida
	// ------------------------------------------------------------------
	var modalAbierto = null;
	var modalOrigen = null;

	function abrirModal( modal, origen ) {
		var form = modal.querySelector( 'form' );
		var curso = origen ? origen.getAttribute( 'data-eq-curso' ) : '';
		var desde = modal.querySelector( '[data-eq-desde]' );

		// "Nueva microclase" abierta desde un curso: el curso viene elegido
		// y la clase se agrega a su lista sin salir de la página.
		if ( desde ) {
			var select = modal.querySelector( '[data-eq-curso-select]' );
			var textoCurso = modal.querySelector( '[data-eq-modal-texto-curso]' );
			var textoGeneral = modal.querySelector( '[data-eq-modal-texto-general]' );

			desde.value = curso ? 'curso' : '';
			select.value = curso || '0';
			select.closest( '.eq-campo' ).hidden = !! curso;
			textoCurso.hidden = ! curso;
			textoGeneral.hidden = !! curso;
		}

		modalOrigen = origen;
		modalAbierto = modal;
		modal.hidden = false;
		document.body.classList.add( 'eq-modal-abierto' );

		window.requestAnimationFrame( function () {
			// Si lo cerraron antes de este cuadro, no se vuelve a abrir.
			if ( modalAbierto !== modal ) {
				return;
			}

			modal.classList.add( 'is-open' );
			var foco = modal.querySelector( '[data-eq-foco]' );

			if ( foco ) {
				foco.focus();
			}
		} );

		mostrarErrores( form, [] );
	}

	function cerrarModal() {
		var modal = modalAbierto;

		if ( ! modal ) {
			return;
		}

		modalAbierto = null;
		modal.classList.remove( 'is-open' );
		document.body.classList.remove( 'eq-modal-abierto' );

		window.setTimeout( function () {
			modal.hidden = true;
			modal.querySelector( 'form' ).reset();
		}, 220 );

		if ( modalOrigen ) {
			modalOrigen.focus();
		}
	}

	function guardadoDesdeModal( form, modal, datos ) {
		var lista = document.querySelector( '[data-eq-orden-lista]' );

		// Desde un curso: la fila nueva aparece al final de su lista.
		if ( datos.fila && lista ) {
			var fila = nodoDesde( datos.fila );
			var vacio = document.querySelector( '[data-eq-curso-clases] [data-eq-vacio]' );

			fila.classList.add( 'is-entrando' );
			lista.appendChild( fila );
			renumerar( lista );

			if ( vacio ) {
				vacio.hidden = true;
			}

			cerrarModal();
			aviso( datos.mensaje );
			return;
		}

		// Si no, se abre su editor para seguir completándolo.
		var accion = form.querySelector( '[name="afectivalab_panel_accion"]' ).value;
		var msg = 'guardar_curso' === accion ? 'curso-creado' : 'clase-creada';

		window.location.href = datos.url_editar + ( -1 === datos.url_editar.indexOf( '?' ) ? '?' : '&' ) + 'msg=' + msg;
	}

	document.addEventListener( 'click', function ( evento ) {
		var abrir = evento.target.closest( '[data-eq-modal-abrir]' );

		if ( abrir ) {
			var modal = panel.querySelector( '[data-eq-modal="' + abrir.getAttribute( 'data-eq-modal-abrir' ) + '"]' );

			// Sin modal (o sin AJAX) el enlace lleva a la pantalla completa.
			if ( modal && puedeAjax ) {
				evento.preventDefault();
				abrirModal( modal, abrir );
			}

			return;
		}

		if ( modalAbierto && evento.target.closest( '[data-eq-modal-cerrar]' ) ) {
			evento.preventDefault();
			cerrarModal();
		}
	} );

	document.addEventListener( 'keydown', function ( evento ) {
		if ( ! modalAbierto ) {
			return;
		}

		if ( 'Escape' === evento.key ) {
			cerrarModal();
			return;
		}

		// El foco no se escapa del modal con Tab.
		if ( 'Tab' === evento.key ) {
			var enfocables = modalAbierto.querySelectorAll( 'button:not([disabled]), input:not([type="hidden"]), select, textarea, a[href]' );
			var visibles = Array.prototype.filter.call( enfocables, function ( el ) {
				return el.offsetParent !== null;
			} );
			var primero = visibles[0];
			var ultimo = visibles[ visibles.length - 1 ];

			if ( evento.shiftKey && document.activeElement === primero ) {
				evento.preventDefault();
				ultimo.focus();
			} else if ( ! evento.shiftKey && document.activeElement === ultimo ) {
				evento.preventDefault();
				primero.focus();
			}
		}
	} );

	// ------------------------------------------------------------------
	// Buscador y filtros de los listados
	// ------------------------------------------------------------------
	cadaUno( panel.querySelectorAll( '[data-eq-filtros]' ), function ( barra ) {
		var id = barra.getAttribute( 'data-eq-filtros' );
		var lista = document.getElementById( id );
		var sinResultados = panel.querySelector( '[data-eq-sin-resultados="' + id + '"]' );
		var buscar = barra.querySelector( '[data-eq-buscar]' );
		var estado = { texto: '', filtros: {} };
		var espera = null;

		if ( ! lista ) {
			return;
		}

		barra.hidden = false;

		function aplicar() {
			var visibles = 0;

			cadaUno( lista.querySelectorAll( '[data-eq-item]' ), function ( item ) {
				var ok = ! estado.texto || -1 !== ( item.getAttribute( 'data-texto' ) || '' ).indexOf( estado.texto );

				Object.keys( estado.filtros ).forEach( function ( clave ) {
					if ( estado.filtros[ clave ] && item.getAttribute( 'data-' + clave ) !== estado.filtros[ clave ] ) {
						ok = false;
					}
				} );

				item.hidden = ! ok;
				visibles += ok ? 1 : 0;
			} );

			cadaUno( lista.querySelectorAll( '[data-eq-grupo]' ), function ( grupo ) {
				var alguno = grupo.querySelector( '[data-eq-item]:not([hidden])' );
				grupo.hidden = ! alguno;

				// Al buscar se abren los grupos plegados que tienen resultados.
				if ( alguno && estado.texto ) {
					grupo.open = true;
				}
			} );

			if ( sinResultados ) {
				sinResultados.hidden = visibles > 0;
			}
		}

		if ( buscar ) {
			buscar.addEventListener( 'input', function () {
				window.clearTimeout( espera );
				espera = window.setTimeout( function () {
					estado.texto = normalizar( buscar.value );
					aplicar();
				}, 120 );
			} );
		}

		barra.addEventListener( 'click', function ( evento ) {
			var chip = evento.target.closest( '[data-eq-filtro]' );

			if ( ! chip ) {
				return;
			}

			var clave = chip.getAttribute( 'data-eq-filtro' );

			cadaUno( barra.querySelectorAll( '[data-eq-filtro="' + clave + '"]' ), function ( otro ) {
				otro.classList.toggle( 'is-activo', otro === chip );
				otro.setAttribute( 'aria-pressed', otro === chip ? 'true' : 'false' );
			} );

			estado.filtros[ clave ] = chip.getAttribute( 'data-valor' );
			aplicar();
		} );

		cadaUno( barra.querySelectorAll( '[data-eq-filtro-select]' ), function ( select ) {
			select.addEventListener( 'change', function () {
				estado.filtros[ select.getAttribute( 'data-eq-filtro-select' ) ] = select.value;
				aplicar();
			} );
		} );
	} );

	// ------------------------------------------------------------------
	// Editor de curso / microclase
	// ------------------------------------------------------------------
	var editor = { sucio: false, form: null };
	var raizEditor = panel.querySelector( '[data-eq-editor]' );

	if ( raizEditor ) {
		iniciarEditor( raizEditor );
	}

	function iniciarEditor( raiz ) {
		var form = raiz.querySelector( 'form[data-eq-ajax="guardar"]' );
		var guardadoEl = raiz.querySelector( '[data-eq-guardado]' );
		var guardarBoton = raiz.querySelector( '[data-eq-guardar]' );

		editor.form = form;

		editor.indicador = function ( texto, clase ) {
			if ( ! guardadoEl ) {
				return;
			}

			guardadoEl.textContent = texto;
			guardadoEl.className = 'eq-guardado' + ( clase ? ' ' + clase : '' );
		};

		function marcarSucio() {
			if ( ! editor.sucio ) {
				editor.sucio = true;
				editor.indicador( T.sinGuardar, 'is-sucio' );
			}
		}

		function guardar() {
			if ( form.requestSubmit ) {
				form.requestSubmit( guardarBoton || undefined );
			} else if ( guardarBoton ) {
				guardarBoton.click();
			}
		}

		// Cualquier campo del formulario, incluidos los de la barra lateral
		// (asociados con form=), marca la página como "sin guardar".
		[ 'input', 'change' ].forEach( function ( tipo ) {
			document.addEventListener( tipo, function ( evento ) {
				if ( evento.target.form === form ) {
					marcarSucio();
				}
			} );
		} );

		// El editor con formato vive en un iframe: se escucha aparte, y ahí
		// también funciona Ctrl+S.
		function engancharTiny( ed ) {
			ed.on( 'input change undo redo paste', marcarSucio );
			ed.on( 'keydown', function ( evento ) {
				if ( ( evento.ctrlKey || evento.metaKey ) && 's' === String( evento.key ).toLowerCase() ) {
					evento.preventDefault();
					guardar();
				}
			} );
		}

		if ( window.tinymce ) {
			( window.tinymce.editors || [] ).forEach( engancharTiny );
			window.tinymce.on( 'AddEditor', function ( e ) {
				engancharTiny( e.editor );
			} );
		}

		document.addEventListener( 'keydown', function ( evento ) {
			if ( ( evento.ctrlKey || evento.metaKey ) && 's' === String( evento.key ).toLowerCase() && ! modalAbierto ) {
				evento.preventDefault();
				guardar();
			}
		} );

		window.addEventListener( 'beforeunload', function ( evento ) {
			if ( editor.sucio ) {
				evento.preventDefault();
				evento.returnValue = T.salir;
				return T.salir;
			}
		} );

		// Título en vivo en la cabecera.
		var tituloInput = raiz.querySelector( '[data-eq-titulo-input]' );
		var tituloH = raiz.querySelector( '[data-eq-titulo]' );

		if ( tituloInput && tituloH ) {
			tituloInput.addEventListener( 'input', function () {
				tituloH.textContent = tituloInput.value.trim() || tituloH.getAttribute( 'data-vacio' );
			} );
		}

		// Vista previa de la insignia.
		var habilidad = raiz.querySelector( '[data-eq-habilidad]' );
		var habilidadPreview = raiz.querySelector( '[data-eq-habilidad-preview]' );

		if ( habilidad && habilidadPreview ) {
			habilidad.addEventListener( 'input', function () {
				habilidadPreview.textContent = habilidad.value.trim() || habilidadPreview.getAttribute( 'data-vacio' );
			} );
		}

		// Contador de caracteres del resumen.
		cadaUno( raiz.querySelectorAll( '[data-eq-contador]' ), function ( campo ) {
			var max = parseInt( campo.getAttribute( 'data-eq-contador' ), 10 );
			var salida = campo.parentNode.querySelector( '[data-eq-contador-texto]' );

			function contar() {
				if ( salida ) {
					salida.textContent = T.caracteres.replace( '%1$d', campo.value.length ).replace( '%2$d', max );
					salida.classList.toggle( 'is-pasado', campo.value.length > max );
				}
			}

			campo.addEventListener( 'input', contar );
			contar();
		} );

		// Opción recomendada del caso: la tarjeta se resalta.
		raiz.addEventListener( 'change', function ( evento ) {
			if ( evento.target.matches( '[data-eq-recomendada]' ) ) {
				evento.target.closest( '[data-eq-caso-opcion]' ).classList.toggle( 'is-recomendada', evento.target.checked );
			}
		} );

		iniciarImagen( raiz );

		// Después de guardar: todo se actualiza en su lugar.
		editor.guardado = function ( datos ) {
			editor.sucio = false;

			var id = form.querySelector( '[data-eq-id]' );

			if ( id ) {
				id.value = datos.id;
			}

			if ( datos.creado ) {
				// Ya existe: la URL pasa a ser la de su edición (recargar no
				// crea otro) y aparece lo que solo tiene sentido guardado.
				if ( window.history && window.history.replaceState ) {
					window.history.replaceState( null, '', datos.url_editar );
				}

				var tipo = raiz.querySelector( '.eq-editor__tipo' );

				if ( tipo ) {
					tipo.textContent = 'curso' === raiz.getAttribute( 'data-eq-editor' ) ? 'Editando curso' : 'Editando microclase';
				}

				if ( guardarBoton ) {
					guardarBoton.textContent = T.guardar;
				}

				if ( datos.clases_html ) {
					var clases = raiz.querySelector( '[data-eq-curso-clases]' );
					var nuevas = nodoDesde( datos.clases_html );

					if ( clases && nuevas ) {
						nuevas.classList.add( 'is-entrando' );
						clases.replaceWith( nuevas );
					}
				}
			}

			var ver = raiz.querySelector( '[data-eq-ver]' );

			if ( ver && datos.url_ver ) {
				ver.href = datos.url_ver;
				ver.hidden = false;
			}

			if ( tituloH && datos.titulo ) {
				tituloH.textContent = datos.titulo;
			}

			var chip = raiz.querySelector( '[data-eq-estado-chip]' );

			if ( chip ) {
				chip.textContent = datos.estado_label;
				chip.classList.toggle( 'is-publicado', 'publish' === datos.estado );
			}

			// Si pidió publicar sin permiso, el servidor lo dejó en borrador:
			// los radios muestran lo que de verdad quedó.
			cadaUno( document.querySelectorAll( '[name="estado"][form="' + form.id + '"]' ), function ( radio ) {
				if ( 'radio' === radio.type ) {
					radio.checked = radio.value === ( 'publish' === datos.estado ? 'publish' : 'draft' );
				}
			} );

			imagenGuardada( raiz, datos.imagen );

			editor.indicador( datos.modificado || T.guardado, 'is-guardado' );

			if ( datos.errores && datos.errores.length ) {
				mostrarErrores( form, datos.errores );
				aviso( datos.errores[0], 'error' );
			} else {
				aviso( datos.mensaje );
			}
		};
	}

	// ------------------------------------------------------------------
	// Imagen destacada: validación y vista previa
	// ------------------------------------------------------------------
	var TIPOS_IMAGEN = [ 'image/jpeg', 'image/png', 'image/webp' ];

	function megas( bytes ) {
		return ( bytes / 1048576 ).toFixed( 1 ).replace( '.0', '' );
	}

	function iniciarImagen( raiz ) {
		var caja = raiz.querySelector( '[data-eq-imagen]' );

		if ( ! caja ) {
			return;
		}

		var input = caja.querySelector( '[data-eq-imagen-input]' );
		var quitar = caja.querySelector( '[data-eq-imagen-quitar]' );
		var preview = caja.querySelector( '[data-eq-imagen-preview]' );
		var error = caja.querySelector( '[data-eq-imagen-error]' );
		var texto = caja.querySelector( '[data-eq-imagen-texto]' );

		caja.original = preview.querySelector( 'img' );

		input.addEventListener( 'change', function () {
			var archivo = input.files && input.files[0];
			var mensaje = '';

			error.hidden = true;

			if ( ! archivo ) {
				return;
			}

			var porNombre = /\.(jpe?g|png|webp)$/i.test( archivo.name || '' );

			if ( -1 === TIPOS_IMAGEN.indexOf( archivo.type ) && ! ( '' === archivo.type && porNombre ) ) {
				mensaje = T.imagenTipo;
			} else if ( cfg.maxImagen && archivo.size > cfg.maxImagen ) {
				mensaje = T.imagenPeso.replace( '%1$s', megas( archivo.size ) ).replace( '%2$s', megas( cfg.maxImagen ) );
			}

			if ( mensaje ) {
				input.value = '';
				error.textContent = mensaje;
				error.hidden = false;
				caja.classList.remove( 'is-sacude' );
				void caja.offsetWidth;
				caja.classList.add( 'is-sacude' );
				return;
			}

			var img = preview.querySelector( 'img' );

			if ( img && img.dataset.objeto ) {
				URL.revokeObjectURL( img.dataset.objeto );
			}

			if ( ! img || img === caja.original ) {
				if ( img ) {
					img.hidden = true;
				}

				img = document.createElement( 'img' );
				img.alt = '';
				preview.insertBefore( img, preview.firstChild );
			}

			img.dataset.objeto = URL.createObjectURL( archivo );
			img.src = img.dataset.objeto;

			quitar.checked = false;
			caja.classList.remove( 'is-quitando' );
			caja.classList.add( 'tiene-imagen', 'is-nueva' );
			texto.textContent = T.cambiar;
		} );

		quitar.addEventListener( 'change', function () {
			caja.classList.toggle( 'is-quitando', quitar.checked );
		} );
	}

	function imagenGuardada( raiz, html ) {
		var caja = raiz.querySelector( '[data-eq-imagen]' );

		if ( ! caja ) {
			return;
		}

		var preview = caja.querySelector( '[data-eq-imagen-preview]' );

		cadaUno( preview.querySelectorAll( 'img' ), function ( img ) {
			if ( img.dataset.objeto ) {
				URL.revokeObjectURL( img.dataset.objeto );
			}
			img.remove();
		} );

		var nueva = html ? nodoDesde( html ) : null;

		if ( nueva ) {
			preview.insertBefore( nueva, preview.firstChild );
		}

		caja.original = nueva;
		caja.querySelector( '[data-eq-imagen-input]' ).value = '';
		caja.querySelector( '[data-eq-imagen-quitar]' ).checked = false;
		caja.classList.remove( 'is-quitando', 'is-nueva' );
		caja.classList.toggle( 'tiene-imagen', !! nueva );
		caja.querySelector( '[data-eq-imagen-texto]' ).textContent = nueva ? T.cambiar : T.elegir;
	}
} )();
