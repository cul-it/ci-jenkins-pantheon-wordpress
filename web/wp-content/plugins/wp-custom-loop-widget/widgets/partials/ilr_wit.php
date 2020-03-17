<?php $wit_args = array(
  'post_type' => 'wit',
  'posts_per_page' => 10,
  'hide_empty' => true,
  'orderby' => 'publish_date',
  'order' => 'DESC',
  'facetwp' => true
);
$wit = new WP_Query($wit_args);

if ($wit->have_posts()) : ?>
  <div>
    <?php while ( $wit->have_posts() ) : $wit->the_post(); ?>
      <div class="card">
        <section class="database-list" aria-label="Workplace Issues Today: <?php the_field('article_title'); ?>" >
          <div class="card-body">
            <h3 class="card-title"><?php $link = get_field('article_url'); $link_url = $link['url']; ?><a href="<?php esc_url($link); ?>"><?php the_field('article_title'); ?></a></h3>
            <div class="database-metadata">
              <?php if( !empty(get_field('body'))) : ?>
                <div class="description">
                  <?php the_field('body'); ?>
                </div>
              <?php endif; ?>
              <?php if( !empty(get_field('author')) || !empty(get_field('publication')) || !empty(get_field('date'))) : ?>
                <div class="list_last">
                  See <?php if (!empty(get_field('author'))) : the_field('author'); ?>,<?php endif; ?> <?php if (!empty(get_field('publication'))) : the_field('publication'); ?>,<?php endif; ?> <?php get_the_date('F j, Y'); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </section>
      </div>
    <?php endwhile; ?>
  </div>
<?php endif; ?>