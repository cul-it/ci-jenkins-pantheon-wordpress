<div class="card">
  <div class="card-body">
    <div class="metadata">
      <div class="description">
        <?php if(get_field('post_type') == 'Bibliography') : ?>
          <?php if( !empty(get_field('author'))) : ?><?php echo the_field('author'); ?><?php endif; ?><?php if( (!empty(get_field('author'))) && (!empty(get_field('title')))) : ?>, <?php endif; ?><?php if( !empty(get_field('title'))) : ?><em><?php echo the_field('title'); ?></em><?php endif; ?>.<?php if( !empty(get_field('location'))) : ?> <?php echo the_field('location'); ?><?php endif; ?><?php if( !empty(get_field('publisher'))) : ?>: <?php echo the_field('publisher'); ?><?php endif; ?><?php if( !empty(get_field('year'))) : ?>, <?php echo the_field('year'); ?><?php endif; ?>.<?php if( !empty(get_field('call_number'))) : ?> (<a href="https://newcatalog.library.cornell.edu/catalog?utf8=✓&controller=catalog&action=index&q=<?php echo the_field('call_number'); ?>&search_field=call+number"><?php echo the_field('call_number'); ?></a>)<?php endif; ?><?php if( !empty(get_field('version'))) : ?> (<?php echo the_field('version'); ?>)<?php endif; ?>
        <br />
        <?php elseif(get_field('post_type') == 'Link/Description') : ?>
          <?php if( !empty(get_field('title'))) : ?><a href="<?php echo the_field('url'); ?>"><?php echo the_field('title'); ?></a><br /><?php endif; ?>
          <?php if( !empty(get_field('description'))) : ?><strong>Description:</strong> <?php echo the_field('description'); ?><br /><?php endif; ?>
        <?php endif; ?>
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
          endif; ?>
          <br />
        <?php endif; ?>
          <?php if( !empty(get_field('region_ethnicity'))) : ?>
          <?php $terms = get_the_terms( $post->ID, 'region_ethnicity' );
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
          endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>