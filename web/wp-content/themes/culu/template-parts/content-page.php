<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header" aria-label="Title content">

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

	</header>

	<?php culu_post_thumbnail(); ?>

	<div class="entry-content">

		<?php
		the_content();

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'culu' ),
			'after'  => '</div>',
		) );
		?>

	</div><!-- .entry-content -->

	<?php if ( get_edit_post_link() ) : ?>


	<footer class="entry-footer" aria-label="Footer content">

		<?php culu_edit_post(); ?>

	</footer><!-- .entry-footer -->

	<?php endif; ?>

</article><!-- #post-<?php the_ID(); ?> -->