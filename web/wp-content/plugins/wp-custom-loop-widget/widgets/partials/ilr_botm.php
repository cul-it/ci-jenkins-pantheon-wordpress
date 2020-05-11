<?php $book_args = array(
    'post_type' => 'book_of_the_month',
    'posts_per_page' => 10,
    'hide_empty' => true,
    'order' => 'DESC',
    'facetwp' => true
  );
  $book = new WP_Query($book_args);

  if ($book->have_posts()) : ?>
    <div>
      <?php while ( $book->have_posts() ) : $book->the_post(); ?>
        <?php $year = get_field('featured_year'); $month = get_field('featured_month'); ?>
        <div class="card">
          <section class="database-list" aria-label="<?php echo $month->name; ?> <?php echo $year->name; ?>: <?php echo the_field('title'); ?>" >
            <div class="card-body">
              <h2 class="card-title"><a href="https://newcatalog.library.cornell.edu/catalog?utf8=%E2%9C%93&q=<?php echo the_field('call_number'); ?>&search_field=call%20number"><?php echo the_field('title'); ?> (<?php echo $month->name; ?>, <?php echo $year->name; ?>)</a></h2>
              <div class="database-metadata">
                <?php if( !empty(get_field('authors_editors'))) : ?>
                  <div class="description">
                    <?php echo the_field('authors_editors'); ?>
                  </div>
                <?php endif; ?>
                <?php if( !empty(get_field('cover_image'))) : ?>
                  <div class="description">
                    <img src="<?php echo the_field('cover_image'); ?>" alt="<?php echo the_field('title'); ?>">
                  </div>
                <?php endif; ?>
                <?php if( !empty(get_field('description'))) : ?>
                  <div class="description">
                    <?php echo the_field('description'); ?>
                  </div>
                <?php endif; ?>
                <?php if( !empty(get_field('city_and_state')) && !empty(get_field('publisher')) && !empty(get_field('number_of_pages'))) : ?>
                  <div>
                    <?php echo the_field('city_and_state'); ?>: <?php echo the_field('publisher'); ?>. <?php echo the_field('number_of_pages'); ?> pages.
                  </div>
                <?php endif; ?>
                <?php if( !empty(get_field('isbn'))) : ?>
                  <div>
                    ISBN: <?php echo the_field('isbn'); ?>
                  </div>
                <?php endif; ?>
                <?php if( !empty(get_field('call_number'))) : ?>
                  <div class="list_last">
                    Call number: <a href="https://newcatalog.library.cornell.edu/catalog?utf8=%E2%9C%93&q=<?php echo the_field('call_number'); ?>&search_field=call%20number"><?php echo the_field('call_number'); ?></a>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </section>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>