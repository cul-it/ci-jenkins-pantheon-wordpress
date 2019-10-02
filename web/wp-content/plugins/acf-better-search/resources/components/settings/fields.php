<table class="acfbsPage__widgetTable">
  <?php
    foreach ($fields as $value => $label) :
      $isChecked = (isset($config['fields_types']) && in_array($value, $config['fields_types']));
  ?>
    <tr>
      <td>
        <input type="checkbox" name="acfbs_fields_types[]" value="<?= $value; ?>"
          id="acfbs-<?= $value; ?>" class="acfbsPage__checkbox" <?= $isChecked ? 'checked' : ''; ?>
          <?= ($isLiteMode) ? 'disabled' : ''; ?>>
        <label for="acfbs-<?= $value; ?>"></label>
      </td>
      <td>
        <label for="acfbs-<?= $value; ?>" class="acfbsPage__checkboxLabel"><?= $label; ?></label>
      </td>
    </tr>
  <?php endforeach; ?>
</table>