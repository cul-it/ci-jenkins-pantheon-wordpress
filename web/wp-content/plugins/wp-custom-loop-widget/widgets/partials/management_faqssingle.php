<?php $current_post_id = get_the_ID();
  ?>

    <div class="database_metadata">
    <?php if (!empty(get_field('faq_description'))) : ?>
      <div>
        <p class="note"><?php echo the_field('faq_description'); ?></p>
      </div>
    <?php endif; ?>
    <div>
      <p class="metadata-title"><strong>Topics:</strong></p>
      <p><?php 
        foreach(get_the_tags($current_post_id) as $tag) :
          echo '<a class="topic-tag" href="' . home_url( '/?s=&search-type-home=site&fwp_categories=', 'https' ) . $tag->slug . '">' . $tag->name . '</a>';
        endforeach; ?>
      </p>
    </div>
  </div>