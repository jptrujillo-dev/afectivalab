<?php
/**
 * Contenido de prueba, para poder ver el panel y el camino funcionando antes
 * de que el equipo de contenido cargue los cursos de verdad.
 *
 * Se crea y se borra desde un botón en el escritorio (Cursos > Contenido de
 * prueba), no al activar el theme ni al cargar una página: nada que escriba
 * en la base de datos debe pasar sin que alguien lo pida.
 *
 * Todo lo que crea queda marcado con la meta `_afectivalab_demo`, que es
 * justamente lo que permite borrarlo después sin tocar nada que haya escrito
 * una persona. **Si algún día se escribe contenido real encima de un curso de
 * prueba, hay que quitarle esa marca a mano**, o el botón de borrar se lo
 * llevará.
 *
 * Los textos son deliberadamente de relleno y lo dicen. No son orientación
 * psicológica: eso lo escribe el equipo del cliente, y no queremos que un
 * texto inventado aquí termine leyéndose como si lo fuera.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AFECTIVALAB_DEMO_META = '_afectivalab_demo';

/**
 * Los cursos de prueba. Están repartidos en tres etapas a propósito: así se
 * puede cambiar la edad de un hijo y ver que la ruta cambia sola. Y dentro de
 * "niñez inicial" hay cuatro ejes distintos, que es lo que deja comprobar que
 * las preocupaciones marcadas por el padre suben al principio de la ruta.
 */
function afectivalab_demo_cursos() {
	return array(
		array(
			'titulo'    => 'Autoestima: que se sienta capaz',
			'etapa'     => 'ninez-inicial',
			'eje'       => 'crecer-seguro',
			'habilidad' => 'Refuerzo de la autoestima',
			'resumen'   => 'Cómo acompañar a tu hijo para que confíe en lo que puede hacer, sin exigirle de más.',
			'clases'    => array(
				array( 'Qué es la autoestima a esta edad', 11 ),
				array( 'Elogiar el esfuerzo, no el resultado', 9 ),
				array( 'Cuando se frustra y quiere rendirse', 12 ),
				array( 'Comparaciones con hermanos y compañeros', 10 ),
				array( 'Misión en familia: tres cosas que hiciste bien', 8 ),
			),
		),
		array(
			'titulo'    => 'Bullying: prevenir y actuar',
			'etapa'     => 'ninez-inicial',
			'eje'       => 'proteger',
			'habilidad' => 'Prevención y manejo del bullying',
			'resumen'   => 'Cómo detectar a tiempo una situación de bullying y qué hacer si ya está pasando.',
			'clases'    => array(
				array( 'Qué es y qué no es bullying', 12 ),
				array( 'Señales de alerta en casa', 10 ),
				array( 'Cómo abrir la conversación', 11 ),
				array( 'Hablar con el colegio: qué pedir', 13 ),
				array( 'Si tu hijo es quien molesta', 12 ),
				array( 'Acompañar después de que pasó', 9 ),
			),
		),
		array(
			'titulo'    => 'Hablar de todo sin que se cierre',
			'etapa'     => 'ninez-inicial',
			'eje'       => 'conectar',
			'habilidad' => 'Comunicación con tu hijo',
			'resumen'   => 'Cómo preguntar para que te cuente, y qué hacer cuando no quiere hablar.',
			'clases'    => array(
				array( 'Por qué responde "nada" y "bien"', 10 ),
				array( 'Preguntas que sí abren conversación', 9 ),
				array( 'Escuchar sin corregir de inmediato', 11 ),
				array( 'El momento del día que mejor funciona', 8 ),
			),
		),
		array(
			'titulo'    => 'Pantallas y videojuegos en casa',
			'etapa'     => 'ninez-inicial',
			'eje'       => 'mundo-digital',
			'habilidad' => 'Acuerdos digitales en casa',
			'resumen'   => 'Cómo poner límites de pantalla que se sostengan, sin pelear todos los días.',
			'clases'    => array(
				array( 'Cuánto es demasiado a esta edad', 10 ),
				array( 'Acuerdos que se pueden cumplir', 12 ),
				array( 'La rabieta cuando se apaga', 9 ),
				array( 'Qué mira y con quién juega', 11 ),
				array( 'Misión en familia: una tarde sin pantallas', 7 ),
			),
		),
		array(
			'titulo'    => 'Cyberbullying y redes sociales',
			'etapa'     => 'ninez-media',
			'eje'       => 'mundo-digital',
			'habilidad' => 'Seguridad digital',
			'resumen'   => 'La primera vez que tiene celular propio: qué acordar y qué vigilar.',
			'clases'    => array(
				array( 'El primer celular: cuándo y con qué reglas', 12 ),
				array( 'Qué es el cyberbullying', 11 ),
				array( 'Privacidad: qué se publica y qué no', 10 ),
				array( 'Si alguien desconocido le escribe', 13 ),
			),
		),
		array(
			'titulo'    => 'Pubertad: los cambios que vienen',
			'etapa'     => 'ninez-media',
			'eje'       => 'sexualidad-afectividad',
			'habilidad' => 'Acompañamiento en la pubertad',
			'resumen'   => 'Cómo hablar de los cambios del cuerpo antes de que lleguen.',
			'clases'    => array(
				array( 'Por qué conviene hablarlo antes', 10 ),
				array( 'Los cambios del cuerpo, con nombre propio', 12 ),
				array( 'Pudor y privacidad en casa', 9 ),
				array( 'Preguntas incómodas: cómo responder', 11 ),
			),
		),
		array(
			'titulo'    => 'Rabietas y primeras emociones',
			'etapa'     => 'primera-infancia',
			'eje'       => 'emociones',
			'habilidad' => 'Manejo de rabietas',
			'resumen'   => 'Qué pasa en la cabeza de un niño de 3 años cuando estalla, y qué ayuda.',
			'clases'    => array(
				array( 'Por qué a esta edad estallan', 10 ),
				array( 'Qué hacer durante la rabieta', 9 ),
				array( 'Qué hacer después, cuando se calma', 8 ),
				array( 'Poner un límite sin gritar', 11 ),
			),
		),
	);
}

/**
 * Asigna un término ya sembrado, buscándolo por slug. Si no existe no hace
 * nada: es preferible un curso sin etapa (que se ve y se puede arreglar a
 * mano) a un término inventado que duplique una de las cinco etapas.
 *
 * @param int    $post_id
 * @param string $slug
 * @param string $taxonomia
 */
function afectivalab_demo_asignar_termino( $post_id, $slug, $taxonomia ) {
	$term = get_term_by( 'slug', $slug, $taxonomia );

	if ( $term ) {
		wp_set_object_terms( $post_id, array( (int) $term->term_id ), $taxonomia );
	}
}

/**
 * Crea los cursos y microclases de prueba.
 *
 * @return array{cursos: int, clases: int}
 */
function afectivalab_demo_crear() {
	$autor  = get_current_user_id();
	$cursos = 0;
	$clases = 0;

	foreach ( afectivalab_demo_cursos() as $datos ) {
		$curso_id = wp_insert_post(
			array(
				'post_type'    => AFECTIVALAB_CPT_CURSO,
				'post_status'  => 'publish',
				'post_title'   => $datos['titulo'],
				'post_excerpt' => $datos['resumen'],
				'post_content' => '<p>' . esc_html__( 'Curso de ejemplo, creado para probar el diseño de la plataforma. Su contenido no es orientación profesional.', 'afectivalab' ) . '</p>',
				'post_author'  => $autor,
			)
		);

		if ( is_wp_error( $curso_id ) ) {
			continue;
		}

		$cursos++;

		// Por id y no por slug: wp_set_object_terms() con un texto que no
		// existe **crea** el término, y una etapa duplicada rompería la
		// matriz de rutas sin que se note.
		afectivalab_demo_asignar_termino( $curso_id, $datos['etapa'], AFECTIVALAB_TAX_ETAPA );
		afectivalab_demo_asignar_termino( $curso_id, $datos['eje'], AFECTIVALAB_TAX_EJE );

		update_post_meta( $curso_id, '_afectivalab_habilidad', $datos['habilidad'] );
		update_post_meta( $curso_id, AFECTIVALAB_DEMO_META, 1 );

		$orden = 1;

		foreach ( $datos['clases'] as $clase ) {
			list( $titulo, $minutos ) = $clase;

			$clase_id = wp_insert_post(
				array(
					'post_type'    => AFECTIVALAB_CPT_CLASE,
					'post_status'  => 'publish',
					'post_title'   => $titulo,
					'post_content' => '<p>' . esc_html__( 'Texto de ejemplo para probar el diseño. El contenido real de esta clase lo escribe el equipo de contenido.', 'afectivalab' ) . '</p>',
					'post_author'  => $autor,
					'menu_order'   => $orden,
				)
			);

			if ( is_wp_error( $clase_id ) ) {
				continue;
			}

			$clases++;

			update_post_meta( $clase_id, '_afectivalab_curso', $curso_id );
			update_post_meta( $clase_id, '_afectivalab_duracion', $minutos );
			// Sin video a propósito: no vamos a enlazar videos de terceros que
			// nadie revisó. Para probar el reproductor, basta pegar una URL de
			// YouTube o Vimeo en cualquiera de estas clases.
			update_post_meta( $clase_id, '_afectivalab_video_tipo', 'ninguno' );
			update_post_meta( $clase_id, AFECTIVALAB_DEMO_META, 1 );

			$orden++;
		}
	}

	return array(
		'cursos' => $cursos,
		'clases' => $clases,
	);
}

/**
 * Ids de todo lo que creó el botón de contenido de prueba.
 *
 * @return int[]
 */
function afectivalab_demo_ids() {
	return get_posts(
		array(
			'post_type'      => array( AFECTIVALAB_CPT_CURSO, AFECTIVALAB_CPT_CLASE ),
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => AFECTIVALAB_DEMO_META,
			'meta_value'     => 1,
		)
	);
}

/**
 * Borra el contenido de prueba, definitivamente (no a la papelera: es
 * contenido de relleno, dejarlo ahí solo estorba).
 *
 * @return int Cuántas entradas se borraron.
 */
function afectivalab_demo_borrar() {
	$borrados = 0;

	foreach ( afectivalab_demo_ids() as $id ) {
		if ( wp_delete_post( $id, true ) ) {
			$borrados++;
		}
	}

	return $borrados;
}

function afectivalab_demo_menu() {
	add_submenu_page(
		'edit.php?post_type=' . AFECTIVALAB_CPT_CURSO,
		__( 'Contenido de prueba', 'afectivalab' ),
		__( 'Contenido de prueba', 'afectivalab' ),
		'manage_afectivalab_contenido',
		'afectivalab-demo',
		'afectivalab_demo_pantalla'
	);
}
add_action( 'admin_menu', 'afectivalab_demo_menu' );

function afectivalab_demo_pantalla() {
	if ( ! current_user_can( 'manage_afectivalab_contenido' ) ) {
		wp_die( esc_html__( 'No tienes permiso para hacer esto.', 'afectivalab' ) );
	}

	$aviso = '';

	if ( isset( $_POST['afectivalab_demo_accion'] ) && check_admin_referer( 'afectivalab_demo' ) ) {
		$accion = sanitize_key( wp_unslash( $_POST['afectivalab_demo_accion'] ) );

		if ( 'crear' === $accion ) {
			$hecho = afectivalab_demo_crear();
			$aviso = sprintf(
				/* translators: 1: cantidad de cursos, 2: cantidad de microclases. */
				__( 'Listo: %1$d cursos y %2$d microclases de prueba.', 'afectivalab' ),
				$hecho['cursos'],
				$hecho['clases']
			);
		} elseif ( 'borrar' === $accion ) {
			$aviso = sprintf(
				/* translators: %d: cantidad de entradas borradas. */
				__( 'Se borraron %d entradas de prueba.', 'afectivalab' ),
				afectivalab_demo_borrar()
			);
		}
	}

	$existentes = count( afectivalab_demo_ids() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Contenido de prueba', 'afectivalab' ); ?></h1>

		<?php if ( $aviso ) : ?>
			<div class="notice notice-success"><p><?php echo esc_html( $aviso ); ?></p></div>
		<?php endif; ?>

		<p>
			<?php esc_html_e( 'Crea unos cursos y microclases de relleno para poder ver el panel, la ruta y el camino de clases funcionando antes de cargar el contenido real.', 'afectivalab' ); ?>
		</p>
		<p>
			<strong><?php esc_html_e( 'Los textos son de ejemplo y no son orientación profesional.', 'afectivalab' ); ?></strong>
			<?php esc_html_e( 'Sirven solo para probar el diseño. Bórralos antes de publicar el sitio de verdad.', 'afectivalab' ); ?>
		</p>

		<p>
			<?php
			printf(
				/* translators: %d: cantidad de entradas de prueba que ya existen. */
				esc_html__( 'Ahora mismo hay %d entradas de prueba en el sitio.', 'afectivalab' ),
				absint( $existentes )
			);
			?>
		</p>

		<form method="post">
			<?php wp_nonce_field( 'afectivalab_demo' ); ?>

			<p>
				<button type="submit" name="afectivalab_demo_accion" value="crear" class="button button-primary">
					<?php esc_html_e( 'Crear contenido de prueba', 'afectivalab' ); ?>
				</button>

				<?php if ( $existentes ) : ?>
					<button
						type="submit"
						name="afectivalab_demo_accion"
						value="borrar"
						class="button button-link-delete"
						onclick="return confirm('<?php echo esc_js( __( 'Se borrará todo el contenido de prueba. ¿Seguro?', 'afectivalab' ) ); ?>');"
					>
						<?php esc_html_e( 'Borrar contenido de prueba', 'afectivalab' ); ?>
					</button>
				<?php endif; ?>
			</p>
		</form>

		<p class="description">
			<?php esc_html_e( 'Las clases se crean sin video, para no enlazar videos de terceros. Para probar el reproductor, pega una URL de YouTube o Vimeo en cualquiera de ellas.', 'afectivalab' ); ?>
		</p>
	</div>
	<?php
}
