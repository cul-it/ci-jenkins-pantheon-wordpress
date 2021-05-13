<?php $databases_args = array(
  'post_type' => 'database',
  'posts_per_page' => 10,
  'hide_empty' => true,
  'meta_key' => 'title',
  'orderby' => 'meta_value',
  'order' => 'ASC',
  'facetwp' => true
);
$databases = new WP_Query($databases_args);

if ($databases->have_posts()) : ?>
  <div class="database">
    <?php while ( $databases->have_posts() ) : $databases->the_post(); ?>
      <?php include 'asia_databases_single.php'; ?>
    <?php endwhile; ?>
  </div>
<?php endif; ?>