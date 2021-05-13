<?php $database_args = array(
    'post_type' => 'database',
    'posts_per_page' => 10,
    'hide_empty' => true,
    'meta_key' => 'database_title',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'facetwp' => true
  );
  $database = new WP_Query($database_args);

  if ($database->have_posts()) : ?>
    <div>
      <?php while ( $database->have_posts() ) : $database->the_post(); ?>
        <div class="card">
          <section class="database-list" aria-label="Database: <?php echo the_field('database_title'); ?>" >
            <div class="card-body">
              <h2 class="card-title"><a href="<?php echo the_permalink($post->ID); ?>"><?php echo the_field('database_title'); ?></a></h2>
              <div class="database-metadata">
          <?php if( !empty(get_field('database_logo'))) : 
          $image = get_field('database_logo'); ?>
          <div class="database_logo">
            <a href="<?php echo the_permalink($post->ID); ?>"><img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>"></a>
          </div>
          <?php endif; ?>
                <?php if( !empty(get_field('database_description'))) : ?>
                  <div class="description">
                    <strong>Description:</strong><br />
                    <?php echo wp_trim_words( get_field('database_description' ), $num_words = 55, $more = '...' ); ?>
                  </div>
          <?php if (!empty(get_field('database_content_topics'))) : ?>
            <div class="description">
            <strong>Topics:</strong><br />
              <?php 
                $terms = get_the_tags($post->ID);
                if( $terms ):
                $total = count($terms);
                $count = 1;
                foreach( $terms as $term ):
                  echo '<a class="topic-tag" href="' . home_url( '/?s=&search-type-home=site&fwp_categories=', 'https' ) . $term->slug . '">' . $term->name . '</a>';
                  if ($count < $total) {
                    echo ', ';
                  }
                  $count++;
                endforeach;
                endif; 
              ?>
            </div>
          <?php endif; ?>
                  <div class="more">
                    <a href="<?php echo the_permalink($post->ID) ?>">See more <?php echo the_field('database_title'); ?> information</a>
                  </div>
                <?php endif; ?>
                <?php if( !empty(get_field('database_url'))) : ?>
                  <div class="button">
                    <a href="<?php echo the_field('database_url') ?>"><button>Connect to <?php echo the_field('database_title'); ?> <span class="fa fa-external-link"></span></button></a>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </section>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>