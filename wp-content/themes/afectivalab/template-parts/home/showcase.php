<?php
/**
 * Home: ruta recomendada + caso práctico interactivo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_route = array(
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
?>
<section class="showcase" id="casos-practicos">
	<div class="container showcase__grid">
		<div class="showcase-card">
			<div class="showcase-card__head">
				<h3><?php esc_html_e( 'Ruta recomendada · 8 años', 'afectivalab' ); ?></h3>
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
						<strong><?php esc_html_e( 'Tu hijo de 8 años dice: "No quiero volver al colegio mañana." ¿Qué harías primero?', 'afectivalab' ); ?></strong>
					</div>
				</div>

				<div class="case-demo__options">
					<button
						type="button"
						class="case-option"
						data-correct="false"
						data-feedback-title="<?php esc_attr_e( 'Puede generar más resistencia.', 'afectivalab' ); ?>"
						data-feedback-text="<?php esc_attr_e( 'Restarle importancia a lo que siente puede hacer que la próxima vez prefiera no contarte nada.', 'afectivalab' ); ?>"
					>
						<span class="case-option__letter">A</span>
						<span><?php esc_html_e( 'Le digo que tiene que ir, todos tenemos problemas.', 'afectivalab' ); ?></span>
					</button>
					<button
						type="button"
						class="case-option"
						data-correct="true"
						data-feedback-title="<?php esc_attr_e( 'Buena elección.', 'afectivalab' ); ?>"
						data-feedback-text="<?php esc_attr_e( 'Primero conviene generar un espacio seguro para que el niño hable. Si presionas demasiado, podría cerrarse.', 'afectivalab' ); ?>"
					>
						<span class="case-option__letter">B</span>
						<span><?php esc_html_e( 'Le pregunto con calma qué pasó.', 'afectivalab' ); ?></span>
					</button>
					<button
						type="button"
						class="case-option"
						data-correct="false"
						data-feedback-title="<?php esc_attr_e( 'Todavía es pronto para eso.', 'afectivalab' ); ?>"
						data-feedback-text="<?php esc_attr_e( 'Conviene entender primero qué está pasando con tu hijo antes de involucrar al colegio.', 'afectivalab' ); ?>"
					>
						<span class="case-option__letter">C</span>
						<span><?php esc_html_e( 'Llamo de inmediato al colegio.', 'afectivalab' ); ?></span>
					</button>
					<button
						type="button"
						class="case-option"
						data-correct="false"
						data-feedback-title="<?php esc_attr_e( 'Evita el tema en vez de resolverlo.', 'afectivalab' ); ?>"
						data-feedback-text="<?php esc_attr_e( 'Puede ser un alivio momentáneo, pero no ayuda a entender ni resolver lo que le está pasando.', 'afectivalab' ); ?>"
					>
						<span class="case-option__letter">D</span>
						<span><?php esc_html_e( 'Le permito quedarse en casa.', 'afectivalab' ); ?></span>
					</button>
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
