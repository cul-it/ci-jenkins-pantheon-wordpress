<?php
  $path = sprintf('%s&_wpnonce=%s', menu_page_url('acfbs_admin_page', false), wp_create_nonce('acfbs-save'));
?>
<form method="post" action="<?= $path; ?>" class="acfbsPage">
  <div class="acfbsPage__inner">
    <h1 class="acfbsPage__headline"><?= __('ACF: Better Search', 'acf-better-search'); ?></h1>
    <ul class="acfbsPage__columns">
      <li class="acfbsPage__column acfbsPage__column--large">
        <?php if ($_POST) : ?>
          <div class="acfbsPage__alert"><?= __('Changes were successfully saved!', 'acf-better-search'); ?></div>
        <?php endif; ?>
        <?php
          include ACFBS_PATH . '/resources/components/widgets/settings.php';
        ?>
      </li>
      <li class="acfbsPage__column acfbsPage__column--small">
        <?php
          include ACFBS_PATH . '/resources/components/widgets/about.php';
          include ACFBS_PATH . '/resources/components/widgets/support.php';
          include ACFBS_PATH . '/resources/components/widgets/donate.php';
        ?>
      </li>
    </ul>
  </div>
</form>