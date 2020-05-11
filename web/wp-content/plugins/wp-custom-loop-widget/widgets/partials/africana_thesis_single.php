<?php
$current_post_id = get_the_ID();
  ?>

    <div class="database_metadata">
    <?php if (!empty(get_field('author'))) : ?>
      <div>
        <p class="metadata-title description"><strong>Author:</strong></p>
        <p><?php echo the_field('author'); ?></p>
      </div>
    <?php endif; ?>
    <?php if (!empty(get_field('degree_date'))) : ?>
      <div>
        <p class="metadata-title"><strong>Degree Date:</strong></p>
        <p><?php echo the_field('degree_date'); ?></p>
      </div>
    <?php endif; ?>
    <?php if (!empty(get_field('committee_chairperson'))) : ?>
      <div>
        <p class="metadata-title"><strong>Committee Chairperson:</strong></p>
        <p><?php echo the_field('committee_chairperson'); ?></p>
      </div>
    <?php endif; ?>
  <?php if (!empty(get_field('call_number'))) : ?>
      <div>
        <p class="metadata-title"><strong>Call Number:</strong></p>
        <p><?php echo the_field('call_number'); ?></p>
      </div>
    <?php endif; ?>
    <?php if (!empty(get_field('description'))) : ?>
      <div>
        <p class="metadata-title"><strong>Description:</strong></p>
        <p><?php echo the_field('description'); ?></p>
      </div>
    <?php endif; ?>
    <?php if (!empty(get_field('abstract'))) : ?>
      <div>
        <p class="metadata-title"><strong>Abstract:</strong></p>
        <p><?php echo the_field('abstract'); ?></p>
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