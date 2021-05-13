<?php $book_args = array(
    'post_type' => 'book_of_the_month',
    'posts_per_page' => 10,
    'hide_empty' => true,
    'order' => 'DESC',
    'facetwp' => true
  );
  $book = new WP_Query($book_args);

  if ($book->have_posts()) : ?>
    <div>
      <?php while ( $book->have_posts() ) : $book->the_post(); ?>
        <?php include 'ilr_botm_single.php'; ?>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>