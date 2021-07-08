<div class="card">
  <div class="cover">
    <img src="<?php if( !empty(get_field('cover_image'))) : echo the_field('cover_image'); else : ?>/wp-content/themes/culu/images/staff/no-photo-profile.png<?php endif; ?>" class="book-cover" alt="<?php echo the_field('title'); ?>">
  </div>
  <div class="card-body">
    <p class="card-title"><strong><?php echo the_field('title'); ?></strong></p>
    <div class="metadata">
      <div class="description">
        <?php if( !empty(get_field('author'))) : echo the_field('author' ); ?><br /><?php endif; ?>
        <?php if( !empty(get_field('location'))) : ?><?php echo the_field('location'); ?><?php endif; ?><?php if( !empty(get_field('publisher'))) : ?>: <?php echo the_field('publisher'); ?><?php endif; ?><?php if( !empty(get_field('published_date'))) : ?>, <?php echo the_field('published_date'); ?><?php endif; ?><br />
        <?php if( !empty(get_field('paper_copy'))) : ?><br /><strong>Paper Copy:</strong> <?php echo the_field('paper_copy'); ?><?php endif; ?>
        <?php if( !empty(get_field('online_copy'))) : ?><br /><strong>Online Copy:</strong> <?php echo the_field('online_copy'); ?><?php endif; ?>
          <?php if( !empty(get_field('country'))) :
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
          endif; ?>
          <br />
        <?php endif; ?>
        <?php if( !empty(get_field('additional_information'))) : ?><br /><br /><?php echo the_field('additional_information'); endif; ?>
      </div>
    </div>
  </div>
</div>