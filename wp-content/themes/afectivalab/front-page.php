<?php
/**
 * Página de inicio.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

get_template_part( 'template-parts/home/hero' );
get_template_part( 'template-parts/home/features' );
get_template_part( 'template-parts/home/how-it-works' );
get_template_part( 'template-parts/home/stages' );
get_template_part( 'template-parts/home/showcase' );
get_template_part( 'template-parts/home/testimonials' );
get_template_part( 'template-parts/home/cta' );

get_footer();
