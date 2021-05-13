<?php $book_chapters_args = array(
  'facetwp' => true,
  'post_type' => 'book_chapters',
  'posts_per_page' => 10,
  'hide_empty' => true,
  'orderby' => 'title',
  'order' => 'ASC'
);
$book_chapters = new WP_Query($book_chapters_args);

if ($book_chapters->have_posts()) : ?>
  <div class="database">
    <?php while ( $book_chapters->have_posts() ) : $book_chapters->the_post(); ?>
      <?php include 'asia_bookchapters_single.php'; ?>
    <?php endwhile; ?>
  </div>
<?php endif; ?>