<?php
  $fields     = apply_filters('acfbs_options_fields', []);
  $features   = apply_filters('acfbs_options_features', []);
  $config     = apply_filters('acfbs_config', []);
  $isLiteMode = (isset($config['lite_mode']) && $config['lite_mode']);
?>
<div class="acfbsPage__widget">
  <h3 class="acfbsPage__widgetTitle">
    <?= __('Settings', 'acf-better-search'); ?>
  </h3>
  <div class="acfbsContent">
    <div class="acfbsPage__widgetRow">
      <h4><?= __('List of supported fields types', 'acf-better-search'); ?></h4>
      <?php include ACFBS_PATH . '/resources/components/settings/fields.php'; ?>
    </div>
    <div class="acfbsPage__widgetRow">
      <h4><?= __('Additional features', 'acf-better-search'); ?></h4>
      <?php include ACFBS_PATH . '/resources/components/settings/features.php'; ?>
    </div>
    <div class="acfbsPage__widgetRow">
      <button type="submit" name="acfbs_save"
        class="acfbsButton acfbsButton--green"><?= __('Save Changes', 'acf-better-search'); ?></button>
    </div>
  </div>
</div>