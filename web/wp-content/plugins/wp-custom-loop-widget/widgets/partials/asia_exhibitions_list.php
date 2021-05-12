<?php $exhibitions_args = array(
  'post_type' => 'exhibitions',
  'posts_per_page' => 10,
  'hide_empty' => true,
  //'meta_key' => 'title',
  'orderby' => 'date',
  'order' => 'DESC',
  'facetwp' => true
);
$exhibitions = new WP_Query($exhibitions_args);

if ($exhibitions->have_posts()) : ?>
  <div>
    <?php while ( $exhibitions->have_posts() ) : $exhibitions->the_post();
      include 'asia_exhibitions_single.php';
    endwhile; ?>
  </div>
<?php endif; ?>