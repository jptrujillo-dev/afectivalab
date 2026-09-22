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
?>

<ul class="equipo-cifras reveal-stagger">
	<li class="cifra">
		<span class="cifra__numero"><?php echo esc_html( $afectivalab_resumen['cursos_publicados'] ); ?></span>
		<span class="cifra__etiqueta"><?php esc_html_e( 'Cursos publicados', 'afectivalab' ); ?></span>
		<?php if ( $afectivalab_resumen['cursos_borrador'] ) : ?>
			<span class="cifra__extra">
				<?php
				printf(
					/* translators: %d: cursos en borrador. */
					esc_html( _n( '%d en borrador', '%d en borrador', $afectivalab_resumen['cursos_borrador'], 'afectivalab' ) ),
					absint( $afectivalab_resumen['cursos_borrador'] )
				);
				?>
			</span>
		<?php endif; ?>
	</li>

	<li class="cifra">
		<span class="cifra__numero"><?php echo esc_html( $afectivalab_resumen['clases_publicadas'] ); ?></span>
		<span class="cifra__etiqueta"><?php esc_html_e( 'Microclases', 'afectivalab' ); ?></span>
		<?php if ( $afectivalab_resumen['clases_borrador'] ) : ?>
			<span class="cifra__extra">
				<?php
				printf(
					/* translators: %d: microclases en borrador. */
					esc_html( _n( '%d en borrador', '%d en borrador', $afectivalab_resumen['clases_borrador'], 'afectivalab' ) ),
					absint( $afectivalab_resumen['clases_borrador'] )
				);
				?>
			</span>
		<?php endif; ?>
	</li>

	<li class="cifra">
		<span class="cifra__numero"><?php echo esc_html( $afectivalab_resumen['familias'] ); ?></span>
		<span class="cifra__etiqueta"><?php esc_html_e( 'Familias registradas', 'afectivalab' ); ?></span>
	</li>

	<li class="cifra">
		<span class="cifra__numero"><?php echo esc_html( $afectivalab_resumen['hijos'] ); ?></span>
		<span class="cifra__etiqueta"><?php esc_html_e( 'Perfiles de hijo', 'afectivalab' ); ?></span>
		<?php if ( $afectivalab_resumen['sin_etapa'] ) : ?>
			<span class="cifra__extra">
				<?php
				printf(
					/* translators: %d: perfiles fuera del rango de edad. */
					esc_html( _n( '%d fuera de 3–17 años', '%d fuera de 3–17 años', $afectivalab_resumen['sin_etapa'], 'afectivalab' ) ),
					absint( $afectivalab_resumen['sin_etapa'] )
				);
				?>
			</span>
		<?php endif; ?>
	</li>
</ul>

<?php if ( $afectivalab_vacios ) : ?>
	<div class="equipo-aviso reveal">
		<strong><?php esc_html_e( 'Cursos publicados sin ninguna microclase', 'afectivalab' ); ?></strong>
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
<?php endif; ?>

<section class="panel-ruta">
	<h2 class="panel-seccion__titulo"><?php esc_html_e( 'Perfiles por etapa', 'afectivalab' ); ?></h2>
	<p class="equipo-nota">
		<?php esc_html_e( 'Dónde están hoy las familias. Sirve para decidir qué etapa conviene cubrir primero.', 'afectivalab' ); ?>
	</p>

	<ul class="equipo-etapas reveal-stagger">
		<?php foreach ( $afectivalab_resumen['por_etapa'] as $afectivalab_slug => $afectivalab_etapa ) : ?>
			<li class="equipo-etapa">
				<span class="equipo-etapa__nombre"><?php echo esc_html( $afectivalab_etapa['nombre'] ); ?></span>
				<span class="equipo-etapa__total"><?php echo esc_html( $afectivalab_etapa['total'] ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</section>

<section class="panel-ruta">
	<h2 class="panel-seccion__titulo"><?php esc_html_e( 'Cómo va cada curso', 'afectivalab' ); ?></h2>
	<p class="equipo-nota">
		<?php esc_html_e( 'Cifras agregadas: cuántos perfiles empezaron y cuántos terminaron. No mostramos nombres ni el avance de una familia en particular.', 'afectivalab' ); ?>
	</p>

	<?php if ( ! $afectivalab_resumen['avance'] ) : ?>
		<div class="equipo-vacio">
			<?php afectivalab_icon( 'juego-mapa-desbloqueable', 'equipo-vacio__icon' ); ?>
			<h3><?php esc_html_e( 'Todavía no hay cursos publicados', 'afectivalab' ); ?></h3>
			<p><?php esc_html_e( 'En cuanto publiques el primero aparecerá aquí con su avance.', 'afectivalab' ); ?></p>
		</div>
	<?php else : ?>
		<ul class="equipo-cursos reveal-stagger">
			<?php foreach ( $afectivalab_resumen['avance'] as $afectivalab_fila ) : ?>
				<li class="equipo-curso">
					<div class="equipo-curso__texto">
						<a href="<?php echo esc_url( get_permalink( $afectivalab_fila['curso'] ) ); ?>">
							<?php echo esc_html( $afectivalab_fila['curso']->post_title ); ?>
						</a>
						<span class="equipo-curso__meta">
							<?php
							printf(
								/* translators: %d: cantidad de microclases. */
								esc_html( _n( '%d microclase', '%d microclases', $afectivalab_fila['clases'], 'afectivalab' ) ),
								absint( $afectivalab_fila['clases'] )
							);
							?>
						</span>
					</div>

					<div class="equipo-curso__cifras">
						<span>
							<strong><?php echo esc_html( $afectivalab_fila['empezaron'] ); ?></strong>
							<?php esc_html_e( 'empezaron', 'afectivalab' ); ?>
						</span>
						<span>
							<strong><?php echo esc_html( $afectivalab_fila['acabaron'] ); ?></strong>
							<?php esc_html_e( 'terminaron', 'afectivalab' ); ?>
						</span>
					</div>

					<a class="equipo-curso__editar" href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'editar', 'id' => $afectivalab_fila['curso']->ID ) ) ); ?>">
						<?php esc_html_e( 'Editar', 'afectivalab' ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</section>

<section class="panel-ruta">
	<h2 class="panel-seccion__titulo"><?php esc_html_e( 'Atajos', 'afectivalab' ); ?></h2>

	<ul class="equipo-enlaces reveal-stagger">
		<li>
			<a href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'cursos', 'accion' => 'nuevo' ) ) ); ?>">
				<?php esc_html_e( 'Nuevo curso', 'afectivalab' ); ?>
			</a>
		</li>
		<li>
			<a href="<?php echo esc_url( afectivalab_panel_url( array( 'seccion' => 'clases', 'accion' => 'nuevo' ) ) ); ?>">
				<?php esc_html_e( 'Nueva microclase', 'afectivalab' ); ?>
			</a>
		</li>

		<?php // Lo que el panel no cubre y sigue viviendo en el escritorio. ?>
		<?php if ( $afectivalab_es_admin ) : ?>
			<li>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . AFECTIVALAB_CPT_CURSO . '&page=afectivalab-demo' ) ); ?>">
					<?php esc_html_e( 'Contenido de prueba', 'afectivalab' ); ?>
				</a>
			</li>
		<?php endif; ?>
		<li>
			<a href="<?php echo esc_url( admin_url() ); ?>">
				<?php esc_html_e( 'Escritorio de WordPress', 'afectivalab' ); ?>
			</a>
		</li>
	</ul>
</section>
