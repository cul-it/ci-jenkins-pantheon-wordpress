<?php
/*
  Template Name: Blog
  Template Post Type: post, page

 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

get_header();
?>

<main id="main-content" class="page-interior">

	<h2><?php _e( '', 'culu' ); the_title(); ?></h2>

  <?php

  while ( have_posts() ) :

  	the_post();
      $query = new WP_Query(array(
      'post_type' => 'blog',
      'post_status' => 'publish',
      'posts_per_page' => 2
    ));

		//It is not necessary to add this. But elementor doesn't like it and messes up awesome icons display
		the_content();

	  while ($query->have_posts()) {
	    $query->the_post();
	    $post_id = get_the_ID();

	  get_template_part('template-parts/blog');


	  }

  wp_reset_query();
	culu_pagination();

?>

<footer class="entry-footer" aria-label="Footer content">

	<?php culu_category_links(); ?>
	<?php culu_tag_links(); ?>
	<?php culu_edit_post(); ?>

</footer><!-- .entry-footer -->

	<?php endwhile; // End of the loop. ?>

</main><!-- #main -->

<?php
get_footer();
?>
