<?php
/**
 * Afectivalab theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AFECTIVALAB_VERSION', '0.1.0' );

require get_theme_file_path( 'inc/setup.php' );
require get_theme_file_path( 'inc/enqueue.php' );
require get_theme_file_path( 'inc/icons.php' );
require get_theme_file_path( 'inc/nav.php' );
require get_theme_file_path( 'inc/routes.php' );
require get_theme_file_path( 'inc/auth.php' );
require get_theme_file_path( 'inc/account.php' );
require get_theme_file_path( 'inc/content.php' );
require get_theme_file_path( 'inc/roles.php' );

if ( is_admin() ) {
	require get_theme_file_path( 'inc/content-admin.php' );
}
