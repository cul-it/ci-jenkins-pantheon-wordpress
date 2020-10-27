<?php
/**
 * The main template file for homepage
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

get_header();
?>

<main id="main-content" class="page-home">
	
    <?php
	if ( have_posts() ) :

		//if ( is_home() && ! is_front_page() ) :
			?>

    <?php
		//endif;

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
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> aria-label="Homepage content">
        <header class="entry-header">
            <?php //the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        </header><!-- .entry-header -->

        <?php //culu_post_thumbnail(); ?>

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
        <footer class="entry-footer">
            <?php
						edit_post_link(
							sprintf(
								wp_kses(
									/* translators: %s: Name of current post. Only visible to screen readers */
									__( 'Edit <span class="screen-reader-text">%s</span>', 'culu' ),
									array(
										'span' => array(
											'class' => array(),
										),
									)
								),
								get_the_title()
							),
							'<span class="edit-link">',
							'</span>'
						);
						?>
        </footer><!-- .entry-footer -->
        <?php endif; ?>
    </article>

    <!-- #post-<?php the_ID(); ?> -->

    <?php

		endwhile;



		//the_posts_navigation();

	else :

		get_template_part( 'template-parts/content', 'none' );

	endif;
	?>

</main><!-- #main -->

<?php  get_footer();?>