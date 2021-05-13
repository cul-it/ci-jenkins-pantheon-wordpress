<?php if (get_post_type() == 'exhibitions') : ?>
  <div class="core-book-country exhibit">
    <div class="card">
      <div class="cover">
        <?php if( !empty(get_field('image'))) :
          $image = get_field('image'); ?>
          <img src="<?php echo esc_url($image['url']); ?>" class="book-cover" alt="<?php echo esc_attr($image['alt']); ?>" />
        <?php else: ?>
          <img src="/wp-content/themes/culu/images/staff/no-photo-profile.png" class="book-cover" alt="">
        <?php endif; ?>
      </div>
      <div class="card-body">
        <p class="card-title"><strong><?php echo the_title(); ?></strong></p>
        <div class="metadata">
          <div class="description">
            <?php if( !empty(get_field('description'))) : echo the_field('description' ); endif;
            if( !empty(get_field('start_date'))) : echo the_field('start_date'); endif; ?><?php if( !empty(get_field('start_date')) && !empty(get_field('end_date'))) : ?> - <?php endif; ?><?php if( !empty(get_field('end_date'))) : echo the_field('end_date'); endif;
            if( !empty(get_field('region'))) :
              $countries = get_the_terms( $post->ID, 'region' );
              if ($countries != false) :
                $total = count($countries);
                $count = 1; ?>
                <br /><strong>Country:</strong>
                <?php foreach ( $countries as $country ) :
                  echo '<a class="topic-tag" href="' . home_url( '/', 'https' ) . $country->slug . '">' . $country->name . '</a>';
                  if ($count < $total) :
                    echo ', ';
                  endif;
                  $count++;
                endforeach;
              endif;
            endif;
            if( !empty(get_field('regionethnicity'))) : ?>
              <?php $terms = get_the_terms( $post->ID, 'regionethnicity' );
              if ($terms != false) :
                $total = count($terms);
                $count = 1; ?>
                <br />
                <strong>Region/Ethnicity:</strong>
                <?php foreach ( $terms as $term ) :
                  echo '<a class="topic-tag" href="' . home_url( '/?s=&search-type-home=site&fwp_categories=', 'https' ) . $term->slug . '">' . $term->name . '</a>';
                    if ($count < $total) :
                      echo ', ';
                    endif;
                    $count++;
                endforeach;
              endif;
            endif;
            if( !empty(get_field('subject'))) : ?>
              <br />
              <strong>Subject:</strong>
              <?php $subjects = get_the_terms( $post->ID, 'subject' );
              if ($subjects != false) :
                $total = count($subjects);
                $count = 1;
                foreach ( $subjects as $subject ) :
                  echo '<a class="topic-tag" href="' . home_url( '/?s=&search-type-home=site&fwp_categories=', 'https' ) . $subject->slug . '">' . $subject->name . '</a>';
                  if ($count < $total) :
                    echo ', ';
                  endif;
                  $count++;
                endforeach;
              endif;
            endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>