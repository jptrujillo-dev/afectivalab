<?php
/**
 * Home: ruta recomendada + caso práctico interactivo.
 *
 * Alimentada con contenido real cuando lo hay (ver inc/home.php): el primer
 * curso publicado de niñez inicial para la ruta, y la primera microclase con
 * un caso interactivo real para el demo. Selección automática, sin un campo
 * de "destacado" que mantener — decisión del usuario. Si todavía no hay
 * contenido real que califique (instalación nueva, sin cursos cargados),
 * cada tarjeta cae de vuelta a su ejemplo ilustrativo original.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_showcase_curso = afectivalab_showcase_curso();
$afectivalab_route          = array();
$afectivalab_route_titulo   = __( 'Ruta recomendada · 6 a 8 años', 'afectivalab' );

if ( $afectivalab_showcase_curso ) {
	$afectivalab_etapas_datos = afectivalab_etapas();
	$afectivalab_etapa_datos  = $afectivalab_etapas_datos['ninez-inicial'] ?? null;

	if ( $afectivalab_etapa_datos ) {
		$afectivalab_route_titulo = sprintf(
			/* translators: 1: edad mínima, 2: edad máxima. */
			__( 'Ruta recomendada · %1$d a %2$d años', 'afectivalab' ),
			$afectivalab_etapa_datos['edad_min'],
			$afectivalab_etapa_datos['edad_max']
		);
	}

	$afectivalab_clases_curso = array_slice( afectivalab_clases_del_curso( $afectivalab_showcase_curso->ID ), 0, 4 );

	foreach ( $afectivalab_clases_curso as $afectivalab_indice => $afectivalab_clase_showcase ) {
		$afectivalab_duracion = (int) get_post_meta( $afectivalab_clase_showcase->ID, '_afectivalab_duracion', true );

		if ( 0 === $afectivalab_indice ) {
			$afectivalab_status = 'done';
			$afectivalab_icono  = 'check';
		} elseif ( 1 === $afectivalab_indice ) {
			$afectivalab_status = 'current';
			$afectivalab_icono  = 'star';
		} else {
			$afectivalab_status = 'locked';
			$afectivalab_icono  = 'lock';
		}

		$afectivalab_route[] = array(
			'status' => $afectivalab_status,
			'icon'   => $afectivalab_icono,
			'title'  => $afectivalab_clase_showcase->post_title,
			'meta'   => sprintf(
				/* translators: 1: número de lección, 2: duración en minutos. */
				__( 'Lección %1$d · %2$d min', 'afectivalab' ),
				$afectivalab_indice + 1,
				$afectivalab_duracion
			),
		);
	}
}

// Sin curso real todavía, o sin ninguna microclase cargada en él: se cae al
// ejemplo ilustrativo original en vez de mostrar la tarjeta vacía.
if ( ! $afectivalab_route ) {
	$afectivalab_route_titulo = __( 'Ruta recomendada · 8 años', 'afectivalab' );
	$afectivalab_route        = array(
		array(
			'status' => 'done',
			'icon'   => 'check',
			'title'  => __( 'Comunicación y confianza', 'afectivalab' ),
			'meta'   => __( 'Lección 1 · 10 min', 'afectivalab' ),
		),
		array(
			'status' => 'current',
			'icon'   => 'star',
			'title'  => __( 'Autoestima', 'afectivalab' ),
			'meta'   => __( 'Lección 2 · 12 min', 'afectivalab' ),
		),
		array(
			'status' => 'locked',
			'icon'   => 'lock',
			'title'  => __( 'Bullying: prevenir y actuar', 'afectivalab' ),
			'meta'   => __( 'Lección 3 · 15 min', 'afectivalab' ),
		),
		array(
			'status' => 'locked',
			'icon'   => 'lock',
			'title'  => __( 'Educación sexual 8–9 años', 'afectivalab' ),
			'meta'   => __( 'Lección 4 · 12 min', 'afectivalab' ),
		),
	);
}

$afectivalab_showcase_caso = afectivalab_showcase_caso();
$afectivalab_caso_opciones = array();
$afectivalab_caso_situacion = '';

if ( $afectivalab_showcase_caso ) {
	$afectivalab_caso_situacion = $afectivalab_showcase_caso['caso']['paso1']['situacion'];

	foreach ( $afectivalab_showcase_caso['caso']['paso1']['opciones'] as $afectivalab_opcion ) {
		$afectivalab_caso_opciones[] = array(
			'texto'    => $afectivalab_opcion['texto'],
			'correcta' => $afectivalab_opcion['recomendada'],
			// El modelo real guarda una sola explicación por opción (no un
			// título separado); el título es solo una etiqueta de UI que se
			// arma acá según si es la recomendada o no, no contenido nuevo.
			'titulo'   => $afectivalab_opcion['recomendada']
				? __( 'Buena elección.', 'afectivalab' )
				: __( 'Otra opción posible.', 'afectivalab' ),
			'texto_feedback' => $afectivalab_opcion['retroalimentacion'],
		);
	}
}

// Sin ninguna microclase con caso real todavía: se cae al dilema ilustrativo
// original.
if ( ! $afectivalab_caso_opciones ) {
	$afectivalab_caso_situacion = __( 'Tu hijo de 8 años dice: "No quiero volver al colegio mañana." ¿Qué harías primero?', 'afectivalab' );
	$afectivalab_caso_opciones  = array(
		array(
			'texto'          => __( 'Le digo que tiene que ir, todos tenemos problemas.', 'afectivalab' ),
			'correcta'       => false,
			'titulo'         => __( 'Puede generar más resistencia.', 'afectivalab' ),
			'texto_feedback' => __( 'Restarle importancia a lo que siente puede hacer que la próxima vez prefiera no contarte nada.', 'afectivalab' ),
		),
		array(
			'texto'          => __( 'Le pregunto con calma qué pasó.', 'afectivalab' ),
			'correcta'       => true,
			'titulo'         => __( 'Buena elección.', 'afectivalab' ),
			'texto_feedback' => __( 'Primero conviene generar un espacio seguro para que el niño hable. Si presionas demasiado, podría cerrarse.', 'afectivalab' ),
		),
		array(
			'texto'          => __( 'Llamo de inmediato al colegio.', 'afectivalab' ),
			'correcta'       => false,
			'titulo'         => __( 'Todavía es pronto para eso.', 'afectivalab' ),
			'texto_feedback' => __( 'Conviene entender primero qué está pasando con tu hijo antes de involucrar al colegio.', 'afectivalab' ),
		),
		array(
			'texto'          => __( 'Le permito quedarse en casa.', 'afectivalab' ),
			'correcta'       => false,
			'titulo'         => __( 'Evita el tema en vez de resolverlo.', 'afectivalab' ),
			'texto_feedback' => __( 'Puede ser un alivio momentáneo, pero no ayuda a entender ni resolver lo que le está pasando.', 'afectivalab' ),
		),
	);
}
?>
<section class="showcase" id="casos-practicos">
	<div class="container showcase__grid reveal-stagger">
		<div class="showcase-card">
			<div class="showcase-card__head">
				<h3><?php echo esc_html( $afectivalab_route_titulo ); ?></h3>
				<p><?php esc_html_e( 'Ayuda a tu hijo a desarrollar habilidades para una vida más segura y feliz.', 'afectivalab' ); ?></p>
			</div>

			<div class="route-list">
				<?php foreach ( $afectivalab_route as $item ) : ?>
					<div class="route-list__item is-<?php echo esc_attr( $item['status'] ); ?>">
						<span class="status-icon"><?php afectivalab_icon( $item['icon'] ); ?></span>
						<div>
							<strong><?php echo esc_html( $item['title'] ); ?></strong>
							<span><?php echo esc_html( $item['meta'] ); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/registro' ) ); ?>">
				<?php esc_html_e( 'Iniciar ruta', 'afectivalab' ); ?>
				<?php afectivalab_icon( 'arrow-right' ); ?>
			</a>
		</div>

		<div class="showcase-card">
			<div class="showcase-card__head">
				<h3><?php esc_html_e( 'Casos prácticos', 'afectivalab' ); ?></h3>
				<p><?php esc_html_e( 'Situaciones reales, con explicación después de cada decisión.', 'afectivalab' ); ?></p>
			</div>

			<div class="case-demo" data-case-demo>
				<div class="case-demo__intro">
					<img
						class="case-demo__portrait"
						src="<?php echo esc_url( get_theme_file_uri( 'assets/images/caso-practico-nino.webp' ) ); ?>"
						alt=""
						width="160"
						height="160"
						loading="lazy"
					>
					<div class="case-demo__prompt">
						<strong><?php echo esc_html( $afectivalab_caso_situacion ); ?></strong>
					</div>
				</div>

				<div class="case-demo__options">
					<?php foreach ( $afectivalab_caso_opciones as $afectivalab_indice_opcion => $afectivalab_opcion ) : ?>
						<button
							type="button"
							class="case-option"
							data-correct="<?php echo $afectivalab_opcion['correcta'] ? 'true' : 'false'; ?>"
							data-feedback-title="<?php echo esc_attr( $afectivalab_opcion['titulo'] ); ?>"
							data-feedback-text="<?php echo esc_attr( $afectivalab_opcion['texto_feedback'] ); ?>"
						>
							<span class="case-option__letter"><?php echo esc_html( chr( 65 + $afectivalab_indice_opcion ) ); ?></span>
							<span><?php echo esc_html( $afectivalab_opcion['texto'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="case-demo__feedback">
					<span class="status-icon"><?php afectivalab_icon( 'check' ); ?></span>
					<div>
						<strong class="case-demo__feedback-title"></strong>
						<p class="case-demo__feedback-text"></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
