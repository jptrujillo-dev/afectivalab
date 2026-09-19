<?php
/**
 * Header del sitio.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="container site-header__inner">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
			<?php afectivalab_icon( 'logo-color' ); ?>
		</a>

		<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Menú principal', 'afectivalab' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => 'afectivalab_default_primary_menu',
				)
			);
			?>
		</nav>

		<div class="header-actions">
			<?php if ( is_user_logged_in() ) : ?>
				<?php $afectivalab_user = wp_get_current_user(); ?>
				<span class="header-greeting"><?php echo esc_html( sprintf( /* translators: %s: nombre del usuario. */ __( 'Hola, %s', 'afectivalab' ), $afectivalab_user->display_name ) ); ?></span>
				<a class="btn-ghost" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Salir', 'afectivalab' ); ?></a>
			<?php else : ?>
				<?php $afectivalab_route = get_query_var( 'afectivalab_route' ); ?>
				<?php if ( 'ingresar' !== $afectivalab_route ) : ?>
					<a class="btn-ghost" href="<?php echo esc_url( home_url( '/ingresar' ) ); ?>"><?php esc_html_e( 'Ingresar', 'afectivalab' ); ?></a>
				<?php endif; ?>
				<?php if ( 'registro' !== $afectivalab_route ) : ?>
					<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/registro' ) ); ?>"><?php esc_html_e( 'Empieza gratis', 'afectivalab' ); ?></a>
				<?php endif; ?>
			<?php endif; ?>
			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="mobile-nav" aria-label="<?php esc_attr_e( 'Abrir menú', 'afectivalab' ); ?>">
				<?php afectivalab_icon( 'menu' ); ?>
			</button>
		</div>
	</div>

	<nav id="mobile-nav" class="mobile-nav" aria-label="<?php esc_attr_e( 'Menú móvil', 'afectivalab' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '%3$s',
				'fallback_cb'    => 'afectivalab_default_primary_menu_links',
			)
		);
		?>
		<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Salir', 'afectivalab' ); ?></a>
		<?php elseif ( 'ingresar' !== get_query_var( 'afectivalab_route' ) ) : ?>
			<a href="<?php echo esc_url( home_url( '/ingresar' ) ); ?>"><?php esc_html_e( 'Ingresar', 'afectivalab' ); ?></a>
		<?php endif; ?>
	</nav>
</header>
