( function () {
	'use strict';

	// Paneles condicionales según un grupo de radios: video (enlace o
	// archivo) y misión (casa o taller). Solo se muestra el panel del valor
	// elegido, para que no haya que adivinar cuál cuenta.
	//
	// Sin JS se ven todos los paneles, que es correcto: el formulario
	// funciona igual porque quien manda es el radio marcado, no el panel
	// visible — esto es solo para no mostrar campos que no aplican.
	//
	// data-video-panel="url" se muestra solo con ese valor exacto.
	// data-mision-panel="casa,taller" se muestra con cualquiera de esa lista
	// — la misión usa el mismo campo de texto para sus dos tipos.
	function activarGrupo( nombreRadio, atributoPanel ) {
		var radios = document.querySelectorAll( '[name="' + nombreRadio + '"]' );
		var panels = document.querySelectorAll( '[' + atributoPanel + ']' );

		if ( ! radios.length ) {
			return;
		}

		function syncPanels() {
			var checked = document.querySelector( '[name="' + nombreRadio + '"]:checked' );
			var valor = checked ? checked.value : '';

			Array.prototype.forEach.call( panels, function ( panel ) {
				var valores = panel.getAttribute( atributoPanel ).split( ',' );
				panel.hidden = valores.indexOf( valor ) === -1;
			} );
		}

		Array.prototype.forEach.call( radios, function ( radio ) {
			radio.addEventListener( 'change', syncPanels );
		} );

		syncPanels();
	}

	activarGrupo( 'video_tipo', 'data-video-panel' );
	activarGrupo( 'mision_tipo', 'data-mision-panel' );
} )();
