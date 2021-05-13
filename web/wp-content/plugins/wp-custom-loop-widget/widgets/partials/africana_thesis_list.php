<?php
$thesis_args = array(
    'post_type' => 'thesis',
    'posts_per_page' => 10,
    'hide_empty' => true,
    'meta_key' => 'title',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'facetwp' => true
  );
  $thesis = new WP_Query($thesis_args);

  if ($thesis->have_posts()) : ?>
      <?php while ( $thesis->have_posts() ) : $thesis->the_post(); ?>
        <div class="card">
          <section class="database-list" aria-label="<?php echo str_replace(array("'", "\"", "&quot;"), "", htmlspecialchars(get_field('title'))); ?>">
            <div class="card-body">
              <h3 class="card-title"><a href="<?php echo the_permalink($post->ID); ?>"><?php echo the_field('title'); ?></a></h3>
              <div class="database-metadata">
                <?php if( !empty(get_field('abstract'))) : ?>
                  <div class="description">
                    <strong>Abstract Excerpt:</strong><br />
                    <?php echo wp_trim_words( get_field('abstract' ), $num_words = 55, $more = '...' ); ?>
                  </div>
                  <div class="more">
                    <?php $aria_label = str_replace(array("'", "\"", "&quot;"), "", htmlspecialchars(get_field('title'))); ?>
                    <a href="<?php echo the_permalink($post->ID) ?>" aria-label="Read full thesis: <?php echo $aria_label ?>"></a>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </section>
        </div>
      <?php endwhile;
  endif; 
?>