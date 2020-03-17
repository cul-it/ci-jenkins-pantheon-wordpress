<?php $faq_args = array(
    'post_type' => 'faqs',
    'posts_per_page' => 10,
    'hide_empty' => true,
    'meta_key' => 'faq_title',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'facetwp' => true
  );
  $faq = new WP_Query($faq_args);

  if ($faq->have_posts()) : ?>
    <div>
      <?php while ( $faq->have_posts() ) : $faq->the_post(); ?>
        <div class="card">
          <section class="faq-list" aria-label="FAQ: <?php echo the_field('faq_title'); ?>" >
            <div class="card-body">
              <h2 class="card-title"><a href="<?php echo the_permalink($post->ID); ?>"><?php echo the_field('faq_title'); ?></a></h2>
              <div class="faq-metadata">
                <?php if( !empty(get_field('faq_description'))) : ?>
                  <div class="description">
                    <strong>Description:</strong><br />
                    <?php echo wp_trim_words( get_field('faq_description' ), $num_words = 55, $more = '...' ); ?>
                  </div>
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
                  <div class="button">
                    <a href="<?php echo the_permalink($post->ID); ?>"><button>Read full FAQ: <?php echo wp_trim_words( get_field('faq_title' ), $num_words = 8, $more = '...' ); ?></button></a>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </section>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>