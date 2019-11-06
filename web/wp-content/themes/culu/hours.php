<?php
/**
 * The template for displaying all hours
 * Template Name: Hours
 * Template Post Type: post, page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package culu
 */

get_header();
?>

<main id="main-content" class="page-interior">

<?php

	while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/content', 'page' );

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) :
		comments_template();
	endif;

endwhile; // End of the loop.
?>
<?php $full_hours_label = get_theme_mod( 'full_hours_label', '' ); ?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://api3.libcal.com/js/hours_grid.js?002"></script>
<div id="s-lc-whw<?php echo $full_hours_label ?>"></div>
<script>
$(function(){
var week<?php echo $full_hours_label ?> = new $.LibCalWeeklyGrid( $("#s-lc-whw<?php echo $full_hours_label ?>"), { iid: 973, lid: <?php echo $full_hours_label ?>,  weeks: 4, systemTime: false });
});
</script>

</main><!-- #main -->

<?php
get_footer();
?>
