<?php
$news_args = array(
  'post_type' => 'post',
  'cat' => 19,
  'posts_per_page' => 10,
  'hide_empty' => true,
  //'meta_key' => 'title',
  'orderby' => 'date',
  'order' => 'DESC',
  'facetwp' => true
);
$news = new WP_Query($news_args);

if ($news->have_posts()) : ?>
  <div class="core-book-country">
    <?php while ( $news->have_posts() ) : $news->the_post(); ?>
      <div class="card">
        <div class="cover">
          <?php if( has_post_thumbnail()) :
              the_post_thumbnail();
          else: ?>
            <img src="/wp-content/themes/culu/images/staff/no-photo-profile.png" class="book-cover" alt="">
          <?php endif; ?>
        </div>
        <div class="card-body">
          <p class="card-title"><strong><a href="<?php echo the_permalink(); ?>"><?php echo the_title(); ?></a></strong></p>
          <div class="metadata">
            <div class="description">
                <?php
                  $my_content = apply_filters( 'the_content', get_the_content() );
                  $my_content = wp_strip_all_tags($my_content);
                  echo wp_trim_words( $my_content, 75, $moreLink);
                ?><br /><br />
                  <a href="<?php echo the_permalink(); ?>">Continue reading <?php echo the_title(); ?></a><br />
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
<?php endif; ?>