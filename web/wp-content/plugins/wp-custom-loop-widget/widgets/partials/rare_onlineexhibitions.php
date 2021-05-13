<?php $current_post_id = get_the_ID();

  $online_exhibitions_args = array(
    'post_type' => 'online_exhibitions',
    'posts_per_page' => 30,
    'hide_empty' => true,
    'facetwp' => true
  );
  $online_exhibitions = new WP_Query($online_exhibitions_args);

  if ($online_exhibitions->have_posts()) : ?>
    <div class="core-book">
      <?php while ( $online_exhibitions->have_posts() ) : $online_exhibitions->the_post(); ?>
        <?php include 'rare_onlineexhibitions_single.php'; ?>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>