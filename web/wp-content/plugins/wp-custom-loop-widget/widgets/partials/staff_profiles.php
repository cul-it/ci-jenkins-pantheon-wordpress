<?php
  $profile_args = array(
    'post_type' => 'staff',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'hide_empty' => true,
    'facetwp' => true
  );
  $profiles = new WP_Query($profile_args);

  if ($profiles->have_posts()) :
    while ( $profiles->have_posts() ) : $profiles->the_post();?>
    <?php include 'staff_profiles_single.php'; ?>
    <?php endwhile;
  endif; 
?>