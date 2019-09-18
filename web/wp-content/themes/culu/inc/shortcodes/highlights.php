<?php
/**
  *
  * Hightligh slider using Swiperjs https://swiperjs.com
  * @package culu
  *
  *
  */

  function get_highlights() { ?>

    <!-- Swiper -->
    <div class="swiper-container">

      <div class="swiper-wrapper">

      <?php

      $args = array(
      'post_type' => 'highlights',
      'posts_per_page' => '10',
      'order' => 'desc',
      'suppress_filters' => 0,
      );

      $query = new WP_Query( $args );

      if ( $query->have_posts() ) :

        while ( $query->have_posts() ) : $query->the_post();?>

        <div class="swiper-slide">

          <figure>

            <a href="<?php echo the_field('highlights_link'); ?>"><img src="<?php echo the_field('highlights_photo') ?>" alt=""/></a>

          </figure>

          <a class="highlight-link" href="<?php echo the_field('highlights_link'); ?>"><?php the_field('highlights_description'); ?></a>

      </div>

        <?php

        endwhile;

        wp_reset_postdata();

      endif; ?>

        </div>

      <div class="swiper-pagination"></div>

    </div>

   <?php
   
  }

  add_shortcode( 'highlight_list', 'get_highlights' );
