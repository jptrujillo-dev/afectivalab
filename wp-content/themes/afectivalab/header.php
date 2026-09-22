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
				<div class="user-menu" data-user-menu>
					<button type="button" class="user-menu__trigger" data-user-menu-trigger aria-haspopup="true" aria-expanded="false">
						<?php echo afectivalab_get_avatar_html( $afectivalab_user, 32 ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado dentro del helper. ?>
						<span class="user-menu__name"><?php echo esc_html( $afectivalab_user->display_name ); ?></span>
						<?php afectivalab_icon( 'chevron-down', 'user-menu__chevron' ); ?>
					</button>
					<div class="user-menu__panel" data-user-menu-panel>
						<a href="<?php echo esc_url( home_url( '/panel' ) ); ?>"><?php esc_html_e( 'Mi panel', 'afectivalab' ); ?></a>
						<?php if ( afectivalab_es_del_equipo() ) : ?>
							<a href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Escritorio', 'afectivalab' ); ?></a>
						<?php else : ?>
							<a href="<?php echo esc_url( home_url( '/mis-hijos' ) ); ?>"><?php esc_html_e( 'Mis hijos', 'afectivalab' ); ?></a>
						<?php endif; ?>
						<a href="<?php echo esc_url( home_url( '/mi-cuenta' ) ); ?>"><?php esc_html_e( 'Mi cuenta', 'afectivalab' ); ?></a>
						<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Salir', 'afectivalab' ); ?></a>
					</div>
				</div>
			<?php else : ?>
				<?php $afectivalab_route = get_query_var( 'afectivalab_route' ); ?>
				<?php if ( ! in_array( $afectivalab_route, array( 'ingresar', 'recuperar', 'restablecer' ), true ) ) : ?>
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
			<?php $afectivalab_mobile_user = wp_get_current_user(); ?>
			<div class="mobile-nav__user">
				<?php echo afectivalab_get_avatar_html( $afectivalab_mobile_user, 40 ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado dentro del helper. ?>
				<span><?php echo esc_html( $afectivalab_mobile_user->display_name ); ?></span>
			</div>
			<a href="<?php echo esc_url( home_url( '/panel' ) ); ?>"><?php esc_html_e( 'Mi panel', 'afectivalab' ); ?></a>
			<?php if ( afectivalab_es_del_equipo() ) : ?>
				<a href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Escritorio', 'afectivalab' ); ?></a>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/mis-hijos' ) ); ?>"><?php esc_html_e( 'Mis hijos', 'afectivalab' ); ?></a>
			<?php endif; ?>
			<a href="<?php echo esc_url( home_url( '/mi-cuenta' ) ); ?>"><?php esc_html_e( 'Mi cuenta', 'afectivalab' ); ?></a>
			<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Salir', 'afectivalab' ); ?></a>
		<?php elseif ( ! in_array( get_query_var( 'afectivalab_route' ), array( 'ingresar', 'recuperar', 'restablecer' ), true ) ) : ?>
			<a href="<?php echo esc_url( home_url( '/ingresar' ) ); ?>"><?php esc_html_e( 'Ingresar', 'afectivalab' ); ?></a>
		<?php endif; ?>
	</nav>
</header>
