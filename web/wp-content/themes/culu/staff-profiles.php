<?php
/*
  Template Name: Staff Profiles
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
      'post_type' => 'staff',
      'post_status' => 'publish',
      'posts_per_page' => -1
    ));


  while ($query->have_posts()) {
    $query->the_post();
    $post_id = get_the_ID();

    ?>

	<section class="staff-profile" aria-label="Staff profile" >
    <?php

    $image = get_field('photo');

    if( !empty($image) ) { ?>

      <img class="staff-photo" src="<?php echo $image['url'];?>" alt="<?php echo $image['alt']; ?>">

    <?php } else { ?>

      <img class="staff-photo" src="<?php echo get_template_directory_uri(); ?>/images/staff/no-photo-profile.png">

    <?php } ?>

    <h2><?php echo the_field('first_name');?>
        <?php echo the_field('last_name');

        if ( !empty(get_field('degree')) ) {
          echo ', ';
          echo the_field('degree');
        }
        ?>
    </h2>

    <h3><?php echo the_field('title'); ?></h3>

    <p><a href="mailto:<?php echo the_field('email');?>"><?php echo the_field('email');?></a></p>
    <p class="staff-phone">Phone: <?php the_field('phone');?></p>


    <?php if ( !empty(get_field('consultation')) ) { ?>
      <script>
        jQuery.getScript("https://api3.libcal.com/js/myscheduler.min.js", function() {
            jQuery("#<?php echo the_field('consultation'); ?>").LibCalMySched({iid: 973, lid: 0, gid: 0, uid: 18275, width: 560, height: 680, title: 'Make an Appointment', domain: 'https://api3.libcal.com'});
        });
      </script>

      <p>
        <a href="#" id="<?php echo the_field('consultation'); ?>" class="btn-graphic">
          Book a Consultation
        </a>
      </p>

    <?php } ?>

    </div>

    </section>

  <?php
  }

  wp_reset_query();
?>

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

	<?php culu_category_links(); ?>
	<?php culu_tag_links(); ?>
	<?php culu_edit_post(); ?>

</footer><!-- .entry-footer -->

	<?php endwhile; // End of the loop. ?>

</main><!-- #main -->

<?php
get_footer();
?>
