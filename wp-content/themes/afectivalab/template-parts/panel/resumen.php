<?php
/**
 * Panel > Resumen: cómo va el contenido y cómo lo usan las familias.
 *
 * Las cifras sobre familias son agregadas a propósito: cuántos perfiles hay
 * por etapa y cuántos terminaron cada curso, nunca el nombre de un niño ni el
 * avance de una familia concreta. Ver inc/equipo.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afectivalab_resumen  = afectivalab_resumen_equipo();
$afectivalab_vacios   = afectivalab_cursos_sin_clases();
$afectivalab_es_admin = current_user_can( 'manage_afectivalab_contenido' );
$afectivalab_max      = max( 1, max( wp_list_pluck( $afectivalab_resumen['por_etapa'], 'total' ) ) );

$afectivalab_cifras = array(
	array(
		'icono'   => 'menu-mundos',
		'tono'    => 'purple',
		'numero'  => $afectivalab_resumen['cursos_publicados'],
		'etiqueta' => __( 'Cursos publicados', 'afectivalab' ),
		'extra'   => $afectivalab_resumen['cursos_borrador'] ? sprintf( /* translators: %d: cursos en borrador. */ _n( '%d en borrador', '%d en borrador', $afectivalab_resumen['cursos_borrador'], 'afectivalab' ), $afectivalab_resumen['cursos_borrador'] ) : '',
		'url'     => afectivalab_panel_url( array( 'seccion' => 'cursos' ) ),
	),
	array(
		'icono'   => 'eq-play',
		'tono'    => 'green',
		'numero'  => $afectivalab_resumen['clases_publicadas'],
		'etiqueta' => __( 'Microclases publicadas', 'afectivalab' ),
		'extra'   => $afectivalab_resumen['clases_borrador'] ? sprintf( /* translators: %d: microclases en borrador. */ _n( '%d en borrador', '%d en borrador', $afectivalab_resumen['clases_borrador'], 'afectivalab' ), $afectivalab_resumen['clases_borrador'] ) : '',
		'url'     => afectivalab_panel_url( array( 'seccion' => 'clases' ) ),
	),
	array(
		'icono'   => 'menu-hijos',
		'tono'    => 'yellow',
		'numero'  => $afectivalab_resumen['familias'],
		'etiqueta' => __( 'Familias registradas', 'afectivalab' ),
		'extra'   => '',
		'url'     => current_user_can( 'list_users' ) ? afectivalab_panel_url( array( 'seccion' => 'usuarios' ) ) : '',
	),
	array(
		'icono'   => 'menu-cuenta',
		'tono'    => 'purple',
		'numero'  => $afectivalab_resumen['hijos'],
		'etiqueta' => __( 'Perfiles de hijo', 'afectivalab' ),
		'extra'   => $afectivalab_resumen['sin_etapa'] ? sprintf( /* translators: %d: perfiles fuera del rango de edad. */ _n( '%d fuera de 3–17 años', '%d fuera de 3–17 años', $afectivalab_resumen['sin_etapa'], 'afectivalab' ), $afectivalab_resumen['sin_etapa'] ) : '',
		'url'     => '',
	),
);
?>

<ul class="eq-cifras reveal-stagger">
	<?php foreach ( $afectivalab_cifras as $afectivalab_cifra ) : ?>
		<li>
			<?php if ( $afectivalab_cifra['url'] ) : ?>
				<a class="eq-cifra" href="<?php echo esc_url( $afectivalab_cifra['url'] ); ?>">
			<?php else : ?>
				<div class="eq-cifra">
			<?php endif; ?>
				<span class="eq-cifra__icono user-menu__icono--<?php echo esc_attr( $afectivalab_cifra['tono'] ); ?>"><?php afectivalab_icon( $afectivalab_cifra['icono'] ); ?></span>
				<span class="eq-cifra__numero"><?php echo esc_html( $afectivalab_cifra['numero'] ); ?></span>
				<span class="eq-cifra__etiqueta"><?php echo esc_html( $afectivalab_cifra['etiqueta'] ); ?></span>
				<?php if ( $afectivalab_cifra['extra'] ) : ?>
					<span class="eq-cifra__extra"><?php echo esc_html( $afectivalab_cifra['extra'] ); ?></span>
				<?php endif; ?>
			<?php echo $afectivalab_cifra['url'] ? '</a>' : '</div>'; ?>
		</li>
	<?php endforeach; ?>
</ul>

<?php if ( $afectivalab_vacios ) : ?>
	<div class="eq-aviso reveal">
		<span class="eq-aviso__icono" aria-hidden="true">!</span>
		<div>
			<strong><?php echo esc_html( sprintf( /* translators: %d: cantidad de cursos. */ _n( '%d curso publicado no tiene microclases', '%d cursos publicados no tienen microclases', count( $afectivalab_vacios ), 'afectivalab' ), count( $afectivalab_vacios ) ) ); ?></strong>
			<p><?php esc_html_e( 'Las familias los ven en su ruta como "en preparación". Conviene completarlos o pasarlos a borrador.', 'afectivalab' ); ?></p>
			<ul>
				<?php foreach ( $afectivalab_vacios as $afectivalab_vacio ) : ?>
					<li>
						<a href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'editar', 'id' => $afectivalab_vacio->ID ) ) ); ?>">
							<?php echo esc_html( $afectivalab_vacio->post_title ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php endif; ?>

<div class="eq-resumen">
	<section class="eq-card eq-resumen__cursos">
		<header class="eq-card__head">
			<span class="eq-card__icono eq-card__icono--verde"><?php afectivalab_icon( 'star' ); ?></span>
			<div>
				<h3><?php esc_html_e( 'Cómo va cada curso', 'afectivalab' ); ?></h3>
				<p><?php esc_html_e( 'Cuántos perfiles lo empezaron y cuántos lo terminaron. Cifras agregadas: sin nombres ni el avance de una familia en particular.', 'afectivalab' ); ?></p>
			</div>
		</header>

		<?php if ( ! $afectivalab_resumen['avance'] ) : ?>
			<div class="eq-vacio eq-vacio--chico">
				<?php afectivalab_icon( 'juego-mapa-desbloqueable', 'eq-vacio__icono' ); ?>
				<h3><?php esc_html_e( 'Todavía no hay cursos publicados', 'afectivalab' ); ?></h3>
				<p><?php esc_html_e( 'En cuanto publiques el primero aparecerá aquí con su avance.', 'afectivalab' ); ?></p>
			</div>
		<?php else : ?>
			<div class="eq-avance__cabecera" aria-hidden="true">
				<span><?php esc_html_e( 'Curso', 'afectivalab' ); ?></span>
				<span><?php esc_html_e( 'Empezaron', 'afectivalab' ); ?></span>
				<span><?php esc_html_e( 'Terminaron', 'afectivalab' ); ?></span>
			</div>
			<ul class="eq-avance">
				<?php foreach ( $afectivalab_resumen['avance'] as $afectivalab_fila ) : ?>
					<?php $afectivalab_tasa = $afectivalab_fila['empezaron'] ? (int) round( $afectivalab_fila['acabaron'] / $afectivalab_fila['empezaron'] * 100 ) : 0; ?>
					<li class="eq-avance__fila">
						<div class="eq-avance__curso">
							<a href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'editar', 'id' => $afectivalab_fila['curso']->ID ) ) ); ?>">
								<?php echo esc_html( $afectivalab_fila['curso']->post_title ); ?>
							</a>
							<span class="eq-avance__meta">
								<?php echo esc_html( sprintf( /* translators: %d: cantidad de microclases. */ _n( '%d microclase', '%d microclases', $afectivalab_fila['clases'], 'afectivalab' ), $afectivalab_fila['clases'] ) ); ?>
								<?php if ( $afectivalab_fila['empezaron'] ) : ?>
									· <?php echo esc_html( sprintf( /* translators: %d: porcentaje. */ __( '%d%% lo termina', 'afectivalab' ), $afectivalab_tasa ) ); ?>
								<?php endif; ?>
							</span>
							<span class="eq-barra" aria-hidden="true"><span style="width: <?php echo esc_attr( $afectivalab_tasa ); ?>%"></span></span>
						</div>
						<strong class="eq-avance__num"><?php echo esc_html( $afectivalab_fila['empezaron'] ); ?></strong>
						<strong class="eq-avance__num eq-avance__num--verde"><?php echo esc_html( $afectivalab_fila['acabaron'] ); ?></strong>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</section>

	<div class="eq-resumen__lateral">
		<section class="eq-card">
			<header class="eq-card__head">
				<span class="eq-card__icono eq-card__icono--amarillo"><?php afectivalab_icon( 'menu-hijos' ); ?></span>
				<div>
					<h3><?php esc_html_e( 'Perfiles por etapa', 'afectivalab' ); ?></h3>
					<p><?php esc_html_e( 'Dónde están hoy las familias: ayuda a decidir qué etapa cubrir primero.', 'afectivalab' ); ?></p>
				</div>
			</header>

			<ul class="eq-etapas">
				<?php foreach ( $afectivalab_resumen['por_etapa'] as $afectivalab_slug => $afectivalab_etapa ) : ?>
					<li>
						<span class="eq-etapas__nombre"><?php echo esc_html( $afectivalab_etapa['nombre'] ); ?></span>
						<span class="eq-etapas__total"><?php echo esc_html( $afectivalab_etapa['total'] ); ?></span>
						<span class="eq-barra eq-barra--amarilla" aria-hidden="true"><span style="width: <?php echo esc_attr( round( $afectivalab_etapa['total'] / $afectivalab_max * 100 ) ); ?>%"></span></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>

		<section class="eq-card">
			<header class="eq-card__head eq-card__head--simple">
				<h3><?php esc_html_e( 'Atajos', 'afectivalab' ); ?></h3>
			</header>

			<ul class="eq-atajos">
				<li>
					<a href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'nuevo' ) ) ); ?>" data-eq-modal-abrir="nuevo-curso">
						<span class="user-menu__icono user-menu__icono--purple"><?php afectivalab_icon( 'eq-mas' ); ?></span>
						<?php esc_html_e( 'Nuevo curso', 'afectivalab' ); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'clases', 'accion' => 'nuevo' ) ) ); ?>" data-eq-modal-abrir="nueva-clase">
						<span class="user-menu__icono user-menu__icono--green"><?php afectivalab_icon( 'eq-mas' ); ?></span>
						<?php esc_html_e( 'Nueva microclase', 'afectivalab' ); ?>
					</a>
				</li>
				<?php // Lo que el panel no cubre y sigue viviendo en el escritorio. ?>
				<?php if ( $afectivalab_es_admin ) : ?>
					<li>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . AFECTIVALAB_CPT_CURSO . '&page=afectivalab-demo' ) ); ?>">
							<span class="user-menu__icono user-menu__icono--yellow"><?php afectivalab_icon( 'star' ); ?></span>
							<?php esc_html_e( 'Contenido de prueba', 'afectivalab' ); ?>
						</a>
					</li>
				<?php endif; ?>
				<li>
					<a href="<?php echo esc_url( admin_url() ); ?>">
						<span class="user-menu__icono"><?php afectivalab_icon( 'menu-escritorio' ); ?></span>
						<?php esc_html_e( 'Escritorio de WordPress', 'afectivalab' ); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( add_query_arg( 'vista', 'familia', home_url( '/panel' ) ) ); ?>">
						<span class="user-menu__icono"><?php afectivalab_icon( 'eq-ver' ); ?></span>
						<?php esc_html_e( 'Ver la plataforma como una familia', 'afectivalab' ); ?>
					</a>
				</li>
			</ul>
		</section>
	</div>
</div>
