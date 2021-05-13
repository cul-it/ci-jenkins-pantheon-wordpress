<?php $current_post_id = get_the_ID();
  if ($current_post_id == '4454') :
    $category = 'astronomy_databases';
  elseif ($current_post_id == '4456') :
    $category = 'chemistry_databases';
  elseif ($current_post_id == '4458') :
    $category = 'physics_databases';
  endif;

  $databases_args = array(
    'post_type' => 'database',
    'posts_per_page' => 10,
    'hide_empty' => true,
    'meta_key' => 'name',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'facetwp' => true,
    'category_name' => $category
  );
  $databases = new WP_Query($databases_args);

  if ($databases->have_posts()) : ?>
    <div class="database">
      <?php while ( $databases->have_posts() ) : $databases->the_post(); ?>
        <?php include 'psl_databases_single.php'; ?>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>