<?php
/**
 * The template for displaying categories
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

get_header();
?>

<main id="main-content" class="page-interior">

	<h1><?php _e( '', 'culu' ); single_cat_title(); ?></h1>

	<?php get_template_part('loop'); ?>

	<div class="pagination">
		<?php culu_pagination(); ?>
	</div>

</main><!-- #main -->

<?php

get_footer();

?>
