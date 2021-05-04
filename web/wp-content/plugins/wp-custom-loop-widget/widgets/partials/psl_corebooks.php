<?php $current_post_id = get_the_ID();
  if ($current_post_id == '3920') :
    $category = 'astronomy';
  elseif ($current_post_id == '4434') :
    $category = 'chemistry';
  elseif ($current_post_id == '4437') :
    $category = 'physics';
  endif;

  $core_books_args = array(
    'post_type' => 'core_books',
    'posts_per_page' => 30,
    'hide_empty' => true,
    'meta_key' => 'title',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'facetwp' => true,
    'category_name' => $category
  );
  $core_books = new WP_Query($core_books_args);

  if ($core_books->have_posts()) : ?>
    <div class="core-book">
      <?php while ( $core_books->have_posts() ) : $core_books->the_post(); ?>
        <?php include 'psl_corebooks_single.php'; ?>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>