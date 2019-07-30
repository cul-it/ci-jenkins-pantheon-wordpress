<table class="acfbsPage__widgetTable">
  <?php
    foreach ($features as $value => $label) :
      $isChecked = (isset($config[$value]) && $config[$value]);
  ?>
    <tr>
      <td>
        <input type="checkbox" name="acfbs_features[]" value="<?= $value; ?>"
          id="acfbs-<?= $value; ?>" class="acfbsPage__checkbox" <?= $isChecked ? 'checked' : ''; ?>
          <?= ($isLiteMode && in_array($value, ['selected_mode'])) ? 'disabled' : ''; ?>>
        <label for="acfbs-<?= $value; ?>"></label>
      </td>
      <td>
        <label for="acfbs-<?= $value; ?>" class="acfbsPage__checkboxLabel"><?= $label; ?></label>
      </td>
    </tr>
  <?php endforeach; ?>
</table>