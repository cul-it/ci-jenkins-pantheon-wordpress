<?php 
$diversity_fellows_args = array(
  'post_type' => 'fellows',
  'posts_per_page' => -1,
  'hide_empty' => true,
  'meta_key' => 'year',
  'orderby' => 'meta_value',
  'order' => 'DESC',
  'facetwp' => true
);
$diversity_fellows = new WP_Query($diversity_fellows_args);

if ($diversity_fellows->have_posts()) : ?>
  <div>
    <?php while ( $diversity_fellows->have_posts() ) : $diversity_fellows->the_post(); ?>
      <?php include 'law_diversityfellows_single.php'; ?>
    <?php endwhile; ?>
  </div>
<?php endif; ?>