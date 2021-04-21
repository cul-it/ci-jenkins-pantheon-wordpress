<div class="card">
  <div class="card-body">
    <p class="card-title"><?php echo the_title(); ?></p>
    <div class="metadata">
      <div class="description">
        <?php if( !empty(get_field('chapter_author'))) : ?><strong>Chapter Author(s):</strong> <?php echo the_field('chapter_author'); ?><br /><?php endif; ?>
        <?php if( !empty(get_field('book_title'))) : ?><strong>Book Title:</strong> <?php echo the_field('book_title'); ?><br /><?php endif; ?>
        <?php if( !empty(get_field('book_author'))) : ?><strong>Book Author(s):</strong> <?php echo the_field('book_author'); ?><br /><?php endif; ?>
        <?php if( !empty(get_field('call_number'))) : ?><strong>Call Number:</strong> <a href="https://newcatalog.library.cornell.edu/catalog?utf8=✓&controller=catalog&action=index&q=<?php echo the_field('call_number'); ?>&search_field=call+number"><?php echo the_field('call_number'); ?></a><br /><?php endif; ?>
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
        <?php if( !empty(get_field('subject'))) : ?>
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
          endif; ?>
          <br />
        <?php endif; ?>
        <?php if( !empty(get_field('library_location'))) : ?><strong>Library Location:</strong> <?php endif; ?>
      </div>
    </div>
  </div>
</div>