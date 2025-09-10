<?php
  // Required attributes only
  $header_title = $attributes['header'] ?? '';
  $sub_title    = $attributes['sub-title'] ?? '';
  $items        = $attributes['items'] ?? [];
?>
<section class="container">
  <!-- Header -->
  <div class="text-center mb-4">
    <?php if ($header_title !== ''): ?>
      <h2 class="fw-bold mb-2"><?php echo esc_html($header_title); ?></h2>
    <?php endif; ?>
    <?php if ($sub_title !== ''): ?>
      <p class="text-muted mb-0"><?php echo esc_html($sub_title); ?></p>
    <?php endif; ?>
  </div>

  <!-- Timeline -->
  <?php if (!empty($items)) : ?>
    <div class="timeline">
      <?php
        $count = count($items);
        $index = 0;
        foreach ($items as $item) :
          $image = $item['image'] ?? '';
          $text  = $item['text'] ?? '';
          $index++;
      ?>
        <div class="item">
          <?php if ($image): ?>
            <img src="<?php echo esc_url($image); ?>" alt="icon">
          <?php endif; ?>
          <?php if ($text !== ''): ?>
            <p class="mb-0"><?php echo esc_html($text); ?></p>
          <?php endif; ?>
        </div>

        <?php if ($index < $count): ?>
          <div class="divider"></div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
