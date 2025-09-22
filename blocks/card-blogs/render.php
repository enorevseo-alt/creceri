<?php
/**
 * Dynamic render for Tek Stories block (with overlay & color controls).
 */

$items          = $attributes['items'] ?? [];
$title          = $attributes['title'] ?? 'Tek Stories';
$intro          = $attributes['intro'] ?? '';
$cta_text       = $attributes['ctaText'] ?? 'Browse All Stories';
$cta_url        = $attributes['ctaUrl']  ?? '#';
$show_read      = (bool)($attributes['showReadLink'] ?? true);
$read_label     = $attributes['readLinkText'] ?? 'Read More';
$total_to_show  = (int)($attributes['data_count'] ?? 4); // total including the feature
$belt_enabled   = (isset($attributes['animation']) && $attributes['animation'] === 'On');

/** NEW: style controls */
$cta_bg         = $attributes['ctaBgColor']     ?? '#962E2A';
$section_bg     = $attributes['sectionBgColor'] ?? '';

$overlay_enabled= (bool)($attributes['overlayEnabled'] ?? true);
$ov_color       = $attributes['overlayColor']     ?? '#000000';
$ov_opacity     = isset($attributes['overlayOpacity']) ? floatval($attributes['overlayOpacity']) : 0.55; // 0..1
$ov_size        = isset($attributes['overlaySize'])    ? intval($attributes['overlaySize'])    : 60;    // %
$ov_position    = $attributes['overlayPosition']  ?? 'bottom'; // "top"|"bottom"
$image_pos      = $attributes['imagePosition']    ?? 'center';

/** Normalize + limit items */
$items = array_values(array_filter(
  (array)$items,
  fn($it) => !empty($it['image']) || !empty($it['heading']) || !empty($it['text'])
));
if ($total_to_show > 0) {
  $items = array_slice($items, 0, $total_to_show);
}

/** Split feature and list */
$feature = $items ? array_shift($items) : null;

/** Helpers */
function tek_hex2rgba($hex, $alpha = 1.0) {
  $hex = preg_replace('/[^0-9a-fA-F]/', '', (string)$hex);
  if (strlen($hex) === 3) {
    $r = hexdec(str_repeat($hex[0], 2));
    $g = hexdec(str_repeat($hex[1], 2));
    $b = hexdec(str_repeat($hex[2], 2));
  } else {
    $hex = str_pad($hex, 6, '0', STR_PAD_RIGHT);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
  }
  $alpha = max(0, min(1, floatval($alpha)));
  return "rgba($r,$g,$b,$alpha)";
}

$media_style = function($url, $item = null) use ($overlay_enabled, $ov_color, $ov_opacity, $ov_size, $ov_position, $image_pos) {
  if (!$url) return '';

  // Per-item overrides: items[n].overlay = { enabled, color, opacity, size, position }
  $ov = (is_array($item) && !empty($item['overlay']) && is_array($item['overlay'])) ? $item['overlay'] : [];

  $enabled  = array_key_exists('enabled', $ov) ? (bool)$ov['enabled'] : $overlay_enabled;
  $color    = $ov['color']    ?? $ov_color;
  $opacity  = array_key_exists('opacity', $ov) ? floatval($ov['opacity']) : $ov_opacity;
  $size     = array_key_exists('size',    $ov) ? intval($ov['size'])    : $ov_size;
  $position = $ov['position'] ?? $ov_position;

  $dir  = ($position === 'top') ? 'to bottom' : 'to top';
  $rgba = tek_hex2rgba($color, $opacity);

  $bg_img = 'url(' . esc_url($url) . ')';
  if ($enabled && $opacity > 0) {
    $grad = "linear-gradient($dir, $rgba 0%, rgba(0,0,0,0) {$size}%)";
    $bg_layer = esc_attr("$grad, $bg_img");
  } else {
    $bg_layer = esc_attr($bg_img);
  }

  $pos = esc_attr($image_pos);
  return 'style="background-image:' . $bg_layer . ';background-position:' . $pos . ';background-size:cover;background-repeat:no-repeat;"';
};

$get_url = function($node) {
  return $node['link']['url'] ?? $node['url'] ?? '';
};
?>

<section class="wrap" aria-labelledby="tek-stories-title" <?php echo $section_bg ? 'style="background-color:' . esc_attr($section_bg) . ';"' : ''; ?>>
  <div class="stories-header">
    <?php if ($title) : ?>
      <h2 id="tek-stories-title" class="stories-title"><?php echo esc_html($title); ?></h2>
    <?php endif; ?>
  </div>

  <div class="stories-intro" style="display:flex;align-items:center;justify-content:space-between;gap:2rem;">
    <?php if ($intro) : ?>
      <p class="stories-sub" style="margin-bottom:0;"><?php echo esc_html($intro); ?></p>
    <?php endif; ?>

    <?php if ($cta_text && $cta_url) : ?>
      <div class="stories-actions" style="flex-shrink:0;">
        <a class="btn" href="<?php echo esc_url($cta_url); ?>" style="background-color:<?php echo esc_attr($cta_bg); ?>!important;">
          <?php echo esc_html($cta_text); ?>
        </a>
      </div>
    <?php endif; ?>
  </div>

  <div class="grid">
    <!-- Feature (first item) -->
    <?php if ($feature) :
      $feature_url = $get_url($feature);
    ?>
      <article class="feature">
        <div class="feature-media" <?php echo $media_style($feature['image'] ?? '', $feature); ?>></div>

        <div class="feature-body">
          <?php if (!empty($feature['heading'])) : ?>
            <h3 class="feature-title"><?php echo esc_html($feature['heading']); ?></h3>
          <?php endif; ?>

          <?php if (!empty($feature['text'])) : ?>
            <p class="feature-text"><?php echo esc_html($feature['text']); ?></p>
          <?php endif; ?>

          <?php if ($show_read && $feature_url) : ?>
            <div class="feature-actions">
              <a href="<?php echo esc_url($feature_url); ?>" class="link-btn">
                <?php echo esc_html($read_label); ?> <span class="chev">›</span>
              </a>
            </div>
          <?php endif; ?>
        </div>
      </article>
    <?php endif; ?>

    <!-- Compact list (remaining items) -->
    <div
      class="list<?php echo $belt_enabled ? ' belt' : ''; ?>"
      <?php echo $belt_enabled ? 'id="belt"' : ''; ?>
    >
      <?php foreach ($items as $item) :
        $item_url = $get_url($item);
      ?>
        <article class="item">
          <div class="thumb" aria-hidden="true" <?php echo $media_style($item['image'] ?? '', $item); ?>></div>

          <div class="item-data">
            <?php if (!empty($item['heading'])) : ?>
              <h4><?php echo esc_html($item['heading']); ?></h4>
            <?php endif; ?>

            <?php if (!empty($item['text'])) : ?>
              <p><?php echo esc_html($item['text']); ?></p>
            <?php endif; ?>

            <?php if ($show_read && $item_url) : ?>
              <a href="<?php echo esc_url($item_url); ?>" class="link-btn">
                <?php echo esc_html($read_label); ?> <span class="chev">›</span>
              </a>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
