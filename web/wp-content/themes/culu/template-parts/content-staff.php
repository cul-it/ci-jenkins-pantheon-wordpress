<?php
/**
 * Template part for displaying staff profile posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php get_template_part('template-parts/staff'); ?>

	<footer class="entry-footer" aria-label="Edit Staff profile">

		<?php culu_edit_post(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
