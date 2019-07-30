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

	<header class="entry-header" aria-label="Title content">

	<?php
	/*
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
	*/
	?>

	</header><!-- .entry-header -->

	<?php get_template_part('template-parts/staff'); ?>

	</div><!-- .entry-content -->

	<footer class="entry-footer" aria-label="Footer content">

		<?php culu_edit_post(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
