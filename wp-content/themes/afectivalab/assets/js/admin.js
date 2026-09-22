( function () {
	'use strict';

	// Campos de video de una microclase: solo se muestra el panel del tipo
	// elegido (enlace de YouTube/Vimeo o archivo subido), para que el
	// instructor no tenga que adivinar cuál de los dos es el que cuenta.
	var radios = document.querySelectorAll( '[name="afectivalab_video_tipo"]' );
	var panels = document.querySelectorAll( '[data-video-panel]' );

	if ( ! radios.length ) {
		return;
	}

	function syncPanels() {
		var checked = document.querySelector( '[name="afectivalab_video_tipo"]:checked' );
		var tipo = checked ? checked.value : 'ninguno';

		Array.prototype.forEach.call( panels, function ( panel ) {
			panel.hidden = panel.getAttribute( 'data-video-panel' ) !== tipo;
		} );
	}

	Array.prototype.forEach.call( radios, function ( radio ) {
		radio.addEventListener( 'change', syncPanels );
	} );

	syncPanels();

	var selectButton = document.querySelector( '[data-video-select]' );
	var clearButton = document.querySelector( '[data-video-clear]' );
	var field = document.getElementById( 'afectivalab_video_id' );
	var name = document.querySelector( '[data-video-name]' );

	if ( ! selectButton || ! field || ! window.wp || ! window.wp.media ) {
		return;
	}

	var frame;

	selectButton.addEventListener( 'click', function () {
		if ( ! frame ) {
			frame = window.wp.media( {
				title: selectButton.textContent,
				library: { type: 'video' },
				multiple: false
			} );

			frame.on( 'select', function () {
				var video = frame.state().get( 'selection' ).first().toJSON();

				field.value = video.id;

				if ( name ) {
					name.textContent = video.title || video.filename || '';
				}
			} );
		}

		frame.open();
	} );

	if ( clearButton ) {
		clearButton.addEventListener( 'click', function () {
			field.value = '';

			if ( name ) {
				name.textContent = '';
			}
		} );
	}
} )();
