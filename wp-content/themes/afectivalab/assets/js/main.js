( function () {
	'use strict';

	// Aparición de secciones al hacer scroll. Progressive enhancement:
	// la clase que oculta los elementos (.js-reveal) solo la agrega JS,
	// así que si algo falla aquí el contenido se ve normal, nunca oculto.
	var revealEls = document.querySelectorAll( '.reveal, .reveal-stagger' );

	if ( revealEls.length ) {
		document.documentElement.classList.add( 'js-reveal' );

		if ( 'IntersectionObserver' in window ) {
			var revealObserver = new IntersectionObserver(
				function ( entries, observer ) {
					entries.forEach( function ( entry ) {
						if ( entry.isIntersecting ) {
							entry.target.classList.add( 'is-visible' );
							observer.unobserve( entry.target );
						}
					} );
				},
				{ threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
			);

			revealEls.forEach( function ( el ) {
				revealObserver.observe( el );
			} );
		} else {
			revealEls.forEach( function ( el ) {
				el.classList.add( 'is-visible' );
			} );
		}
	}

	// Menú móvil
	var toggle = document.querySelector( '.nav-toggle' );
	var mobileNav = document.querySelector( '.mobile-nav' );

	if ( toggle && mobileNav ) {
		toggle.addEventListener( 'click', function () {
			var isOpen = mobileNav.classList.toggle( 'is-open' );
			toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );
	}

	// Chip de usuario logueado (header): abre/cierra el menú desplegable.
	var userMenu = document.querySelector( '[data-user-menu]' );
	var userMenuTrigger = document.querySelector( '[data-user-menu-trigger]' );

	if ( userMenu && userMenuTrigger ) {
		var closeUserMenu = function () {
			userMenu.classList.remove( 'is-open' );
			userMenuTrigger.setAttribute( 'aria-expanded', 'false' );
		};

		userMenuTrigger.addEventListener( 'click', function ( event ) {
			event.stopPropagation();
			var isOpen = userMenu.classList.toggle( 'is-open' );
			userMenuTrigger.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( ! userMenu.contains( event.target ) ) {
				closeUserMenu();
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && userMenu.classList.contains( 'is-open' ) ) {
				closeUserMenu();
				userMenuTrigger.focus();
			}
		} );
	}

	// Demo del caso interactivo (solo front-end, sin envío a servidor todavía)
	var caseDemo = document.querySelector( '[data-case-demo]' );

	if ( caseDemo ) {
		var options = caseDemo.querySelectorAll( '.case-option' );
		var feedback = caseDemo.querySelector( '.case-demo__feedback' );
		var feedbackTitle = caseDemo.querySelector( '.case-demo__feedback-title' );
		var feedbackText = caseDemo.querySelector( '.case-demo__feedback-text' );

		options.forEach( function ( option ) {
			option.addEventListener( 'click', function () {
				var isCorrect = option.dataset.correct === 'true';

				options.forEach( function ( opt ) {
					opt.classList.remove( 'is-selected', 'is-correct' );
				} );

				option.classList.add( 'is-selected' );

				if ( isCorrect ) {
					option.classList.add( 'is-correct' );
				}

				if ( feedback ) {
					feedback.classList.add( 'is-visible' );
					feedback.classList.toggle( 'is-guidance', ! isCorrect );
				}

				if ( feedbackTitle ) {
					feedbackTitle.textContent = option.dataset.feedbackTitle || '';
				}

				if ( feedbackText ) {
					feedbackText.textContent = option.dataset.feedbackText || '';
				}
			} );
		} );
	}

	// Confirmación para cualquier botón que borre algo, en todo el sitio
	// (perfiles de hijo, cursos, microclases). Es solo una red de seguridad:
	// sin JS el botón sigue funcionando, y todo lo que borra va a la papelera,
	// así que un clic de más se puede deshacer.
	//
	// En vez del confirm() del navegador se abre un modal propio con
	// animación (.confirmar-modal en base.css). Al aceptar se vuelve a hacer
	// clic en el mismo botón con una marca para dejarlo pasar: así el
	// formulario se envía con el name/value de ese botón, igual que antes.
	var modalConfirmar = null;

	function crearModalConfirmar() {
		var modal = document.createElement( 'div' );
		modal.className = 'confirmar-modal';
		modal.hidden = true;
		modal.innerHTML =
			'<div class="confirmar-modal__fondo" data-confirmar-cancelar></div>' +
			'<div class="confirmar-modal__caja" role="alertdialog" aria-modal="true" aria-labelledby="confirmar-modal-texto">' +
				'<span class="confirmar-modal__icono" aria-hidden="true">!</span>' +
				'<p class="confirmar-modal__texto" id="confirmar-modal-texto"></p>' +
				'<div class="confirmar-modal__botones">' +
					'<button type="button" class="btn btn-secondary" data-confirmar-cancelar>Cancelar</button>' +
					'<button type="button" class="btn confirmar-modal__aceptar" data-confirmar-aceptar></button>' +
				'</div>' +
			'</div>';
		document.body.appendChild( modal );
		return modal;
	}

	function cerrarModalConfirmar() {
		modalConfirmar.classList.remove( 'is-open' );
		document.removeEventListener( 'keydown', escConfirmar );

		window.setTimeout( function () {
			modalConfirmar.hidden = true;
		}, 200 );
	}

	function escConfirmar( event ) {
		if ( 'Escape' === event.key ) {
			cerrarModalConfirmar();
		}
	}

	function abrirModalConfirmar( button ) {
		if ( ! modalConfirmar ) {
			modalConfirmar = crearModalConfirmar();
		}

		var aceptar = modalConfirmar.querySelector( '[data-confirmar-aceptar]' );

		modalConfirmar.querySelector( '.confirmar-modal__texto' ).textContent = button.getAttribute( 'data-confirm' );
		aceptar.textContent = button.getAttribute( 'data-confirm-boton' ) || 'Sí, continuar';

		aceptar.onclick = function () {
			cerrarModalConfirmar();
			button.setAttribute( 'data-confirmado', '1' );
			button.click();
		};

		Array.prototype.forEach.call( modalConfirmar.querySelectorAll( '[data-confirmar-cancelar]' ), function ( el ) {
			el.onclick = cerrarModalConfirmar;
		} );

		modalConfirmar.hidden = false;
		// Un frame después, para que la transición de entrada sí se vea.
		window.requestAnimationFrame( function () {
			modalConfirmar.classList.add( 'is-open' );
			aceptar.focus();
		} );
		document.addEventListener( 'keydown', escConfirmar );
	}

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '[data-confirm]' );

		if ( ! button ) {
			return;
		}

		if ( button.hasAttribute( 'data-confirmado' ) ) {
			button.removeAttribute( 'data-confirmado' );
			return;
		}

		event.preventDefault();
		abrirModalConfirmar( button );
	} );
} )();
