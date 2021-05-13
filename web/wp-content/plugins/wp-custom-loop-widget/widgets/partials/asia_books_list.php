<?php $books_args = array(
  'post_type' => 'books',
  'posts_per_page' => 10,
  'hide_empty' => true,
  'meta_key' => 'title',
  'orderby' => 'meta_value',
  'order' => 'ASC',
  'facetwp' => true
);
$books = new WP_Query($books_args);

if ($books->have_posts()) : ?>
  <div class="core-book">
    <?php while ( $books->have_posts() ) : $books->the_post(); ?>
      <?php include 'asia_books_single.php'; ?>
    <?php endwhile; ?>
  </div>
<?php endif; ?>