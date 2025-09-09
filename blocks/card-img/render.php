<?php
$items        = $attributes['items'] ?? [];
$show_read    = $attributes['showReadLink'] ?? false;
$global_read  = $attributes['readLinkText'] ?? 'Read more';
$header_title = $attributes['title'] ?? '';
$description  = $attributes['intro'] ?? '';
$animation  = (isset($attributes['animation']) && $attributes['animation'] === 'On') ? 'belt' : '';
$items_count  = 1;
?>
<div class="container py-4">
  <section class="food-section">
    <header class="section-header ">
      <?php if ($header_title) : ?>
        <h2 class="section-title"><?php echo esc_html($header_title); ?></h2>
      <?php endif; ?>

      <?php if ($description) : ?>
        <p class="section-subtitle"><?php echo esc_html($description); ?></p>
      <?php endif; ?>
    </header>
    
    <div class="row g-4 justify-content-center mt-2"></div>    
    <?php if (!empty($items)) : ?>
      <div class="food-grid<?php echo $animation ? ' belt' : ''; ?>" <?php echo $animation ? 'id="belt"' : ''; ?>>
        <?php foreach ($items as $card) :
          $img         = $card['image']   ?? '';
          $h           = $card['heading'] ?? '';
          $t           = $card['text']    ?? '';
          $link        = $card['link']['url']   ?? ($card['link'] ?? '');
          $link_title  = $card['link']['title'] ?? $global_read;
        ?>
          <article class="food-card ">
            <figure class="food-card__media">
              <?php if ($img) : ?> 
                <img
                  src="<?php echo esc_url($img); ?>"
                  alt="<?php echo esc_attr($h ?: ''); ?>"
                  loading="lazy"
                  decoding="async" 
                />
              <?php else : ?>
                <div class="food-card__media--empty" aria-hidden="true"></div>
              <?php endif; ?>
            </figure>

            <div class="food-card__content">
              <?php if ($h) : ?>
                <h3 class="food-card__title"><?php echo esc_html($h); ?></h3>
              <?php endif; ?>

              <?php if ($t) : ?>
                <p class="food-card__text"><?php echo esc_html($t); ?></p>
              <?php endif; ?>

              <?php if ($show_read && $link) : ?>
                <a class="food-card__link" href="<?php echo esc_url($link); ?>">
                  <?php echo esc_html($link_title); ?>
                </a>
              <?php endif; ?>
            </div>
          </article>
        <?php
          $items_count++;
          endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</div>
</div>