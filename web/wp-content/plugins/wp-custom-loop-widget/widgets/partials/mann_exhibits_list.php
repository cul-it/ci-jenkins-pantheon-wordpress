<?php
  $exhibits_args = array(
    'post_type' => 'exhibits',
    'posts_per_page' => 10,
    'hide_empty' => true,
    //'meta_key' => 'title',
    'orderby' => 'date',
    'order' => 'DESC',
    'facetwp' => true
  );
  $exhibits = new WP_Query($exhibits_args);

  if ($exhibits->have_posts()) : ?>
    <div class="core-book-country exhibit">
      <?php while ( $exhibits->have_posts() ) : $exhibits->the_post(); ?>
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
            <p class="card-title">
              <strong>
                <?php if( !empty(get_field('url'))) : ?><a href="<?php echo the_field('url'); ?>"><?php endif; ?><?php echo the_title(); ?><?php if( !empty(get_field('url'))) : ?></a><?php endif; ?>
                <?php if (!empty(get_field('subtitle'))) :?><br /><span class="subtitle"><?php echo the_field('subtitle');?></span><?php endif; ?>
              </strong>
            </p>
            <div class="metadata">
              <div class="description">
                <?php if( !empty(get_field('description'))) : echo wp_trim_words( get_field('description' ), $num_words = 75, $more = '...' ); endif; ?><br />
              </div>
              <?php if( !empty(get_field('url'))) : ?>
                <p>
                    <a class="btn-graphic" href="<?php echo the_field('url'); ?>">Visit <?php echo the_title(); ?> Exhibit</a>
                </p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>