<?php
// Attributes
$title = $attributes['title'] ?? '';
$paragraph  = $attributes['paragraph']  ?? '';
$headline = $attributes['headline'] ?? '';
$subhead  = $attributes['subhead']  ?? '';
$caption  = $attributes['caption']  ?? '';

$alignPref = $attributes['alignTextCards'] ?? 'left';

// Features (for legacy layouts)
$featuresRaw = $attributes['features'] ?? [];
$features = is_array($featuresRaw) ? array_values(array_filter($featuresRaw, function($f){
  return is_array($f) && (!empty($f['title']) || !empty($f['desc']) || !empty($f['icon']));
})) : [];

// Background settings
$bgMode   = $attributes['bgMode'] ?? 'plain';
$bgColor  = $attributes['bgColor'] ?? '#ffffff';
$fromRaw  = $attributes['bgGradientFrom'] ?? '';
$toRaw    = $attributes['bgGradientTo']   ?? '';
$dir      = $attributes['bgGradientDir']  ?? 'to bottom';
$gradFrom = (is_string($fromRaw) && trim($fromRaw) !== '') ? $fromRaw : '#ffffff';
$gradTo   = (is_string($toRaw)   && trim($toRaw)   !== '') ? $toRaw   : '#ffffff';

// Cards (legacy)
$cards = (isset($attributes['cards']) && is_array($attributes['cards'])) ? $attributes['cards'] : [];
$count = count($cards);

// New: flexible 4-slot items (image/text mix)
$slotsRaw = $attributes['slots'] ?? [];
$slots = [];
if (is_array($slotsRaw)) {
  foreach ($slotsRaw as $it) {
    if (!is_array($it)) continue;
    $type = isset($it['type']) && in_array($it['type'], ['image','text'], true) ? $it['type'] : 'text';
    $clean = ['type' => $type];
    if ($type === 'image') {
      $clean['image']   = isset($it['image']) ? (string)$it['image'] : '';
      $clean['alt']     = isset($it['alt']) ? (string)$it['alt'] : '';
      $clean['title']   = isset($it['title']) ? (string)$it['title'] : '';
      $clean['caption'] = isset($it['caption']) ? (string)$it['caption'] : '';
      $clean['url']     = isset($it['url']) ? (string)$it['url'] : '';
    } else {
      $clean['title']     = isset($it['title']) ? (string)$it['title'] : '';
      $clean['paragraph'] = isset($it['paragraph']) ? (string)$it['paragraph'] : '';
      $clean['bullets']   = (isset($it['bullets']) && is_array($it['bullets'])) ? array_values(array_filter($it['bullets'], function($v){ return is_string($v) && trim($v) !== ''; })) : [];
      $clean['sections']  = (isset($it['sections']) && is_array($it['sections'])) ? $it['sections'] : [];
    }
    $slots[] = $clean;
    if (count($slots) >= 4) break; // cap at 4
  }
}

// Determine layout
$layoutMode = $attributes['layoutMode'] ?? 'auto';
if ($layoutMode === 'auto') {
  if (!empty($slots)) {
    $layoutMode = (count($slots) === 3) ? 'flex3' : 'flex4';
  } else {
    $layoutMode = ($count <= 1) ? 'single' : (($count == 2) ? 'stacked2' : 'grid3');
  }
}

$useMix4 = !empty($attributes['mixImageText']) && $count >= 2 && !empty($features);
if ($useMix4 && $layoutMode !== 'flex4') {
  $layoutMode = 'mix4'; // keep legacy mixed option available
}

// Variant flag: hero image + two text cards (3 slots pattern: image, text, text)
$isHero2Text = false;
if ($layoutMode === 'flex4' && count($slots) >= 3) {
  $t1 = $slots[0]['type'] ?? '';
  $t2 = $slots[1]['type'] ?? '';
  $t3 = $slots[2]['type'] ?? '';
  $isHero2Text = ($t1 === 'image' && $t2 === 'text' && $t3 === 'text');
}

// Background inline style
$style_bg = '';
if ($bgMode === 'solid') {
  $style_bg = 'background-color:' . esc_attr($bgColor) . ';';
} elseif ($bgMode === 'gradient') {
  $keywords = ['to bottom','to top','to right','to left','to top right','to top left','to bottom right','to bottom left'];
  if (!in_array($dir, $keywords, true) && !preg_match('/^\d+(\.\d+)?deg$/', $dir)) $dir = 'to bottom';
  $style_bg = 'background-image: linear-gradient(' . esc_attr($dir) . ',' . esc_attr($gradFrom) . ',' . esc_attr($gradTo) . ');';
}

$desktopReverse = ($alignPref === 'right') ? ' flex-lg-row-reverse' : '';

$wrapper = get_block_wrapper_attributes([
  'class' => 'tc-block tc-flush py-5 tc-layout-' . $layoutMode,
  'style' => $style_bg
]);

$uid = wp_unique_id('tcblk-');
ob_start(); ?>
<section <?php echo $wrapper; ?>>
  <div class="container">
    <?php if ($layoutMode === 'flex4' || $layoutMode === 'flex3'): ?>
      <?php $flexClass = ($layoutMode === 'flex3') ? 'tc-flex3' : 'tc-flex4'; ?>
      <div class="<?php echo esc_attr($flexClass); ?> <?php echo 'tc-items-' . (int)count($slots); ?><?php echo $isHero2Text || $layoutMode==='flex3' ? ' tc-hero-2text' : ''; ?>">
        <?php foreach ($slots as $item): $type = $item['type'] ?? 'text'; ?>
          <?php if ($type === 'image'): 
            $img = $item['image'] ?? ''; $alt = $item['alt'] ?? ''; $ttl = $item['title'] ?? ''; $cap = $item['caption'] ?? ''; $url = $item['url'] ?? '';
          ?>
            <div class="tc-item type-image">
              <div class="tc-card shadow-sm">
                <?php if ($url): ?><a href="<?php echo esc_url($url); ?>" class="stretched-link" aria-label="<?php echo esc_attr($ttl ?: 'Image card'); ?>"></a><?php endif; ?>
                <div class="tc-card-media">
                  <?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($alt ?: $ttl); ?>" class="w-100 h-100 object-fit-cover"><?php else: ?><div class="tc-card-ph w-100 h-100"></div><?php endif; ?>
                </div>
                <?php if ($ttl || $cap): ?>
                  <div class="tc-card-overlay">
                    <?php if ($ttl): ?><div class="tc-ov-title fw-semibold text-white mb-1"><?php echo esc_html($ttl); ?></div><?php endif; ?>
                    <?php if ($cap): ?><div class="tc-ov-caption text-white-50 small"><?php echo esc_html($cap); ?></div><?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php else: 
            $t  = $item['title'] ?? '';
            $p  = $item['paragraph'] ?? '';
            $bs = (isset($item['bullets']) && is_array($item['bullets'])) ? $item['bullets'] : [];
            $secs = (isset($item['sections']) && is_array($item['sections'])) ? $item['sections'] : [];
          ?>
            <div class="tc-item type-text">
              <div class="tc-textbox h-100">
                <?php if ($t): ?><h3 class="tc-text-title mb-2"><?php echo esc_html($t); ?></h3><?php endif; ?>
                <?php if ($p): ?><p class="mb-3"><?php echo esc_html($p); ?></p><?php endif; ?>
                <?php if (!empty($bs)): ?><ul class="tc-list mb-3"><?php foreach ($bs as $b) { if (is_string($b) && trim($b) !== '') echo '<li>' . esc_html($b) . '</li>'; } ?></ul><?php endif; ?>
                <?php if (!empty($secs)):
                  foreach ($secs as $sec) { $st=$sec['title']??''; $sp=$sec['paragraph']??''; $sb=(isset($sec['bullets'])&&is_array($sec['bullets']))?$sec['bullets']:[]; ?>
                  <?php if ($st): ?><h4 class="tc-text-subtitle mt-3 mb-2"><?php echo esc_html($st); ?></h4><?php endif; ?>
                  <?php if (!empty($sb)): ?><ul class="tc-list mb-3"><?php foreach ($sb as $x) { if (is_string($x) && trim($x) !== '') echo '<li>' . esc_html($x) . '</li>'; } ?></ul><?php endif; ?>
                  <?php if ($sp): ?><p class="mb-0"><?php echo esc_html($sp); ?></p><?php endif; ?>
                <?php } endif; ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="row tc-row<?php echo esc_attr($desktopReverse); ?>">
        <!-- TEXT -->
        <div class="tc-col-text">
          <?php if ($headline): ?><h2 class="tc-headline mb-2"><?php echo esc_html($headline); ?></h2><?php endif; ?>
          <?php if ($subhead):  ?><p class="tc-subhead mb-2"><?php echo esc_html($subhead); ?></p><?php endif; ?>
          <?php if (!empty($features)): ?>
            <ul class="tc-features list-unstyled m-0">
              <?php foreach ($features as $f): $icon=$f['icon']??''; $ttl=$f['title']??''; $desc=$f['desc']??''; ?>
                <li class="tc-feature d-flex gap-2 mb-3">
                  <?php if ($icon): ?><span class="tc-feature-icon" aria-hidden="true"><?php echo esc_html($icon); ?></span><?php endif; ?>
                  <div class="tc-feature-body">
                    <?php if ($ttl):  ?><p class="tc-feature-title fw-semibold mb-1"><?php echo esc_html($ttl); ?></p><?php endif; ?>
                    <?php if ($desc): ?><p class="tc-feature-desc text-muted mb-0"><?php echo esc_html($desc); ?></p><?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
          <?php if ($caption): ?><p class="tc-caption text-muted mb-3"><?php echo esc_html($caption); ?></p><?php endif; ?>
        </div>

        <!-- CARDS -->
        <div class="tc-col-cards">
          <!-- Desktop -->
          <div class="tc-desktop d-none d-lg-block">
            <?php if ($layoutMode === 'single'):
              $c = $cards ? $cards[0] : ['title'=>'','image'=>'','url'=>''];
              $title = $c['title'] ?? ''; $img = $c['image'] ?? ''; $url = $c['url'] ?? '';
            ?>
              <div class="tc-card shadow-sm">
                <?php if ($url): ?><a href="<?php echo esc_url($url); ?>" class="stretched-link" aria-label="<?php echo esc_attr($title ?: 'Card'); ?>"></a><?php endif; ?>
                <div class="tc-card-media">
                  <?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" class="w-100 h-100 object-fit-cover"><?php else: ?><div class="tc-card-ph w-100 h-100"></div><?php endif; ?>
                </div>
                <?php if ($title): ?><div class="p-3"><div class="fw-semibold tc-card-title text-center text-lg-start"><?php echo esc_html($title); ?></div></div><?php endif; ?>
              </div>
            <?php elseif ($layoutMode === 'stacked2'): ?>
              <div class="tc-stack2">
                <?php for ($i=0; $i<2; $i++): $c=$cards[$i]??['title'=>'','image'=>'','url'=>'']; $title=$c['title']??''; $img=$c['image']??''; $url=$c['url']??''; ?>
                  <div class="tc-stack2-item">
                    <div class="tc-card shadow-sm">
                      <?php if ($url): ?><a href="<?php echo esc_url($url); ?>" class="stretched-link" aria-label="<?php echo esc_attr($title ?: 'Card'); ?>"></a><?php endif; ?>
                      <div class="tc-card-media">
                        <?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" class="w-100 h-100 object-fit-cover"><?php else: ?><div class="tc-card-ph w-100 h-100"></div><?php endif; ?>
                      </div>
                      <?php if ($title): ?><div class="p-3"><div class="fw-semibold tc-card-title"><?php echo esc_html($title); ?></div></div><?php endif; ?>
                    </div>
                  </div>
                <?php endfor; ?>
              </div>
            <?php else: /* grid3 */ ?>
              <div class="tc-grid tc-grid-3">
                <?php foreach ($cards as $c): $title=$c['title']??''; $img=$c['image']??''; $url=$c['url']??''; ?>
                  <div class="tc-grid-item">
                    <div class="tc-card h-100 shadow-sm">
                      <?php if ($url): ?><a href="<?php echo esc_url($url); ?>" class="stretched-link" aria-label="<?php echo esc_attr($title ?: 'Card'); ?>"></a><?php endif; ?>
                      <div class="tc-card-media">
                        <?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" class="w-100 h-100 object-fit-cover"><?php else: ?><div class="tc-card-ph w-100 h-100"></div><?php endif; ?>
                      </div>
                      <?php if ($title): ?><div class="p-3"><div class="fw-semibold tc-card-title"><?php echo esc_html($title); ?></div></div><?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Mobile / Tablet -->
          <div class="d-lg-none">
            <?php if ($count <= 1):
              $c = $cards ? $cards[0] : ['title'=>'','image'=>'','url'=>''];
              $title=$c['title']??''; $img=$c['image']??''; $url=$c['url']??'';
            ?>
              <div class="tc-card shadow-sm">
                <?php if ($url): ?><a href="<?php echo esc_url($url); ?>" class="stretched-link" aria-label="<?php echo esc_attr($title ?: 'Card'); ?>"></a><?php endif; ?>
                <div class="tc-card-media">
                  <?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" class="w-100 h-100 object-fit-cover"><?php else: ?><div class="tc-card-ph w-100 h-100"></div><?php endif; ?>
                </div>
                <?php if ($title): ?><div class="p-3 text-center"><div class="fw-semibold tc-card-title"><?php echo esc_html($title); ?></div></div><?php endif; ?>
              </div>
            <?php else: ?>
              <div class="tc-carousel" data-tc-carousel data-tc-uid="<?php echo esc_attr($uid); ?>" data-tc-count="<?php echo esc_attr($count); ?>" role="region" aria-roledescription="carousel" aria-label="<?php echo esc_attr($headline ?: 'Carousel'); ?>">
                <?php for ($i=1; $i<=$count; $i++): ?>
                  <input class="tc-radio" type="radio" name="<?php echo esc_attr($uid); ?>-slides" id="<?php echo esc_attr($uid . '-r' . $i); ?>" <?php checked($i, 1); ?> />
                <?php endfor; ?>

                <div class="tc-slides" data-tc-slides tabindex="0" aria-live="polite">
                  <?php foreach ($cards as $c): $title=$c['title']??''; $img=$c['image']??''; $url=$c['url']??''; ?>
                    <div class="tc-slide">
                      <div class="tc-card shadow-sm">
                        <?php if ($url): ?><a href="<?php echo esc_url($url); ?>" class="stretched-link" aria-label="<?php echo esc_attr($title ?: 'Card'); ?>"></a><?php endif; ?>
                        <div class="tc-card-media">
                          <?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" class="w-100 h-100 object-fit-cover"><?php else: ?><div class="tc-card-ph w-100 h-100"></div><?php endif; ?>
                        </div>
                        <?php if ($title): ?><div class="p-3 text-center"><div class="fw-semibold tc-card-title"><?php echo esc_html($title); ?></div></div><?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <button class="tc-nav tc-prev" type="button" data-tc-prev aria-label="Previous slide">‹</button>
                <button class="tc-nav tc-next" type="button" data-tc-next aria-label="Next slide">›</button>

                <div class="tc-dots mt-3 d-flex justify-content-center gap-2">
                  <?php for ($i=1; $i<=$count; $i++): ?>
                    <label class="tc-dot" for="<?php echo esc_attr($uid . '-r' . $i); ?>" aria-label="<?php echo esc_attr('Slide ' . $i); ?>"></label>
                  <?php endfor; ?>
                </div>

                <?php
                echo '<style id="'. esc_attr($uid) .'-css">';
                for ($n = 1; $n <= $count; $n++) {
                  $rid = '#' . $uid . '-r' . $n;
                  echo $rid . ':checked ~ .tc-slides .tc-slide:nth-child(' . $n . '){opacity:1;pointer-events:auto;}';
                  echo $rid . ':checked ~ .tc-dots label[for="' . $uid . '-r' . $n . '"]{opacity:1; transform:scale(1.1);}';
                }
                echo '</style>';
                ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>
