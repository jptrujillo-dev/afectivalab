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
