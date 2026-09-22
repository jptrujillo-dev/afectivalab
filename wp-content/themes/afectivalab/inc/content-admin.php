<?php
/**
 * Pantallas de escritorio para el equipo de contenido: campos propios de
 * curso y microclase, y columnas de listado.
 *
 * El video de una microclase puede venir de YouTube/Vimeo por URL o de un
 * archivo subido al sitio — el cliente pidió explícitamente soportar las dos
 * formas, así que el campo es un selector entre ambas, no una sola casilla.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function afectivalab_content_meta_boxes() {
	add_meta_box(
		'afectivalab-curso-detalle',
		__( 'Detalle del curso', 'afectivalab' ),
		'afectivalab_curso_meta_box',
		AFECTIVALAB_CPT_CURSO,
		'side',
		'high'
	);

	add_meta_box(
		'afectivalab-clase-detalle',
		__( 'Detalle de la microclase', 'afectivalab' ),
		'afectivalab_clase_meta_box',
		AFECTIVALAB_CPT_CLASE,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'afectivalab_content_meta_boxes' );

function afectivalab_curso_meta_box( $post ) {
	wp_nonce_field( 'afectivalab_guardar_curso', 'afectivalab_curso_nonce' );

	$habilidad = get_post_meta( $post->ID, '_afectivalab_habilidad', true );
	?>
	<p>
		<label class="afectivalab-field__label" for="afectivalab_habilidad"><?php esc_html_e( 'Habilidad adquirida', 'afectivalab' ); ?></label>
		<input type="text" id="afectivalab_habilidad" name="afectivalab_habilidad" value="<?php echo esc_attr( $habilidad ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Ej. Prevención y manejo del bullying', 'afectivalab' ); ?>">
		<span class="description"><?php esc_html_e( 'Es lo que gana el padre al terminar el curso: da nombre a su insignia y a su certificado.', 'afectivalab' ); ?></span>
	</p>
	<?php
}

function afectivalab_clase_meta_box( $post ) {
	wp_nonce_field( 'afectivalab_guardar_clase', 'afectivalab_clase_nonce' );

	$curso_id   = (int) get_post_meta( $post->ID, '_afectivalab_curso', true );
	$duracion   = (int) get_post_meta( $post->ID, '_afectivalab_duracion', true );
	$video_tipo = get_post_meta( $post->ID, '_afectivalab_video_tipo', true );
	$video_url  = get_post_meta( $post->ID, '_afectivalab_video_url', true );
	$video_id   = (int) get_post_meta( $post->ID, '_afectivalab_video_id', true );

	if ( ! $video_tipo ) {
		$video_tipo = 'ninguno';
	}

	$cursos = get_posts(
		array(
			'post_type'      => AFECTIVALAB_CPT_CURSO,
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
		)
	);
	?>
	<div class="afectivalab-field">
		<label class="afectivalab-field__label" for="afectivalab_curso"><?php esc_html_e( 'Curso al que pertenece', 'afectivalab' ); ?></label>
		<select id="afectivalab_curso" name="afectivalab_curso">
			<option value="0"><?php esc_html_e( '— Sin asignar —', 'afectivalab' ); ?></option>
			<?php foreach ( $cursos as $curso ) : ?>
				<option value="<?php echo esc_attr( $curso->ID ); ?>" <?php selected( $curso_id, $curso->ID ); ?>>
					<?php echo esc_html( $curso->post_title ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<p class="description"><?php esc_html_e( 'El orden dentro del curso se define en el campo "Orden" del panel Atributos.', 'afectivalab' ); ?></p>
	</div>

	<div class="afectivalab-field">
		<label class="afectivalab-field__label" for="afectivalab_duracion"><?php esc_html_e( 'Duración en minutos', 'afectivalab' ); ?></label>
		<input type="number" id="afectivalab_duracion" name="afectivalab_duracion" value="<?php echo esc_attr( $duracion ? $duracion : '' ); ?>" min="1" max="120" step="1" class="small-text">
		<p class="description"><?php esc_html_e( 'Una microclase completa dura entre 10 y 15 minutos.', 'afectivalab' ); ?></p>
	</div>

	<div class="afectivalab-field">
		<span class="afectivalab-field__label"><?php esc_html_e( 'Video principal', 'afectivalab' ); ?></span>

		<label class="afectivalab-field__radio">
			<input type="radio" name="afectivalab_video_tipo" value="ninguno" <?php checked( $video_tipo, 'ninguno' ); ?>>
			<?php esc_html_e( 'Esta clase no lleva video', 'afectivalab' ); ?>
		</label>

		<label class="afectivalab-field__radio">
			<input type="radio" name="afectivalab_video_tipo" value="url" <?php checked( $video_tipo, 'url' ); ?>>
			<?php esc_html_e( 'Enlace de YouTube o Vimeo', 'afectivalab' ); ?>
		</label>

		<div class="afectivalab-field__sub" data-video-panel="url">
			<input type="url" name="afectivalab_video_url" value="<?php echo esc_attr( $video_url ); ?>" class="widefat" placeholder="https://www.youtube.com/watch?v=...">
		</div>

		<label class="afectivalab-field__radio">
			<input type="radio" name="afectivalab_video_tipo" value="media" <?php checked( $video_tipo, 'media' ); ?>>
			<?php esc_html_e( 'Archivo de video subido a la web', 'afectivalab' ); ?>
		</label>

		<div class="afectivalab-field__sub" data-video-panel="media">
			<input type="hidden" id="afectivalab_video_id" name="afectivalab_video_id" value="<?php echo esc_attr( $video_id ? $video_id : '' ); ?>">
			<button type="button" class="button" data-video-select><?php esc_html_e( 'Elegir o subir video', 'afectivalab' ); ?></button>
			<button type="button" class="button-link" data-video-clear><?php esc_html_e( 'Quitar', 'afectivalab' ); ?></button>
			<span class="afectivalab-field__filename" data-video-name><?php echo esc_html( $video_id ? get_the_title( $video_id ) : '' ); ?></span>
		</div>
	</div>
	<?php
}

/**
 * Guardado de los campos propios. Una sola función para los dos tipos: cada
 * bloque se activa solo si venía su nonce, así que un guardado de curso nunca
 * toca los campos de clase ni al revés.
 */
function afectivalab_guardar_contenido( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['afectivalab_curso_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['afectivalab_curso_nonce'] ), 'afectivalab_guardar_curso' ) ) {
		update_post_meta(
			$post_id,
			'_afectivalab_habilidad',
			sanitize_text_field( wp_unslash( $_POST['afectivalab_habilidad'] ?? '' ) )
		);
	}

	if ( isset( $_POST['afectivalab_clase_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['afectivalab_clase_nonce'] ), 'afectivalab_guardar_clase' ) ) {
		update_post_meta( $post_id, '_afectivalab_curso', absint( $_POST['afectivalab_curso'] ?? 0 ) );
		update_post_meta( $post_id, '_afectivalab_duracion', absint( $_POST['afectivalab_duracion'] ?? 0 ) );

		$tipo = sanitize_key( $_POST['afectivalab_video_tipo'] ?? 'ninguno' );
		if ( ! in_array( $tipo, array( 'ninguno', 'url', 'media' ), true ) ) {
			$tipo = 'ninguno';
		}
		update_post_meta( $post_id, '_afectivalab_video_tipo', $tipo );

		update_post_meta(
			$post_id,
			'_afectivalab_video_url',
			esc_url_raw( wp_unslash( $_POST['afectivalab_video_url'] ?? '' ) )
		);
		update_post_meta( $post_id, '_afectivalab_video_id', absint( $_POST['afectivalab_video_id'] ?? 0 ) );
	}
}
add_action( 'save_post', 'afectivalab_guardar_contenido' );

function afectivalab_content_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || ! in_array( $screen->post_type, array( AFECTIVALAB_CPT_CURSO, AFECTIVALAB_CPT_CLASE ), true ) ) {
		return;
	}

	// El selector de archivo de video usa la propia librería de medios de
	// WordPress, así el instructor sube el video igual que cualquier otro
	// archivo del sitio.
	wp_enqueue_media();

	wp_enqueue_style( 'afectivalab-admin', get_theme_file_uri( 'assets/css/admin.css' ), array(), afectivalab_asset_version( 'assets/css/admin.css' ) );
	wp_enqueue_script( 'afectivalab-admin', get_theme_file_uri( 'assets/js/admin.js' ), array(), afectivalab_asset_version( 'assets/js/admin.js' ), true );
}
add_action( 'admin_enqueue_scripts', 'afectivalab_content_admin_assets' );

/**
 * Columnas del listado de microclases: sin el curso a la vista, una lista de
 * decenas de clases con títulos parecidos entre rutas es imposible de manejar.
 */
function afectivalab_clase_columns( $columns ) {
	$nuevas = array();

	foreach ( $columns as $key => $label ) {
		$nuevas[ $key ] = $label;

		if ( 'title' === $key ) {
			$nuevas['afectivalab_curso']  = __( 'Curso', 'afectivalab' );
			$nuevas['afectivalab_orden']  = __( 'Orden', 'afectivalab' );
			$nuevas['afectivalab_video']  = __( 'Video', 'afectivalab' );
		}
	}

	return $nuevas;
}
add_filter( 'manage_' . AFECTIVALAB_CPT_CLASE . '_posts_columns', 'afectivalab_clase_columns' );

function afectivalab_clase_column_content( $column, $post_id ) {
	if ( 'afectivalab_curso' === $column ) {
		$curso_id = (int) get_post_meta( $post_id, '_afectivalab_curso', true );
		echo $curso_id ? esc_html( get_the_title( $curso_id ) ) : '<span aria-hidden="true">—</span>';
	}

	if ( 'afectivalab_orden' === $column ) {
		echo esc_html( get_post_field( 'menu_order', $post_id ) );
	}

	if ( 'afectivalab_video' === $column ) {
		$tipo = get_post_meta( $post_id, '_afectivalab_video_tipo', true );

		$etiquetas = array(
			'url'   => __( 'Enlace', 'afectivalab' ),
			'media' => __( 'Archivo', 'afectivalab' ),
		);

		echo isset( $etiquetas[ $tipo ] ) ? esc_html( $etiquetas[ $tipo ] ) : '<span aria-hidden="true">—</span>';
	}
}
add_action( 'manage_' . AFECTIVALAB_CPT_CLASE . '_posts_custom_column', 'afectivalab_clase_column_content', 10, 2 );

function afectivalab_curso_columns( $columns ) {
	$nuevas = array();

	foreach ( $columns as $key => $label ) {
		$nuevas[ $key ] = $label;

		if ( 'title' === $key ) {
			$nuevas['afectivalab_clases'] = __( 'Microclases', 'afectivalab' );
		}
	}

	return $nuevas;
}
add_filter( 'manage_' . AFECTIVALAB_CPT_CURSO . '_posts_columns', 'afectivalab_curso_columns' );

function afectivalab_curso_column_content( $column, $post_id ) {
	if ( 'afectivalab_clases' === $column ) {
		echo esc_html( count( afectivalab_clases_del_curso( $post_id, array( 'publish', 'draft', 'pending', 'private' ) ) ) );
	}
}
add_action( 'manage_' . AFECTIVALAB_CPT_CURSO . '_posts_custom_column', 'afectivalab_curso_column_content', 10, 2 );
