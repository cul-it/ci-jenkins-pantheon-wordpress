<?php $current_post_id = get_the_ID(); ?>

<h2><?php $link = get_field('article_url'); $link_url = $link['url']; ?><a href="<?php echo esc_url($link); ?>"><?php echo the_field('title'); ?></a></h2>
<div class="database_metadata">
<?php if (!empty(get_field('body'))) : ?>
  <div>
  <p><?php echo the_field('body'); ?></p>
  </div>
<?php endif; ?>
<?php if( !empty(get_field('author')) || !empty(get_field('publication')) || !empty(get_field('date'))) : ?>
  <div class="list_last">
    See <?php if (!empty(get_field('author'))) : the_field('author'); ?>,<?php endif; ?> <?php if (!empty(get_field('publication'))) : the_field('publication'); ?>,<?php endif; ?> <?php echo get_the_date('F j, Y'); ?>
  </div>
<?php endif; ?>
</div>