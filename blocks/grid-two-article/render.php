<?php
    /**
     * Renders: child/grid-two-article
     * Background supports: none | solid | gradient
     */

    $cards        = $attributes['category_sub_title'] ?? [];
    $title        = $attributes['title']        ?? '';
    $subtitle     = $attributes['sub-title']    ?? ''; // keeping your original key
    $intro        = $attributes['intro']        ?? ''; // not printed here, but left in case you add later

    // ---- Background attributes (new) ----
    $bgType       = $attributes['backgroundType']  ?? 'none';   // none | solid | gradient
    $bgValue      = $attributes['backgroundValue'] ?? '';       // hex for solid
    $bgDirection  = $attributes['gradientDirection'] ?? 'to bottom'; // e.g. 'to bottom', 'to right', 'to bottom right'
    $bgColors     = $attributes['gradientColors']  ?? [];       // array of color strings

    // Whitelist gradient directions to avoid arbitrary CSS injection
    $allowed_dirs = [
    'to bottom','to top','to right','to left',
    'to bottom right','to bottom left','to top right','to top left'
    ];
    if (!in_array($bgDirection, $allowed_dirs, true)) {
    $bgDirection = 'to bottom';
    }

    // Build background style safely
    $bgStyle = 'background:#fff;'; // default when "none"
    if ($bgType === 'solid') {
    // Try sanitizing a hex; if it fails, keep white
    if (function_exists('sanitize_hex_color')) {
        $hex = sanitize_hex_color($bgValue);
        if ($hex) $bgStyle = 'background:' . $hex . ';';
    } else {
        $bgStyle = 'background:' . esc_attr($bgValue) . ';';
    }
    } elseif ($bgType === 'gradient') {
    // Accept 2–4 colors; sanitize each hex (fallback to white if bad)
    $colors = array_values(array_filter(array_map(function($c){
        if (function_exists('sanitize_hex_color')) {
        $hex = sanitize_hex_color($c);
        return $hex ?: null;
        }
        return $c ?: null;
    }, (array)$bgColors)));

    if (count($colors) >= 2) {
        $bgStyle = 'background:linear-gradient(' . esc_attr($bgDirection) . ', ' . esc_attr(implode(',', $colors)) . ');';
    }
    }

    // Utility: print title allowing <br> etc., but safely
    $print_title = function($html){
    // allow basic inline tags; <br> will render correctly
    return wp_kses_post($html);
    };

?>
<div class="container py-5" style="<?php echo esc_attr($bgStyle); ?>">
    <?php if ($title || $subtitle): ?>
    <h2 class="grid-two-article-title header-content pb-4">
      <?php if ($title): ?>
        <?php echo $print_title($title); ?>
      <?php endif; ?>
      <?php if ($subtitle): ?>
        <span class="grid-two-article-subtitle">
          <?php echo $print_title($subtitle); ?>
        </span>
      <?php endif; ?>
    </h2>
  <?php endif; ?>

  <div class="container-grid row g-4 justify-content-center">
    <?php if (!empty($cards)) : ?>
      <?php foreach ($cards as $card) :
        $count   = 0;
        $heading = $card['heading'] ?? '';
        $country = $card['country'] ?? '';
        $url     = $card['url']     ?? '';
        $items   = $card['items']   ?? [];
      ?>
      <div class="col-12 col-lg-6">
        <div class="card border-0 rounded-4">
          <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
            <h4 class="grid-two-article-card-heading border-bottom pb-3 mb-3">
              <?php echo esc_html($heading); ?>
              <?php if ($country): ?>
                <a href="#" class="topic-link"><?php echo esc_html($country); ?></a>
              <?php endif; ?>
            </h4>
          </div>
          <div class="card-body pt-3 pb-4 px-4">
            <?php if (!empty($items)) : ?>
              <?php foreach ($items as $it) :
                $count++;
                $img          = $it['image']        ?? '';
                $heading_data = $it['heading_data'] ?? '';
                $sh           = $it['sub-header']   ?? '';
                $t            = $it['text']         ?? '';
              ?>
              <a href="<?php echo esc_url($url); ?>" class="text-decoration-none text-dark">
                <div class="d-flex align-items-start gap-3 py-3 position-relative">
                  <span class="num-badge"><?php echo esc_html($count); ?></span>
                  <?php if ($img): ?>
                    <img class="item-thumb" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($heading_data ?: ''); ?>">
                  <?php endif; ?>
                  <div>
                    <?php if ($heading_data): ?>
                      <h3 class="grid-two-article-item-heading">
                        <?php echo esc_html($heading_data); ?>
                      </h3>
                    <?php endif; ?>
                    <?php if ($sh): ?>
                      <div class="grid-two-article-country">
                        <?php echo esc_html($sh); ?>
                      </div>
                    <?php endif; ?>
                    <?php if ($t): ?>
                      <p class="grid-two-article-desc">
                        <?php echo esc_html($t); ?>
                      </p>
                    <?php endif; ?>
                  </div>
                </div>
              </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
