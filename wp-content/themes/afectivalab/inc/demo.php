<?php
/**
 * Contenido y cuentas de prueba, para poder recorrer el sitio completo como
 * lo haría una familia real antes de que el equipo cargue su contenido.
 *
 * Se crea y se borra desde un botón en el escritorio (Cursos > Contenido de
 * prueba), no al activar el theme ni al cargar una página: nada que escriba
 * en la base de datos debe pasar sin que alguien lo pida.
 *
 * Qué crea:
 * - 13 cursos que cubren las 5 etapas y los 8 ejes, con descripción, foto
 *   destacada (Unsplash) y 4 o 5 microclases cada uno, con texto completo,
 *   videos de YouTube, misiones y casos prácticos (ver inc/demo-contenido.php).
 * - Dos cuentas de padre/madre: una "familia en marcha" con suscripción
 *   activa, tres hijos de distintas edades y avance real (un curso terminado
 *   con certificado, monedas, clases a medias), y una "familia nueva" sin
 *   suscripción ni hijos, para probar el recorrido desde cero.
 *
 * Todo lo que crea queda marcado con la meta `_afectivalab_demo` (en posts,
 * adjuntos y usuarios), que es justamente lo que permite borrarlo después
 * sin tocar nada que haya escrito una persona. **Si algún día se escribe
 * contenido real encima de un curso de prueba, hay que quitarle esa marca a
 * mano**, o el botón de borrar se lo llevará.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_theme_file_path( 'inc/demo-contenido.php' );

const AFECTIVALAB_DEMO_META = '_afectivalab_demo';

/** Opción donde se guardan los datos de acceso de las cuentas de prueba. */
const AFECTIVALAB_DEMO_OPCION_USUARIOS = 'afectivalab_demo_usuarios';

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
 * Lista HTML simple.
 *
 * @param string[] $items
 * @return string
 */
function afectivalab_demo_lista( $items ) {
	return '<ul><li>' . implode( '</li><li>', array_map( 'esc_html', $items ) ) . '</li></ul>';
}

/**
 * El texto largo de un curso ("Sobre este curso").
 *
 * @param array $datos
 * @return string
 */
function afectivalab_demo_html_curso( $datos ) {
	$html = '';

	foreach ( $datos['descripcion'] as $parrafo ) {
		$html .= '<p>' . esc_html( $parrafo ) . '</p>';
	}

	$html .= '<h3>' . esc_html__( 'Lo que vas a aprender', 'afectivalab' ) . '</h3>';
	$html .= afectivalab_demo_lista( $datos['aprenderas'] );

	$html .= '<h3>' . esc_html__( 'Cómo funciona', 'afectivalab' ) . '</h3>';
	$html .= '<p>' . esc_html(
		sprintf(
			/* translators: %d: cantidad de microclases. */
			__( 'Son %d microclases cortas que se abren una tras otra. Cada una trae ideas clave, frases para usar en casa y un ejercicio para la semana; algunas incluyen un video, un caso práctico o una misión para hacer en familia. Al terminar el curso tu hijo obtiene su insignia y su certificado.', 'afectivalab' ),
			count( $datos['clases'] )
		)
	) . '</p>';

	return $html;
}

/**
 * El contenido de una microclase, armado con las mismas secciones siempre
 * para que todas las clases se lean igual.
 *
 * @param array $clase
 * @return string
 */
function afectivalab_demo_html_clase( $clase ) {
	$html = '';

	foreach ( $clase['intro'] ?? array() as $parrafo ) {
		$html .= '<p>' . esc_html( $parrafo ) . '</p>';
	}

	if ( ! empty( $clase['claves'] ) ) {
		$html .= '<h3>' . esc_html__( 'Ideas clave', 'afectivalab' ) . '</h3>' . afectivalab_demo_lista( $clase['claves'] );
	}

	if ( ! empty( $clase['frases'] ) ) {
		$html .= '<h3>' . esc_html__( 'Frases que ayudan', 'afectivalab' ) . '</h3>' . afectivalab_demo_lista( $clase['frases'] );
	}

	if ( ! empty( $clase['evitar'] ) ) {
		$html .= '<h3>' . esc_html__( 'Mejor evitar', 'afectivalab' ) . '</h3>' . afectivalab_demo_lista( $clase['evitar'] );
	}

	if ( ! empty( $clase['practica'] ) ) {
		$html .= '<h3>' . esc_html__( 'Para practicar esta semana', 'afectivalab' ) . '</h3><p>' . esc_html( $clase['practica'] ) . '</p>';
	}

	return $html;
}

/**
 * Descarga una foto de Unsplash y la deja en la biblioteca de medios como
 * imagen destacada del post. Si el servidor no tiene salida a internet o la
 * descarga falla, el curso queda igual, solo que sin foto.
 *
 * No usa media_sideload_image(): esa función exige que la URL termine en
 * .jpg/.png, y las de Unsplash llevan el formato como parámetro.
 *
 * @param string $foto_id Id de la foto en Unsplash ("1517545084371-4a575dde2a02").
 * @param int    $post_id
 * @param string $titulo
 * @return int Id del adjunto, o 0 si no se pudo.
 */
function afectivalab_demo_imagen( $foto_id, $post_id, $titulo ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$url = 'https://images.unsplash.com/photo-' . rawurlencode( $foto_id ) . '?w=1400&h=788&fit=crop&q=80&fm=jpg';
	$tmp = download_url( $url, 30 );

	if ( is_wp_error( $tmp ) ) {
		return 0;
	}

	$archivo = array(
		'name'     => sanitize_title( $titulo ) . '.jpg',
		'tmp_name' => $tmp,
	);

	$adjunto_id = media_handle_sideload( $archivo, $post_id, $titulo );

	if ( is_wp_error( $adjunto_id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}

	update_post_meta( $adjunto_id, AFECTIVALAB_DEMO_META, 1 );
	update_post_meta( $adjunto_id, '_wp_attachment_image_alt', $titulo );

	return (int) $adjunto_id;
}

/**
 * Guarda un caso práctico en una microclase, con las mismas claves de meta
 * que lee la parte real (ver inc/casos.php e inc/panel-admin.php). El paso 2
 * es opcional.
 *
 * @param int   $clase_id
 * @param array $caso Uno de afectivalab_demo_casos().
 */
function afectivalab_demo_guardar_caso( $clase_id, $caso ) {
	foreach ( array( 'paso1', 'paso2' ) as $paso ) {
		if ( empty( $caso[ $paso ] ) ) {
			continue;
		}

		update_post_meta( $clase_id, "_afectivalab_caso_{$paso}_situacion", $caso[ $paso ]['situacion'] );

		foreach ( $caso[ $paso ]['opciones'] as $i => $opcion ) {
			list( $texto, $feedback, $recomendada ) = $opcion;

			$n = $i + 1;
			update_post_meta( $clase_id, "_afectivalab_caso_{$paso}_opcion_{$n}_texto", $texto );
			update_post_meta( $clase_id, "_afectivalab_caso_{$paso}_opcion_{$n}_feedback", $feedback );
			update_post_meta( $clase_id, "_afectivalab_caso_{$paso}_opcion_{$n}_recomendada", $recomendada ? 1 : '' );
		}
	}
}

/**
 * Crea los cursos y microclases de prueba.
 *
 * @return array{cursos: int, clases: int, fotos: int, mapa: array<string, int[]>}
 *         `mapa` es título del curso => ids de sus clases en orden, para
 *         armar el avance de las cuentas de prueba.
 */
function afectivalab_demo_crear_contenido() {
	$autor  = get_current_user_id();
	$casos  = afectivalab_demo_casos();
	$result = array(
		'cursos' => 0,
		'clases' => 0,
		'fotos'  => 0,
		'mapa'   => array(),
	);

	foreach ( afectivalab_demo_cursos() as $datos ) {
		$curso_id = wp_insert_post(
			array(
				'post_type'    => AFECTIVALAB_CPT_CURSO,
				'post_status'  => 'publish',
				'post_title'   => $datos['titulo'],
				'post_excerpt' => $datos['resumen'],
				'post_content' => afectivalab_demo_html_curso( $datos ),
				'post_author'  => $autor,
			),
			true
		);

		if ( is_wp_error( $curso_id ) ) {
			continue;
		}

		$result['cursos']++;
		$result['mapa'][ $datos['titulo'] ] = array();

		// Por id y no por slug: wp_set_object_terms() con un texto que no
		// existe **crea** el término, y una etapa duplicada rompería la
		// matriz de rutas sin que se note.
		afectivalab_demo_asignar_termino( $curso_id, $datos['etapa'], AFECTIVALAB_TAX_ETAPA );
		afectivalab_demo_asignar_termino( $curso_id, $datos['eje'], AFECTIVALAB_TAX_EJE );

		update_post_meta( $curso_id, '_afectivalab_habilidad', $datos['habilidad'] );
		update_post_meta( $curso_id, AFECTIVALAB_DEMO_META, 1 );

		$foto_id = ! empty( $datos['imagen'] ) ? afectivalab_demo_imagen( $datos['imagen'], $curso_id, $datos['titulo'] ) : 0;

		if ( $foto_id ) {
			set_post_thumbnail( $curso_id, $foto_id );
			$result['fotos']++;
		}

		$orden = 1;

		foreach ( $datos['clases'] as $clase ) {
			$clase_id = wp_insert_post(
				array(
					'post_type'    => AFECTIVALAB_CPT_CLASE,
					'post_status'  => 'publish',
					'post_title'   => $clase['t'],
					'post_excerpt' => $clase['resumen'] ?? '',
					'post_content' => afectivalab_demo_html_clase( $clase ),
					'post_author'  => $autor,
					'menu_order'   => $orden,
				),
				true
			);

			if ( is_wp_error( $clase_id ) ) {
				continue;
			}

			$result['clases']++;
			$result['mapa'][ $datos['titulo'] ][] = (int) $clase_id;

			update_post_meta( $clase_id, '_afectivalab_curso', $curso_id );
			update_post_meta( $clase_id, '_afectivalab_duracion', (int) $clase['min'] );

			if ( ! empty( $clase['video'] ) ) {
				update_post_meta( $clase_id, '_afectivalab_video_tipo', 'url' );
				update_post_meta( $clase_id, '_afectivalab_video_url', 'https://www.youtube.com/watch?v=' . $clase['video'] );
			} else {
				update_post_meta( $clase_id, '_afectivalab_video_tipo', 'ninguno' );
			}

			// La misma foto del curso: en las clases sin video es la que
			// abre la página (ver single-afectivalab_clase.php).
			if ( $foto_id ) {
				set_post_thumbnail( $clase_id, $foto_id );
			}

			if ( ! empty( $clase['mision'] ) ) {
				update_post_meta( $clase_id, '_afectivalab_mision_tipo', $clase['mision'][0] );
				update_post_meta( $clase_id, '_afectivalab_mision_texto', $clase['mision'][1] );
			}

			if ( ! empty( $clase['caso'] ) && isset( $casos[ $clase['caso'] ] ) ) {
				afectivalab_demo_guardar_caso( $clase_id, $casos[ $clase['caso'] ] );
			}

			update_post_meta( $clase_id, AFECTIVALAB_DEMO_META, 1 );

			$orden++;
		}
	}

	return $result;
}

/**
 * Correo para una cuenta de prueba, con el dominio del sitio (o example.com
 * si el sitio corre en un "localhost" sin punto, que WordPress no acepta
 * como dominio de correo).
 *
 * @param string $usuario
 * @return string
 */
function afectivalab_demo_correo( $usuario ) {
	$host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
	$host = preg_replace( '/^www\./', '', $host );

	if ( false === strpos( $host, '.' ) ) {
		$host = 'example.com';
	}

	return $usuario . '@' . $host;
}

/**
 * Crea una cuenta de padre/madre de prueba.
 *
 * @return array{id: int, login: string, email: string, password: string}|null
 */
function afectivalab_demo_crear_padre( $login, $nombre, $apellido ) {
	if ( username_exists( $login ) ) {
		return null;
	}

	$password = wp_generate_password( 12, false );
	$email    = afectivalab_demo_correo( $login );

	$user_id = wp_insert_user(
		array(
			'user_login'   => $login,
			'user_email'   => $email,
			'user_pass'    => $password,
			'first_name'   => $nombre,
			'last_name'    => $apellido,
			'display_name' => $nombre . ' ' . $apellido,
			'role'         => 'afectivalab_padre',
		)
	);

	if ( is_wp_error( $user_id ) ) {
		return null;
	}

	update_user_meta( $user_id, AFECTIVALAB_DEMO_META, 1 );

	return array(
		'id'       => (int) $user_id,
		'login'    => $login,
		'email'    => $email,
		'password' => $password,
	);
}

/**
 * Crea un perfil de hijo para una cuenta de prueba. La edad se calcula
 * desde el año actual, para que la cuenta siga sirviendo con el tiempo.
 *
 * @return int
 */
function afectivalab_demo_crear_hijo( $user_id, $nombre, $edad, $mes, $preocupaciones ) {
	$hijo_id = wp_insert_post(
		array(
			'post_type'   => AFECTIVALAB_CPT_HIJO,
			'post_status' => 'publish',
			'post_title'  => $nombre,
			'post_author' => $user_id,
		)
	);

	if ( is_wp_error( $hijo_id ) || ! $hijo_id ) {
		return 0;
	}

	update_post_meta( $hijo_id, '_afectivalab_nacimiento_mes', $mes );
	update_post_meta( $hijo_id, '_afectivalab_nacimiento_anio', (int) current_time( 'Y' ) - $edad );
	update_post_meta( $hijo_id, '_afectivalab_preocupaciones', $preocupaciones );
	update_post_meta( $hijo_id, AFECTIVALAB_DEMO_META, 1 );

	return (int) $hijo_id;
}

/**
 * Completa clases para un hijo con las mismas funciones que usa el sitio
 * (estrellas, monedas por misión, fecha del curso terminado), no escribiendo
 * la meta a mano: así el avance de prueba es idéntico al de una familia real.
 *
 * @param int   $hijo_id
 * @param int[] $clases
 */
function afectivalab_demo_completar( $hijo_id, $clases ) {
	foreach ( $clases as $clase_id ) {
		if ( afectivalab_clase_mision( $clase_id ) ) {
			afectivalab_completar_mision( $hijo_id, $clase_id );
		} else {
			afectivalab_marcar_clase( $hijo_id, $clase_id, true );
		}
	}
}

/**
 * Las dos cuentas de prueba.
 *
 * @param array<string, int[]> $mapa Título del curso => ids de sus clases.
 * @return array<int, array> Datos de acceso para mostrar en el escritorio.
 */
function afectivalab_demo_crear_usuarios( $mapa ) {
	$cuentas = array();

	// --- Familia en marcha: suscrita, con hijos y avance ---
	$familia = afectivalab_demo_crear_padre( 'familia.demo', 'María', 'González' );

	if ( $familia ) {
		if ( function_exists( 'pmpro_changeMembershipLevel' ) && defined( 'AFECTIVALAB_PMPRO_LEVEL_ID' ) ) {
			pmpro_changeMembershipLevel( AFECTIVALAB_PMPRO_LEVEL_ID, $familia['id'] );
		}

		$mateo = afectivalab_demo_crear_hijo( $familia['id'], 'Mateo', 8, 2, array( 'proteger', 'crecer-seguro' ) );
		$sofia = afectivalab_demo_crear_hijo( $familia['id'], 'Sofía', 4, 1, array( 'emociones' ) );
		$lucia = afectivalab_demo_crear_hijo( $familia['id'], 'Lucía', 13, 1, array( 'conectar', 'mundo-digital' ) );

		$autoestima = $mapa['Autoestima: que se sienta capaz'] ?? array();
		$bullying   = $mapa['Bullying: prevenir y actuar'] ?? array();
		$rabietas   = $mapa['Rabietas y primeras emociones'] ?? array();

		if ( $mateo ) {
			// Autoestima completo (con su misión): insignia, monedas y
			// certificado disponibles. Bullying con la primera clase vista,
			// para que la siguiente sea la del caso práctico.
			afectivalab_demo_completar( $mateo, $autoestima );
			afectivalab_demo_completar( $mateo, array_slice( $bullying, 0, 1 ) );

			$curso_autoestima = $autoestima ? (int) get_post_meta( $autoestima[0], '_afectivalab_curso', true ) : 0;

			if ( $curso_autoestima ) {
				afectivalab_curso_fecha_completado( $mateo, $curso_autoestima );
			}

			update_user_meta( $familia['id'], 'afectivalab_hijo_activo', $mateo );
		}

		if ( $sofia ) {
			afectivalab_demo_completar( $sofia, array_slice( $rabietas, 0, 1 ) );
		}

		unset( $lucia ); // Sin avance: sirve para ver una ruta recién empezada.

		$familia['descripcion'] = __( 'Suscripción activa. Tres hijos: Mateo (8 años, ya terminó "Autoestima" y tiene certificado; va en "Bullying"), Sofía (4 años, empezó "Rabietas") y Lucía (13 años, sin avance).', 'afectivalab' );
		$cuentas[]              = $familia;
	}

	// --- Familia nueva: sin suscripción y sin hijos ---
	$nueva = afectivalab_demo_crear_padre( 'nueva.demo', 'Carla', 'Ruiz' );

	if ( $nueva ) {
		$nueva['descripcion'] = __( 'Sin suscripción y sin hijos: para probar el recorrido desde cero (suscribirse, agregar hijos, primera clase).', 'afectivalab' );
		$cuentas[]            = $nueva;
	}

	return $cuentas;
}

/**
 * Ids de todo el contenido que creó el botón de prueba (cursos, clases,
 * hijos y fotos).
 *
 * @return int[]
 */
function afectivalab_demo_ids() {
	return get_posts(
		array(
			'post_type'      => array( AFECTIVALAB_CPT_CURSO, AFECTIVALAB_CPT_CLASE, AFECTIVALAB_CPT_HIJO, 'attachment' ),
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => AFECTIVALAB_DEMO_META,
			'meta_value'     => 1,
		)
	);
}

/**
 * Ids de las cuentas de prueba.
 *
 * @return int[]
 */
function afectivalab_demo_usuarios_ids() {
	return get_users(
		array(
			'meta_key'   => AFECTIVALAB_DEMO_META,
			'meta_value' => 1,
			'fields'     => 'ID',
		)
	);
}

/**
 * Borra todo lo de prueba, definitivamente (no a la papelera: es contenido
 * de relleno, dejarlo ahí solo estorba). Las cuentas se borran con sus
 * perfiles de hijo y su membresía.
 *
 * @return int Cuántas entradas y cuentas se borraron.
 */
function afectivalab_demo_borrar() {
	require_once ABSPATH . 'wp-admin/includes/user.php';

	$borrados = 0;

	foreach ( afectivalab_demo_usuarios_ids() as $user_id ) {
		if ( function_exists( 'pmpro_changeMembershipLevel' ) ) {
			pmpro_changeMembershipLevel( 0, $user_id );
		}

		// Sin reasignar: sus posts (los perfiles de hijo) se van con la cuenta.
		if ( wp_delete_user( (int) $user_id ) ) {
			$borrados++;
		}
	}

	foreach ( afectivalab_demo_ids() as $id ) {
		$ok = 'attachment' === get_post_type( $id ) ? wp_delete_attachment( $id, true ) : wp_delete_post( $id, true );

		if ( $ok ) {
			$borrados++;
		}
	}

	delete_option( AFECTIVALAB_DEMO_OPCION_USUARIOS );

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

	$aviso      = '';
	$aviso_tipo = 'success';

	if ( isset( $_POST['afectivalab_demo_accion'] ) && check_admin_referer( 'afectivalab_demo' ) ) {
		$accion = sanitize_key( wp_unslash( $_POST['afectivalab_demo_accion'] ) );

		if ( 'crear' === $accion ) {
			if ( afectivalab_demo_ids() || afectivalab_demo_usuarios_ids() ) {
				$aviso      = __( 'Ya hay contenido de prueba. Bórralo primero para volver a crearlo desde cero.', 'afectivalab' );
				$aviso_tipo = 'warning';
			} else {
				// Descargar 13 fotos puede tomar un rato.
				if ( function_exists( 'set_time_limit' ) ) {
					@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
				}

				$hecho   = afectivalab_demo_crear_contenido();
				$cuentas = afectivalab_demo_crear_usuarios( $hecho['mapa'] );

				update_option( AFECTIVALAB_DEMO_OPCION_USUARIOS, $cuentas, false );

				$aviso = sprintf(
					/* translators: 1: cursos, 2: microclases, 3: fotos, 4: cuentas. */
					__( 'Listo: %1$d cursos, %2$d microclases, %3$d fotos y %4$d cuentas de prueba.', 'afectivalab' ),
					$hecho['cursos'],
					$hecho['clases'],
					$hecho['fotos'],
					count( $cuentas )
				);

				if ( $hecho['fotos'] < $hecho['cursos'] ) {
					$aviso     .= ' ' . __( 'Algunas fotos no se pudieron descargar (el servidor necesita salida a internet); esos cursos quedaron sin imagen.', 'afectivalab' );
					$aviso_tipo = 'warning';
				}
			}
		} elseif ( 'borrar' === $accion ) {
			$aviso = sprintf(
				/* translators: %d: cantidad de elementos borrados. */
				__( 'Se borraron %d elementos de prueba (contenido, fotos y cuentas).', 'afectivalab' ),
				afectivalab_demo_borrar()
			);
		}
	}

	$existentes = count( afectivalab_demo_ids() ) + count( afectivalab_demo_usuarios_ids() );
	$cuentas    = get_option( AFECTIVALAB_DEMO_OPCION_USUARIOS, array() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Contenido de prueba', 'afectivalab' ); ?></h1>

		<?php if ( $aviso ) : ?>
			<div class="notice notice-<?php echo esc_attr( $aviso_tipo ); ?>"><p><?php echo esc_html( $aviso ); ?></p></div>
		<?php endif; ?>

		<p>
			<?php esc_html_e( 'Crea 13 cursos completos (todas las etapas y todos los temas) con fotos, videos, misiones y casos prácticos, más dos cuentas de familia para recorrer el sitio como lo haría un usuario real.', 'afectivalab' ); ?>
		</p>
		<p>
			<strong><?php esc_html_e( 'Los textos siguen pautas generales de crianza positiva, pero deben ser revisados por el equipo antes de publicarse como contenido oficial.', 'afectivalab' ); ?></strong>
			<?php esc_html_e( 'Bórralos antes de abrir el sitio al público, o quítales la marca de prueba a los que quieran conservarse.', 'afectivalab' ); ?>
		</p>

		<p>
			<?php
			printf(
				/* translators: %d: cantidad de elementos de prueba que ya existen. */
				esc_html__( 'Ahora mismo hay %d elementos de prueba en el sitio.', 'afectivalab' ),
				absint( $existentes )
			);
			?>
		</p>

		<?php if ( $cuentas && $existentes ) : ?>
			<h2><?php esc_html_e( 'Cuentas de prueba', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Entra desde /ingresar (en otra ventana privada, para no cerrar tu sesión de administrador) con el usuario o el correo:', 'afectivalab' ); ?></p>
			<table class="widefat striped" style="max-width: 980px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Usuario', 'afectivalab' ); ?></th>
						<th><?php esc_html_e( 'Correo', 'afectivalab' ); ?></th>
						<th><?php esc_html_e( 'Contraseña', 'afectivalab' ); ?></th>
						<th><?php esc_html_e( 'Qué trae', 'afectivalab' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $cuentas as $cuenta ) : ?>
						<tr>
							<td><code><?php echo esc_html( $cuenta['login'] ); ?></code></td>
							<td><code><?php echo esc_html( $cuenta['email'] ); ?></code></td>
							<td><code><?php echo esc_html( $cuenta['password'] ); ?></code></td>
							<td><?php echo esc_html( $cuenta['descripcion'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p class="description">
				<?php esc_html_e( 'Los correos son ficticios: no llegan a ninguna bandeja. Si cambias la contraseña de una cuenta de prueba desde "Mi cuenta", esta tabla ya no será correcta.', 'afectivalab' ); ?>
			</p>
		<?php endif; ?>

		<form method="post" style="margin-top: 20px;">
			<?php wp_nonce_field( 'afectivalab_demo' ); ?>

			<p>
				<?php if ( ! $existentes ) : ?>
					<button type="submit" name="afectivalab_demo_accion" value="crear" class="button button-primary button-hero">
						<?php esc_html_e( 'Crear contenido y cuentas de prueba', 'afectivalab' ); ?>
					</button>
				<?php else : ?>
					<button
						type="submit"
						name="afectivalab_demo_accion"
						value="borrar"
						class="button button-link-delete"
						onclick="return confirm('<?php echo esc_js( __( 'Se borrará todo el contenido de prueba, sus fotos y las cuentas de prueba. ¿Seguro?', 'afectivalab' ) ); ?>');"
					>
						<?php esc_html_e( 'Borrar todo lo de prueba', 'afectivalab' ); ?>
					</button>
				<?php endif; ?>
			</p>
		</form>

		<p class="description">
			<?php esc_html_e( 'Las fotos se descargan de Unsplash (licencia libre) y los videos se incrustan desde canales públicos de YouTube (UNICEF, guiainfantil, AprendemosJuntos, entre otros). Crear todo puede tardar un minuto por la descarga de las fotos.', 'afectivalab' ); ?>
		</p>
	</div>
	<?php
}
