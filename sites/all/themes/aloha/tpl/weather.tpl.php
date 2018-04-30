<?php
/**
 * @file
 * Template for the weather module.
 */
?>

<div class="weather">
  <?php foreach($weather as $place): ?>
    <!-- <p style="clear:left"><strong><?php print $place['link']; ?></strong></p> -->
    <?php if (empty($place['forecasts'])): ?>
      <?php print(t('Currently, there is no weather information available.')); ?>
    <?php endif ?>
    <?php foreach($place['forecasts'] as $date => $forecast): ?>
      <?php foreach($forecast['time_ranges'] as $time_range => $data): ?>

        <div class="weather-date-info">
          <span class="weather-date">
          <?php if ($date == date('Y-m-d')): ?>
            <?php echo t('Today'); ?>
          <?php elseif ($date == date('Y-m-d', time() + 86400)): ?>
            <?php echo t('Tomorrow'); ?>
          <?php else : ?>
            <?php echo date('l', strtotime($date)); ?>
          <?php endif; ?>
          </span>

          <span class="weather-tempature"><?php print $data['temperature']; ?></span>
        </div>
      <?php endforeach; ?>
    <?php endforeach; ?>
  <?php endforeach; ?>
</div>
