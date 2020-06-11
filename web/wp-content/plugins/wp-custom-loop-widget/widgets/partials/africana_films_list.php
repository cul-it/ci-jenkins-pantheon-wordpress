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
      <div class="card">
        <section class="database-list" aria-label="<?php echo the_field('title'); ?>" >
          <div class="card-body">
            <h2 class="card-title"><?php echo the_field('title'); ?></h2>
            <div class="database-metadata">
              <div class="description">
                <?php if( !empty(get_field('location'))) : ?><?php echo the_field('location'); ?><?php endif; ?><?php if( !empty(get_field('location')) && !empty(get_field('company'))) : ?>: <?php endif; ?><?php if( !empty(get_field('company'))) : ?><?php echo the_field('company'); ?><?php endif; ?><?php if( (!empty(get_field('date')) && !empty(get_field('company'))) || (!empty(get_field('afr')) && !empty(get_field('company')))) : ?>, <?php endif; ?><?php if( !empty(get_field('date'))) : ?><?php echo the_field('date'); ?>.<?php endif; ?> <?php if( !empty(get_field('materials'))) : ?><?php echo the_field('materials'); ?><?php endif; ?> <?php if( !empty(get_field('length'))) : ?>(<?php echo the_field('length'); ?> min)<?php endif; ?>
              </div>
              <?php if( !empty(get_field('description'))) : ?>
                <div class="description">
                  <?php echo the_field('description'); ?>
                </div>
              <?php endif; ?>
              <?php if( !empty(get_field('afr'))) : ?>
                <div>
                  <div class="description">
                    <strong><?php if( !empty(get_field('call_number'))) : ?><a href="https://newcatalog.library.cornell.edu/catalog/<?php echo the_field('call_number'); ?>"><?php endif; ?><?php echo the_field('afr'); ?><?php if( !empty(get_field('call_number'))) : ?></a><?php endif; ?></strong>
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