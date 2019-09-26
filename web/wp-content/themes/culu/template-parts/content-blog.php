<?php
/**
 * Template part for displaying blog custom posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php get_template_part('template-parts/blog'); ?>

<footer class="entry-footer" aria-label="Footer content">

	<?php culu_edit_post(); ?>

</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
