<?php
$audio_args = array(
  'post_type' => 'audio',
  'posts_per_page' => 10,
  'hide_empty' => true,
  'meta_key' => 'title',
  'orderby' => 'meta_value',
  'order' => 'ASC',
  'facetwp' => true
);
$audio = new WP_Query($audio_args);

if ($audio->have_posts()) : ?>
  <div>
    <?php while ( $audio->have_posts() ) : $audio->the_post(); ?>
      <?php include 'africana_audio_single.php'; ?>
    <?php endwhile; ?>
  </div>
<?php endif; ?>