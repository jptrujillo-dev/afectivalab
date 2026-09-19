( function () {
	'use strict';

	// Menú móvil
	var toggle = document.querySelector( '.nav-toggle' );
	var mobileNav = document.querySelector( '.mobile-nav' );

	if ( toggle && mobileNav ) {
		toggle.addEventListener( 'click', function () {
			var isOpen = mobileNav.classList.toggle( 'is-open' );
			toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
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
} )();
