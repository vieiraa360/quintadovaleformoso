<?php
/**
 * @file
 * Flatize's theme implementation to display a view unformatted -
 * bootstrap slideshow.
 */
?>
<?php if (!empty($title)): ?>
  <h3><?php print $title; ?></h3>
<?php endif; ?>
<div id="view-carousel-generic" class="bootstrap-slideshow carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <?php foreach ($rows as $id => $row): ?>
      <li data-target="#view-carousel-generic" data-slide-to="<?php print $id;?>" class="<?php print $id === 0 ? "active" : "";?>"></li>
    <?php endforeach;?>
  </ol>
  
    <div class="carousel-inner">
      <?php foreach ($rows as $id => $row): ?>
        <div<?php print $classes_array[$id] ? ' class="' . $classes_array[$id] . ' item ' . ($id === 0 ? "active" : "") . '"' : ''; ?>>
          <?php print $row; ?>
        </div>
      <?php endforeach; ?>
    </div>
  
  <a class="left carousel-control" href="#view-carousel-generic" data-slide="prev">
    <i class="fa fa-angle-left"></i>
  </a>
  <a class="right carousel-control" href="#view-carousel-generic" data-slide="next">
    <i class="fa fa-angle-right"></i>
  </a>
</div>
