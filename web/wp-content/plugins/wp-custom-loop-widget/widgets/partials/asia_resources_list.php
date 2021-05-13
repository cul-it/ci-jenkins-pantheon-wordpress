<?php $resources_args = array(
  'post_type' => 'resources',
  'posts_per_page' => 10,
  'hide_empty' => true,
  'meta_key' => 'title',
  'orderby' => 'meta_value',
  'order' => 'ASC',
  'facetwp' => true
);
$resources = new WP_Query($resources_args);

if ($resources->have_posts()) : ?>
  <div class="database">
    <?php while ( $resources->have_posts() ) : $resources->the_post(); ?>
      <?php include 'asia_resources_single.php'; ?>
    <?php endwhile; ?>
  </div>
<?php endif; ?>