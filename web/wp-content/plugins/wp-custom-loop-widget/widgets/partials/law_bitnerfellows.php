<?php $fellows_args = array(
    'post_type' => 'fellows',
    'posts_per_page' => -1,
    'hide_empty' => true,
    'meta_key' => 'year',
    'orderby' => 'meta_value',
    'order' => 'DESC',
    'facetwp' => true
  );
  $fellows = new WP_Query($fellows_args);

  if ($fellows->have_posts()) : ?>
    <div>
      <?php while ( $fellows->have_posts() ) : $fellows->the_post(); ?>
        <?php if ((get_field('type_of_fellow') == 'Bitner')) : ?>
      <section class="fellows-list staff-profile" aria-label="<?php echo the_field('type_of_fellow'); ?>: <?php echo the_field('name'); ?>" >
        <?php if( !empty(get_field('photo'))) : ?>
        <img class="staff-photo" src="<?php echo the_field('photo'); ?>" alt="<?php echo the_field('name'); ?>">
        <?php else : ?>
        <img class="staff-photo" src="/wp-content/themes/culu/images/staff/no-photo-profile.png" alt="">
        <?php endif; ?>
        <h5 class="card-title"><a href="<?php echo the_permalink($post->ID); ?>"><?php echo the_field('name'); ?></a></h5>
        <p><?php echo the_field('type_of_fellow'); ?> Fellow, <?php echo the_field('year'); ?></p>
      </section>
      <?php endif;
  endwhile; ?>
    </div>
  <?php endif;
}); ?>