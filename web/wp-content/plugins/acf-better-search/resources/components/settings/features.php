<table class="acfbsPage__widgetTable">
  <?php
    foreach ($features as $value => $feature) :
      $isChecked = (isset($config[$value]) && $config[$value]);
  ?>
    <tr>
      <td>
        <input type="checkbox" name="acfbs_features[]" value="<?= $value; ?>"
          id="acfbs-<?= $value; ?>" class="acfbsPage__checkbox" <?= $isChecked ? 'checked' : ''; ?>
          <?= (!$feature['is_active']) ? 'disabled' : ''; ?>>
        <label for="acfbs-<?= $value; ?>"></label>
      </td>
      <td>
        <label for="acfbs-<?= $value; ?>" class="acfbsPage__checkboxLabel"><?= $feature['label']; ?></label>
      </td>
    </tr>
  <?php endforeach; ?>
</table>