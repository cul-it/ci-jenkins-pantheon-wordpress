<?php
/* Start the Loop */
while ( have_posts() ) :
	the_post();
	/*
	 * Include the Post-Type-specific template for the content.
	 * If you want to override this in a child theme, then include a file
	 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
	 */
	//get_template_part( 'template-parts/content', get_post_type() );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header" aria-label="Title content">

		<?php

		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
		?>

		<?php if ( 'post' === get_post_type() ) : ?>

			<div class="entry-meta">

				<?php
				//culu_posted_on();
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

	<footer class="entry-footer" aria-label="Footer content">

		<?php culu_entry_footer(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->

<?php
	endwhile; // End of the loop.
	the_post_navigation();
?>
