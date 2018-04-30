<?php

/**
 * @file
 * Default simple view template to display a list of rows.
 *
 * @ingroup views_templates
 */
?>
<?php if (!empty($title)): ?>
  <h3><?php print $title; ?></h3>
<?php endif; ?>
<?php foreach ($rows as $id => $row): ?>
  <div class="col-3<?php print $classes_array[$id] ? ' ' . $classes_array[$id] : ''; ?>">
    <div class="view-row-inner">
      <?php print $row; ?>
    </div>
  </div>
<?php endforeach; ?>
