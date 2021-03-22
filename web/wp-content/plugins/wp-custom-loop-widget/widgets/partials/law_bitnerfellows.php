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
        <?php include 'law_bitnerfellows_single.php'; ?>
      <?php endwhile; ?>
    </div>
  <?php endif;
}); ?>