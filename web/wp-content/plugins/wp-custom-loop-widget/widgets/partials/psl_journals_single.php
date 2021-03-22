<div class="card">
  <div class="card-body">
    <p class="card-title"><strong><?php if (!empty(get_field('link'))) : ?><a href="<?php echo the_field('link'); ?>"><?php endif; ?><?php echo the_field('name'); ?><?php if (!empty(get_field('link'))) : ?></a><?php endif; ?></strong></p>
    <div class="metadata">
      <p class="description">
        <?php if (!empty(get_field('bibid'))) : ?>Catalog: <a href="https://newcatalog.library.cornell.edu/catalog/<?php echo the_field('bibid'); ?>"><?php echo the_field('bibid'); ?></a><?php endif; ?>
      </p>
      <p><?php if (!empty(get_field('publisher'))) : ?>Publisher: <?php echo the_field('publisher'); ?><br /><?php endif; ?></p>
      <p><?php if (!empty(get_field('description'))) : ?><?php echo the_field('description'); endif; ?></p>
    </div>
  </div>
</div>