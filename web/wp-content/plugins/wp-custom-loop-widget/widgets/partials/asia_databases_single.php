<div class="card">
  <div class="card-body">
    <p class="card-title"><?php if (!empty(get_field('url'))) : ?><a href="<?php echo the_field('url'); ?>"><?php endif; ?><?php echo the_title(); ?><?php if (!empty(get_field('url'))) : ?></a><?php endif; ?></p>
    <div class="metadata">
      <div class="description">
        <?php if( !empty(get_field('description'))) : ?><strong>Description:</strong> <?php echo the_field('description'); ?><br /><?php endif; ?>
        <?php if( !empty(get_field('access'))) : ?><strong>Access:</strong> <?php echo the_field('access'); ?><br /><?php endif; ?>
        <?php if( !empty(get_field('heading'))) : ?><strong>Heading:</strong> <?php echo the_field('heading'); ?><?php endif; ?>
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
          endif; ?>
          <br />
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>