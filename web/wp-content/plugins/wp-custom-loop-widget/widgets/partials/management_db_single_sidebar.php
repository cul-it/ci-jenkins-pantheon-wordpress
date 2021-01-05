<?php $current_post_id = get_the_ID();
  ?>

    <div class="database_metadata">
    <?php if( !empty(get_field('database_url'))) : ?>
    <div class="button">
      <a href="<?php echo the_field('database_url') ?>"><button>Connect to <?php echo the_field('database_title'); ?> <span class="fa fa-external-link"></span></button></a>
    </div>
  <?php endif; ?>
  <?php if (!empty(get_field('database_status'))) : ?>
      <div>
        <p class="status"><?php 
          $status_arg = get_field('database_status');
          if ($status_arg) : ?>
            <ul>
              <?php foreach ($status_arg as $status) : ?>
                <li>
                  <?php if ($status == 'Account required') : ?>
                    <span class="fa fa-user"></span>
                  <?php elseif ($status == 'Alumni access') : ?>
                    <span class="fa fa-graduation-cap"></span>
                  <?php elseif ($status == 'Off campus access') : ?>
                    <span class="fa fa-globe"></span>
                  <?php elseif ($status == 'On campus only') : ?>
                    <span class="fa fa-building"></span>
                  <?php elseif ($status == 'Restricted access') : ?>
                    <span class="fa fa-lock"></span>
                  <?php endif; ?>
                  <?php echo $status; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </p>
      </div>
    <?php endif; ?>
    <?php if (!empty(get_field('database_technology_note'))) : ?>
      <div>
        <p class="note"><?php echo the_field('database_technology_note'); ?></p>
      </div>
    <?php endif; ?>
    <?php if (!empty(get_field('database_scope_note'))) : ?>
      <div>
        <p class="note"><?php echo the_field('database_scope_note'); ?></p>
      </div>
    <?php endif; ?>
  </div>