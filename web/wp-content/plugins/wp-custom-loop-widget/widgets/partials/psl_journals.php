<?php $current_post_id = get_the_ID();
  if ($current_post_id == '4448') :
    $category = 'astronomy_journals';
  elseif ($current_post_id == '4450') :
    $category = 'chemistry_journals';
  elseif ($current_post_id == '4452') :
    $category = 'physics_journals';
  endif;

  $journals_args = array(
    'post_type' => 'journals',
    'posts_per_page' => 10,
    'hide_empty' => true,
    'meta_key' => 'name',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'facetwp' => true,
    'category_name' => $category
  );
  $journals = new WP_Query($journals_args);

  if ($journals->have_posts()) : ?>
    <div class="database">
      <?php while ( $journals->have_posts() ) : $journals->the_post(); ?>
        <?php include 'psl_journals_single.php'; ?>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>