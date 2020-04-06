<div class="card">
  <div class="cover">
    <img src="<?php if (!empty(get_field('image'))) : echo the_field('image');
              else : ?>/wp-content/themes/culu/images/staff/no-photo-profile.png<?php endif; ?>" class="book-cover" alt="<?php echo the_field('title'); ?>">
  </div>
  <div class="card-body">
    <p class="card-title"><a href="<?php echo the_field('url') ?>"><strong><?php echo the_title(); ?></strong></a></p>
    <div class="metadata">
      <div class="description">
        <?php
        $terms = get_field('curators');
        if ($terms) :
          $total = count($terms);
          $count = 1;
          if ($count < $total) {
            echo 'Curators: ';
          } else {
            echo 'Curator: ';
          }
          foreach ($terms as $term) :
            echo $term->name;
            if ($count < $total) {
              echo ', ';
            }
            $count++;
          endforeach;
        endif;
        ?>
      </div>
    </div>
  </div>
</div>