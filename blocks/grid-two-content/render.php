<?php
/**
 * Renders: child/travel-tips
 */

$title    = $attributes['title']    ?? '';
$subtitle = $attributes['subtitle'] ?? '';
$dos      = $attributes['do_items'] ?? [];
$donts    = $attributes['dont_items'] ?? [];
?>

<div class="travel-tips container py-5" >
  <?php if ($title || $subtitle): ?>
    <div class="text-center mb-4">
      <?php if ($title): ?>
        <h2 class="fw-bold mb-2"><?php echo esc_html($title); ?></h2>
      <?php endif; ?>
      <?php if ($subtitle): ?>
        <p class="text-muted"><?php echo esc_html($subtitle); ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="tips-container" >
    <!-- Do Column -->
    <div class="col-md-6 tips-column do">
      <h3>Do:</h3>
      <ul class="list-unstyled">
        <?php foreach ($dos as $item): ?>
          <li class="d-flex align-items-start mb-2">
            <span class="icon-circle success me-2 " > <i class="fa fa-check"></i></span>
            <span><?php echo esc_html($item); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Don't Column -->
    <div class="col-md-6 tips-column dont">
      <h3>Don’t:</h3>
      <ul class="list-unstyled">
        <?php foreach ($donts as $item): ?>
          <li class="d-flex align-items-start mb-2">
            <span class="icon-circle danger me-2"><i class="fa fa-times"></i></span>
            <span><?php echo esc_html($item); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>
