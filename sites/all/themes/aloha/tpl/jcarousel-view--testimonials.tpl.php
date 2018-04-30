<?php

/**
 * @file jcarousel-view.tpl.php
 * View template to display a list as a carousel.
 */
?>
<ul class="<?php print $jcarousel_classes; ?>">
  <?php for ($i = 0, $n = count($rows); $i < $n; $i += 2): ?>
    <li class="<?php print $row_classes[$i]; ?>">
      <div class="jcarousel-item"><?php print $rows[$i]; ?></div>
      <div class="jcarousel-item"><?php print $rows[$i+1]; ?></div>
    </li>
  <?php endfor; ?>
</ul>
