<div class="card">
  <div class="cover">
    <img src="<?php if (!empty(get_field('cover'))) : echo the_field('cover');
              else : ?>/wp-content/themes/culu/images/staff/no-photo-profile.png<?php endif; ?>" class="book-cover" alt="<?php echo the_field('title'); ?>">
  </div>
  <div class="card-body">
    <p class="card-title"><strong><?php echo the_field('title'); ?></strong></p>
    <div class="metadata">
      <p class="description">
        <?php if (!empty(get_field('author_first_name'))) : echo the_field('author_first_name');
        endif; ?> <?php if (!empty(get_field('author_last_name'))) : echo the_field('author_last_name');
                  endif; ?>
        </p>
        <p><?php if (!empty(get_field('publication_year'))) : ?>Pub year: <?php echo the_field('publication_year');
                                                                      endif; ?></p>
      
      <p><?php if (!empty(get_field('bibid'))) : ?>Catalog: <a href="https://newcatalog.library.cornell.edu/catalog/<?php echo the_field('bibid'); ?>"><?php echo the_field('bibid'); ?></a><?php endif; ?></p>
    
    <p></p><?php if (!empty(get_field('isbn'))) : ?>ISBN: <?php echo the_field('isbn');
                                                  endif; ?></p>
      </div>
    </div>
  </div>
</div>