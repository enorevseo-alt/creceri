  <?php
  $attributes = is_array($attributes ?? null) ? $attributes : [];

  $title     = $attributes['title']     ?? '';
  $intro     = $attributes['intro']     ?? '';
  $ctaText   = $attributes['ctaText']   ?? '';
  $ctaUrl    = $attributes['ctaUrl']    ?? '';
  $items     = is_array($attributes['items'] ?? null) ? $attributes['items'] : [];

  $mediaUrl  = $attributes['mediaUrl']  ?? '';
  $mediaAlt  = $attributes['mediaAlt']  ?? '';
  $mediaSide = ($attributes['mediaSide'] ?? 'right') === 'left' ? 'left' : 'right';
  $cols      = $attributes['cols']      ?? ['xs'=>1,'sm'=>3,'lg'=>3];

  $flip = $mediaSide === 'left' ? ' flex-md-row-reverse' : '';

  $col_xs = max(1, (int)($cols['xs'] ?? 1));
  $col_sm = max(1, (int)($cols['sm'] ?? 3));
  $col_lg = max(1, (int)($cols['lg'] ?? 3));
  $class_xs = 'col-'    . (int) floor(12 / $col_xs);
  $class_sm = 'col-sm-' . (int) floor(12 / $col_sm);
  $class_lg = 'col-lg-' . (int) floor(12 / $col_lg);

  $uid = 'sf-' . wp_unique_id();
  ?>

  <section id="<?php echo esc_attr($uid); ?>" class="split-features alignwide">
    <div class="container px-0">
      <div class="row align-items-center g-4<?php echo esc_attr($flip); ?>">

        <div class="col-12 col-md-7">
          <?php if ($title): ?>
            <h2 class="fw-bold mb-2"><?php echo esc_html($title); ?></h2>
          <?php endif; ?>
          <?php if ($intro): ?>
            <p class="section-intro text-muted mb-3"><?php echo esc_html($intro); ?></p>
          <?php endif; ?>

          <?php if ($items): ?>
            <div class="row g-2 g-md-3">
              <?php foreach ($items as $it):
                $icon = $it['icon'] ?? '';
                $h    = $it['heading'] ?? '';
                $t    = $it['text'] ?? '';
                $u    = $it['url'] ?? '';
              ?>
              <div class="<?php echo esc_attr("$class_xs $class_sm $class_lg"); ?>">
                <div class="feat-card card h-100 border-0 shadow-lg position-relative">
                  <div class="card-body d-flex align-items-start gap-2">
                    <?php if ($icon): ?>
                      <img class="feat-icon" src="<?php echo esc_url($icon); ?>" alt="<?php echo esc_attr($h ?: 'card image'); ?>" aria-hidden="true" loading="lazy" decoding="async">
                    <?php endif; ?>
                    <div class="feat-copy">
                      <?php if ($h): ?><h3 class="h6 fw-bold mb-2 text-danger"><?php echo esc_html($h); ?></h3><?php endif; ?>
                      <?php if ($t): ?><p class="mb-0 text-muted small fw-semibold sc-excerpt"><?php echo esc_html($t); ?></p><?php endif; ?>
                    </div>
                  </div>
                  <?php if ($u): ?>
                    <a class="stretched-link" href="<?php echo esc_url($u); ?>" aria-label="<?php echo esc_attr($h ?: 'Open'); ?>"></a>
                  <?php endif; ?>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if ($ctaText && $ctaUrl): ?>
            <div class="mt-3">
              <a class="btn btn-outline-danger btn-sm" href="<?php echo esc_url($ctaUrl); ?>"><?php echo esc_html($ctaText); ?></a>
            </div>
          <?php endif; ?>
        </div>

        <div class="col-12 col-md-5">
          <?php if ($mediaUrl): ?>
            <figure class="split-features__media m-0">
              <img src="<?php echo esc_url($mediaUrl); ?>"
                  alt="<?php echo esc_attr($mediaAlt); ?>"
                  loading="lazy" decoding="async" draggable="false">
            </figure>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </section>
