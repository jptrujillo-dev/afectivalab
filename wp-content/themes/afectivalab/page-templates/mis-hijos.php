<?php
/**
 * Ruta /mis-hijos — servida vía inc/routes.php (template_include), no es una
 * Página del escritorio. Solo para usuarios logueados.
 *
 * Es también el primer paso después de registrarse: sin al menos un hijo no
 * se puede armar ninguna ruta, así que el registro termina aquí.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/ingresar' ) );
	exit;
}

$afectivalab_estado = afectivalab_handle_hijo_forms();
$afectivalab_hijos  = afectivalab_get_hijos();
$afectivalab_ejes   = afectivalab_ejes();
$afectivalab_meses  = afectivalab_meses();
$afectivalab_valores = $afectivalab_estado['valores'];
$afectivalab_editando = $afectivalab_estado['editando'];

get_header();
?>

<main class="account-page">
	<div class="container">
		<div class="hijos-page">

			<header class="hijos-page__head reveal">
				<h1><?php esc_html_e( 'Mis hijos', 'afectivalab' ); ?></h1>
				<p class="hijos-page__lead">
					<?php esc_html_e( 'Cada hijo tiene su propia ruta, armada según su edad y lo que más te preocupa hoy. Puedes agregar todos los que quieras.', 'afectivalab' ); ?>
				</p>
			</header>

			<?php
			// Los avisos van primero: cuentan lo que la persona acaba de
			// hacer, así que tienen que verse antes que nada.
			?>
			<?php if ( $afectivalab_estado['notice'] ) : ?>
				<div class="form-alert form-alert--success">
					<?php echo esc_html( $afectivalab_estado['notice'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $afectivalab_estado['errors'] ) ) : ?>
				<div class="form-alert" role="alert">
					<ul>
						<?php foreach ( $afectivalab_estado['errors'] as $afectivalab_error ) : ?>
							<li><?php echo esc_html( $afectivalab_error ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php
			// Con perfiles ya creados, la guía va aquí. Sin ninguno, va
			// después del formulario: lo primero que tiene que ver quien
			// llega es dónde escribir el nombre de su hijo, no un texto que
			// empuja el formulario fuera de la pantalla.
			?>
			<?php if ( $afectivalab_hijos && afectivalab_mostrar_onboarding() ) : ?>
				<?php get_template_part( 'template-parts/onboarding' ); ?>
			<?php endif; ?>

			<?php if ( $afectivalab_hijos ) : ?>
				<ul class="hijos-list reveal-stagger">
					<?php
					foreach ( $afectivalab_hijos as $afectivalab_hijo ) :
						$afectivalab_edad  = afectivalab_hijo_edad( $afectivalab_hijo->ID );
						$afectivalab_etapa = afectivalab_hijo_etapa( $afectivalab_hijo->ID );
						$afectivalab_preoc = afectivalab_hijo_preocupaciones( $afectivalab_hijo->ID );
						$afectivalab_inicial = mb_strtoupper( mb_substr( trim( $afectivalab_hijo->post_title ), 0, 1 ) );
						?>
						<li class="hijo-card">
							<span class="user-avatar user-avatar--initial hijo-card__avatar" aria-hidden="true"><?php echo esc_html( $afectivalab_inicial ); ?></span>

							<div class="hijo-card__body">
								<h2 class="hijo-card__name"><?php echo esc_html( $afectivalab_hijo->post_title ); ?></h2>

								<p class="hijo-card__meta">
									<?php
									if ( null !== $afectivalab_edad ) {
										printf(
											/* translators: %d: edad en años. */
											esc_html( _n( '%d año', '%d años', $afectivalab_edad, 'afectivalab' ) ),
											absint( $afectivalab_edad )
										);
									}

									if ( $afectivalab_etapa ) {
										echo ' <span class="hijo-card__sep" aria-hidden="true">&middot;</span> ';
										echo esc_html( $afectivalab_etapa->name );
									}
									?>
								</p>

								<?php if ( ! $afectivalab_etapa ) : ?>
									<p class="hijo-card__aviso">
										<?php esc_html_e( 'Nuestro contenido empieza a los 3 años. Te avisaremos apenas haya una ruta para su edad.', 'afectivalab' ); ?>
									</p>
								<?php endif; ?>

								<?php if ( $afectivalab_preoc ) : ?>
									<ul class="hijo-card__temas">
										<?php foreach ( $afectivalab_preoc as $afectivalab_slug ) : ?>
											<?php if ( isset( $afectivalab_ejes[ $afectivalab_slug ] ) ) : ?>
												<li class="hijo-card__tema">
													<?php afectivalab_icon( 'eje-' . $afectivalab_slug ); ?>
													<?php echo esc_html( $afectivalab_ejes[ $afectivalab_slug ]['nombre'] ); ?>
												</li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul>
								<?php else : ?>
									<p class="hijo-card__aviso">
										<?php esc_html_e( 'Sin temas marcados todavía: su ruta va a seguir el orden recomendado para su edad.', 'afectivalab' ); ?>
									</p>
								<?php endif; ?>
							</div>

							<div class="hijo-card__actions">
								<a class="hijo-card__edit" href="<?php echo esc_url( add_query_arg( 'editar', $afectivalab_hijo->ID, home_url( '/mis-hijos' ) ) . '#formulario' ); ?>">
									<?php esc_html_e( 'Editar', 'afectivalab' ); ?>
								</a>

								<form method="post" class="hijo-card__delete">
									<?php wp_nonce_field( 'afectivalab_hijo', 'afectivalab_hijo_nonce' ); ?>
									<button
										type="submit"
										name="afectivalab_hijo_eliminar"
										value="<?php echo esc_attr( $afectivalab_hijo->ID ); ?>"
										class="hijo-card__delete-button"
										data-confirm="<?php echo esc_attr( sprintf( __( '¿Quitar el perfil de %s?', 'afectivalab' ), $afectivalab_hijo->post_title ) ); ?>"
									>
										<?php esc_html_e( 'Quitar', 'afectivalab' ); ?>
									</button>
								</form>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php get_template_part( 'template-parts/hijo-form', null, array( 'editando' => $afectivalab_editando, 'valores' => $afectivalab_valores, 'primero' => false ) ); ?>

			<?php else : ?>

				<?php // Sin ningún perfil todavía: el formulario es lo primero, y la guía queda debajo. ?>
				<?php get_template_part( 'template-parts/hijo-form', null, array( 'editando' => $afectivalab_editando, 'valores' => $afectivalab_valores, 'primero' => true ) ); ?>

				<?php if ( afectivalab_mostrar_onboarding() ) : ?>
					<?php get_template_part( 'template-parts/onboarding' ); ?>
				<?php endif; ?>

			<?php endif; ?>

		</div>
	</div>
</main>

<?php get_footer(); ?>
