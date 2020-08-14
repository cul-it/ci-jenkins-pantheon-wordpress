<?php $current_post_id = get_the_ID();
  if ($current_post_id == '3920') :
    $category = 'astronomy';
  elseif ($current_post_id == '4434') :
    $category = 'chemistry';
  elseif ($current_post_id == '4437') :
    $category = 'physics';
  endif;

  $core_books_args = array(
    'post_type' => 'core_books',
    'posts_per_page' => 30,
    'hide_empty' => true,
    'meta_key' => 'title',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'facetwp' => true,
    'category_name' => $category
  );
  $core_books = new WP_Query($core_books_args);

  if ($core_books->have_posts()) : ?>
    <div class="core-book">
      <?php while ( $core_books->have_posts() ) : $core_books->the_post(); ?>
        <div class="card">
          <div class="cover">
            <img src="<?php if( !empty(get_field('cover'))) : echo the_field('cover'); else : ?>/wp-content/themes/culu/images/staff/no-photo-profile.png<?php endif; ?>" class="book-cover" alt="<?php echo the_field('title'); ?>">
          </div>
          <div class="card-body">
            <p class="card-title"><strong><?php echo the_field('title'); ?></strong></p>
            <div class="metadata">
              <div class="description">
                <?php if( !empty(get_field('author_first_name'))) : echo the_field('author_first_name' ); endif; ?> <?php if( !empty(get_field('author_last_name'))) : echo the_field('author_last_name' ); endif; ?>
                <br />
                <?php if( !empty(get_field('publication_year'))) : ?>Pub year: <?php echo the_field('publication_year' ); endif; ?>
                <br />
                <?php if( !empty(get_field('bibid'))) : ?>Catalog: <a href="https://newcatalog.library.cornell.edu/catalog/<?php echo the_field('bibid'); ?>"><?php echo the_field('bibid'); ?></a><?php endif; ?>
                <br />
                <?php if( !empty(get_field('isbn'))) : ?>ISBN: <?php echo the_field('isbn'); endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>