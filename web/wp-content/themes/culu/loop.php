<?php
/* Start the Loop */

if (have_posts()): while (have_posts()) : the_post();
	/*
	 * Include the Post-Type-specific template for the content.
	 * If you want to override this in a child theme, then include a file
	 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
	 */
	//get_template_part( 'template-parts/content', get_post_type() );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class();?> aria-label="<?php echo the_title(); ?>">

	<header class="entry-header">

		<?php

		if ( is_singular() ) :
			the_title( '<h3 class="entry-title">', '</h3>' );
		else :
			the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
		endif;

		the_date();
		?>

	</header><!-- .entry-header -->

	<div class="entry-content">

		<?php the_excerpt(); ?>

	</div><!-- .entry-content -->

	<p class="category-tag"><?php _e( '', 'culu' ); the_category(' '); // Separated by commas ?></p>

	<?php the_tags(__('', 'culu'), ' ', '<br>'); // Separated by commas with a line break at the end?>

	<?php //edit_post_link(); ?>
</article><!-- #post-<?php the_ID(); ?> -->

<hr>

<?php endwhile; ?>
<?php else: ?>

	<!-- article -->
	<article>
		<h3><?php _e( 'Sorry, nothing to display.', 'culu' ); ?></h3>
	</article>
	<!-- /article -->

<?php endif; ?>
