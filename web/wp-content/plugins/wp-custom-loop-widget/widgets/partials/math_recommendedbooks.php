<?php $recommended_books_args = array(
  'post_type' => 'recommended_books',
  'posts_per_page' => 30,
  'hide_empty' => true,
  'meta_key' => 'title',
  'orderby' => 'meta_value',
  'order' => 'ASC',
  'facetwp' => true,
  'category_name' => $category
);
$recommended_books = new WP_Query($recommended_books_args);

if ($recommended_books->have_posts()) : ?>
  <div class="core-book">
    <?php while ( $recommended_books->have_posts() ) : $recommended_books->the_post(); ?>
      <?php include 'math_recommendedbooks_single.php'; ?>
    <?php endwhile; ?>
  </div>
<?php endif; ?>