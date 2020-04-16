<?php $current_post_id = get_the_ID(); ?>

<div class="database_metadata">
  <?php if (!empty(get_field('database_description'))) : ?>
    <div>
      <p class="metadata-title description"><strong>Description:</strong></p>
      <p><?php echo the_field('database_description'); ?></p>
    </div>
  <?php endif; ?>
  <?php if (!empty(get_field('database_when_to_use'))) : ?>
    <div>
      <p class="metadata-title"><strong>When to use:</strong></p>
      <p><?php echo the_field('database_when_to_use'); ?></p>
    </div>
  <?php endif; ?>
  <?php if (!empty(get_field('database_video_help'))) : ?>
    <div>
      <p class="metadata-title"><strong>Video help:</strong></p>
      <p><?php echo the_field('database_video_help'); ?></p>
    </div>
  <?php endif; ?>
  <?php if (!empty(get_field('database_content_topics'))) : ?>
    <div>
      <p class="metadata-title"><strong>Topics:</strong></p>
      <p><?php 
        foreach(get_the_tags($current_post_id) as $tag) :
          echo '<a class="topic-tag" href="' . home_url( '/?s=&search-type-home=site&fwp_categories=', 'https' ) . $tag->slug . '">' . $tag->name . '</a>';
        endforeach; ?>
      </p>
    </div>
  <?php endif; ?>
</div>