<?php $database_args = array(
    'post_type' => 'database',
    'posts_per_page' => 25,
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
              <h2 class="card-title"><a href="<?php echo the_field('database_url') ?>"><?php echo the_field('database_title'); ?></a></h2>
              <div class="database-metadata">
                <?php if( !empty(get_field('database_description'))) : ?>
                  <div class="description">
                    <strong>Description:</strong><br />
                    <?php echo wp_trim_words( get_field('database_description' ), $num_words = 55, $more = '...' ); ?>
                  </div>
                <?php endif; ?>
        <?php if( !empty(get_field('more_information_link'))) : ?>
                  <div class="description">
                    <div class="button">
                      <a href="<?php echo the_field('more_information_link') ?>"><button>
              <?php if( !empty(get_field('more_information_text'))) : 
                  echo the_field('more_information_text'); 
              else : ?>
                More information for <?php echo the_field('database_title'); ?>
              <?php endif; ?>
            </button></a>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </section>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>