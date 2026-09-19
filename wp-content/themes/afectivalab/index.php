<?php
/**
 * Plantilla de respaldo (requerida por WordPress).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="container" style="padding-block: var(--space-7);">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
			<?php
		endwhile;
		?>
	<?php else : ?>
		<p><?php esc_html_e( 'No hay contenido para mostrar.', 'afectivalab' ); ?></p>
	<?php endif; ?>
</main>

<?php
get_footer();
