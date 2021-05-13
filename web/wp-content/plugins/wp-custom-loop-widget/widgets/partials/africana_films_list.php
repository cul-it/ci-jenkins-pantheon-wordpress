<?php
$films_args = array(
  'post_type' => 'films',
  'posts_per_page' => 10,
  'hide_empty' => true,
  'meta_key' => 'title',
  'orderby' => 'meta_value',
  'order' => 'ASC',
  'facetwp' => true
);
$films = new WP_Query($films_args);

if ($films->have_posts()) : ?>
  <div>
    <?php while ( $films->have_posts() ) : $films->the_post(); ?>
      <?php include 'africana_films_single.php'; ?>
    <?php endwhile; ?>
  </div>
<?php endif; ?>