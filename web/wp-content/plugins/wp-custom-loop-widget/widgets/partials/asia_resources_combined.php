<?php $args = array(
  'post_type' => array(
    'books', 'book_chapters', 'resources', 'database'
  ),
  'posts_per_page' => 10,
  'hide_empty' => true,
  'orderby' => 'date',
  'order' => 'ASC',
  'facetwp' => true
);
$list = new WP_Query($args);

if ( $list->have_posts() ) :
  while ( $list->have_posts() ) : $list->the_post();
    $current_post_id = get_the_ID();
    ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class( 'card mt-3r row' ); ?>>
      <div class="card-body">
        <div class="entry-header">
          <div>
            <h3><?php if (!empty(get_field('url'))) : ?><a href="<?php echo the_field('url'); ?>"><?php endif; ?><?php echo the_title(); ?><?php if (!empty(get_field('url'))) : ?></a><?php endif; ?></h3>
          </div>
        </div>
        <div class="search-metadata">
          <?php if (!empty(get_field('description'))) : ?>
            <div class="row">
              <p class="col-12 col-sm-3 col-md-4"><strong>Description:</strong><br />
              <?php echo wp_trim_words( get_field('description' ), $num_words = 25, $more = '...' ); ?></p>
            </div>
          <?php elseif (!empty(get_field('body'))) : ?>
            <div class="row">
              <p class="col-12 col-sm-3 col-md-4"><strong>Description:</strong><br />
              <?php echo wp_trim_words( get_field('body' ), $num_words = 25, $more = '...' ); ?></p>
            </div>
          <?php elseif (!empty(get_the_content())) : ?>
            <div class="row">
              <p class="col-12 col-sm-3 col-md-4"><strong>Description:</strong><br />
              <?php echo wp_trim_words( get_the_content(), $num_words = 25, $more = '...' ); ?></p>
            </div>
          <?php endif; ?>
          <?php if( !empty(get_field('chapter_author'))) : ?><strong>Chapter Author(s):</strong> <?php echo the_field('chapter_author'); ?><br /><?php endif; ?>
          <?php if( !empty(get_field('book_title'))) : ?><strong>Book Title:</strong> <?php echo the_field('book_title'); ?><br /><?php endif; ?>
          <?php if( !empty(get_field('book_author'))) : ?><strong>Book Author(s):</strong> <?php echo the_field('book_author'); ?><br /><?php endif; ?>
          <?php if( !empty(get_field('call_number'))) : ?><strong>Call Number:</strong> <a href="https://newcatalog.library.cornell.edu/catalog?utf8=✓&controller=catalog&action=index&q=<?php echo the_field('call_number'); ?>&search_field=call+number"><?php echo the_field('call_number'); ?></a><br /><?php endif; ?>
          <?php if( !empty(get_field('heading'))) : ?><strong>Heading:</strong> <?php echo the_field('heading'); ?><br /><?php endif; ?>
          <?php if( !empty(get_field('category'))) : ?><strong>Category:</strong> <?php echo the_field('category'); ?><br /><?php endif; ?>
          <?php if( !empty(get_field('country'))) :
            $countries = get_the_terms( $post->ID, 'region' );
              if ($countries != false) :
              $total = count($countries);
              $count = 1; ?>
              <strong>Country:</strong>
              <?php foreach ( $countries as $country ) :
                echo '<a class="topic-tag" href="' . home_url( '/', 'https' ) . $country->slug . '">' . $country->name . '</a>';
                if ($count < $total) :
                  echo ', ';
                endif;
                $count++;
              endforeach;
            endif;
           endif;?>
          <?php if( !empty(get_field('region_ethnicity'))) : ?>
            <?php $terms = get_the_terms( $post->ID, 'region_ethnicity' );
            if ($terms != false) :
              $total = count($terms);
              $count = 1; ?>
              <strong>Region/Ethnicity:</strong>
              <?php foreach ( $terms as $term ) :
                echo '<a class="topic-tag" href="' . home_url( '/?s=&search-type-home=site&fwp_categories=', 'https' ) . $term->slug . '">' . $term->name . '</a>';
                  if ($count < $total) :
                    echo ', ';
                  endif;
                  $count++;
              endforeach;
            endif;
          endif;?>
          <?php $terms = get_the_tags($post->ID); if ( $terms ) : ?>
              <div class="row">
                  <p class="col-12 col-sm-3 col-md-4"><strong>Topics:</strong>
                  <?php
                      $total = count($terms);
                      $count = 1;
                      foreach( $terms as $term ):
                          echo '<a class="topic-tag" href="' . home_url( '/?s=&search-type-home=site&fwp_categories=', 'https' ) . $term->slug . '">' . $term->name . '</a>';
                          if ($count < $total) {
                              echo ', ';
                          }
                          $count++;
                      endforeach;
                  ?></p>
              </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endwhile;
endif; ?>