<?php $current_post_id = get_the_ID();
  if ($current_post_id == '4454') :
    $category = 'astronomy_databases';
  elseif ($current_post_id == '4456') :
    $category = 'chemistry_databases';
  elseif ($current_post_id == '4458') :
    $category = 'physics_databases';
  endif;

  $databases_args = array(
    'post_type' => 'database',
    'posts_per_page' => 10,
    'hide_empty' => true,
    'meta_key' => 'name',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'facetwp' => true,
    'category_name' => $category
  );
  $databases = new WP_Query($databases_args);

  if ($databases->have_posts()) : ?>
    <div class="database">
      <?php while ( $databases->have_posts() ) : $databases->the_post(); ?>
        <div class="card">
          <div class="card-body">
            <p class="card-title"><strong><?php if( !empty(get_field('link'))) : ?><a href="<?php echo the_field('link'); ?>"><?php endif; ?><?php echo the_field('name'); ?><?php if( !empty(get_field('link'))) : ?></a><?php endif; ?></strong></p>
            <div class="metadata">
              <div class="description">
                <?php if( !empty(get_field('bibid'))) : ?>Catalog: <a href="https://newcatalog.library.cornell.edu/catalog/<?php echo the_field('bibid'); ?>"><?php echo the_field('bibid'); ?></a><br /><?php endif; ?>
                <?php if( !empty(get_field('publisher'))) : ?>Publisher: <?php echo the_field('publisher' );?><br /><?php endif; ?>
                <?php if( !empty(get_field('description'))) : ?><?php echo the_field('description'); endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>