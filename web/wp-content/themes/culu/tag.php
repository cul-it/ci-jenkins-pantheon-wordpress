<?php
/**
 * The template for displaying all pages
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

	<h2><?php _e( '', 'culu' ); echo single_tag_title('', false); ?></h2>

      <?php if (have_posts()): while (have_posts()) : the_post(); ?>

	<!-- article -->
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<h3>
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
		</h3>

      <div class="entry-content">

      <?php the_excerpt(); ?>

      </div><!-- .entry-content -->

  		<p class="category-tag"><?php _e( '', 'culu' ); the_category('  '); // Separated by commas ?></p>

  		<p class="tag-tag"><?php the_tags(__('', 'culu'), '  ', '<br>'); // Separated by commas with a line break at the end ?>

  	<footer class="entry-footer" aria-label="Footer content">

  		<?php //culu_category_links(); ?>
  		<?php //culu_tag_links(); ?>
  		<?php culu_edit_post(); ?>

  	</footer><!-- .entry-footer -->

	</article>
	<!-- /article -->

  <hr>

  <?php endwhile; ?>

  <?php else: ?>

  	<!-- article -->
  	<article>
  		<h3><?php _e( 'Sorry, nothing to display.', 'culu' ); ?></h3>
  	</article>
  	<!-- /article -->

  <?php endif; ?>

  <div class="pagination">
    <?php culu_pagination(); ?>
  </div>

		</section>
		<!-- /section -->
	</main>

<?php get_footer(); ?>
