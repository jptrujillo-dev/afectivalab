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

	// Mostrar/ocultar contraseña en los formularios de registro/ingreso
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
} )();
