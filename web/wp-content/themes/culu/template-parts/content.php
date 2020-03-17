<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>  aria-label="<?php the_title(); ?>">

	<header class="entry-header">

		<?php

		if ( is_singular() ) :
			the_title( '<h2 class="entry-title">', '</h2>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
		?>
		
		<?php if ( has_post_thumbnail() )  { ?>
			<figure class="featured-image">
				<?php culu_post_thumbnail(); ?>
				<figcaption><?php echo get_post(get_post_thumbnail_id())->post_excerpt . " - " . get_post(get_post_thumbnail_id())->post_content; ?></figcaption>
			</figure>
		<?php } ?>
		

			<div class="entry-meta">

				<?php
				culu_posted_on();
				//culu_posted_by();
				?>

			</div><!-- .entry-meta -->

		<?php endif; ?>


	</header><!-- .entry-header -->

	<div class="entry-content">

		<?php
		the_content( sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'culu' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		) );

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'culu' ),
			'after'  => '</div>',
		) );
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer" aria-label="Edit post">

		<?php culu_category_links(); ?>
		<?php culu_tag_links(); ?>
		<?php culu_edit_post(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
