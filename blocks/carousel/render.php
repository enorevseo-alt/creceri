<?php
/**
 * Generic Carousel — server render (Bootstrap 5)
 */

$A = isset($attributes) ? $attributes : [];

$items = isset($A['items']) && is_array($A['items']) ? $A['items'] : [];
if (empty($items)) {
  echo '<div class="wp-block-child-carousel is-empty">Add cards to display the carousel…</div>';
  return;
}

$title   = trim($A['title'] ?? '');
$intro   = trim($A['intro'] ?? '');

$cols = $A['cols'] ?? ['xs'=>1,'sm'=>2,'md'=>3,'lg'=>4,'xl'=>5];
$bps  = ['xs','sm','md','lg','xl'];

/** Build row-cols classes */
$rowColsPieces = [];
foreach ($bps as $bp) {
  if (!empty($cols[$bp])) {
    $n = max(1, intval($cols[$bp]));
    $rowColsPieces[] = ($bp === 'xs') ? "row-cols-$n" : "row-cols-$bp-$n";
  }
}
$rowColsClasses = 'row '.implode(' ', $rowColsPieces).' g-3 g-md-4';

/** Behavior flags */
$cardLinkBehavior = $A['cardLinkBehavior'] ?? 'stretched';
$autoplay       = !empty($A['autoplay']);
$interval       = intval($A['interval'] ?? 5000);
$pauseOnHover   = !empty($A['pauseOnHover']);
$wrap           = !empty($A['wrap']);
$showControls   = !empty($A['showControls']);
$showIndicators = !empty($A['showIndicators']);

/** Grid mode if no carousel behaviors are enabled */
$isCarousel = ($autoplay || $showControls || $showIndicators);

$ariaLabel = $A['ariaLabel'] ?? 'Carousel';
$id = !empty($A['instanceId']) ? $A['instanceId'] : ('carousel_' . uniqid());

/** Background */
$bgType  = $A['backgroundType'] ?? 'none';
$bgValue = trim($A['backgroundValue'] ?? '');
$bgStyle = ($bgType !== 'none' && $bgValue !== '') ? 'background:'.$bgValue.';' : '';

/** Data inspection for overlay labels */
$allImageOnly = !empty($items) && array_reduce($items, function($carry, $it){
  return $carry && !empty($it['imageOnly']);
}, true);

// Country label comes from the block title (e.g., "Thailand")
$countryLabel = $title !== '' ? $title : '';

// Human-readable “type” label
if ($isCarousel) {
  $typeLabel = $allImageOnly ? 'Image Carousel' : 'Card Carousel';
} else {
  $typeLabel = $allImageOnly ? 'Image Grid' : 'Card Grid';
}

/** Helper to print one card */
$print_card = function(array $card) use ($cardLinkBehavior, $countryLabel, $typeLabel) {
  $img   = esc_url($card['imageURL'] ?? '');
  $alt   = esc_attr($card['imageAlt'] ?? '');
  $h     = trim($card['heading'] ?? '');
  $txt   = trim($card['text'] ?? '');
  $label = trim($card['buttonLabel'] ?? '');
  $url   = esc_url($card['buttonURL'] ?? '');
  $tag   = trim($card['tag'] ?? '');
  $meta  = trim($card['meta'] ?? '');
  $date  = trim($card['date'] ?? '');
  $isImageOnly = !empty($card['imageOnly']);
  $buttonText  = $label !== '' ? $label : 'Learn More';
  $ratio = $isImageOnly ? '133.333%' : '56.25%'; // 4:3 vs 16:9
  ?>
  <div class="col">
    <article class="card <?php echo $isImageOnly ? '' : 'h-100'; ?> shadow-sm generic-card <?php echo $isImageOnly ? 'image-only' : 'has-body'; ?>">
      <?php if ($img): ?>
        <div class="ratio card-media" style="--bs-aspect-ratio: <?php echo esc_attr($ratio); ?>;">
          <img src="<?php echo $img; ?>" alt="<?php echo $alt; ?>" class="card-img-top object-cover">
          <?php if ($isImageOnly): ?>
            <!-- Overlay with arced badge + centered title -->
            <?php $arc_id = 'arc-'.uniqid(); ?>
            <div class="tile-overlay">
              <?php if ($countryLabel || $typeLabel): ?>
                <svg class="tile-arc" viewBox="0 0 100 50" preserveAspectRatio="xMidYMid meet" aria-hidden="true" focusable="false">
                  <defs>
                    <path id="<?php echo esc_attr($arc_id); ?>" d="M5,30 H95" />
                  </defs>
                  <text class="tile-arc-text">
                    <textPath href="#<?php echo esc_attr($arc_id); ?>" startOffset="50%" text-anchor="middle">
                      <?php
                        $parts = array_filter([$card['imageAlt']]);
                        echo esc_html(strtoupper(implode(' • ', $parts)));
                      ?>
                    </textPath>
                  </text>
                </svg>
              <?php endif; ?>

            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if (!$isImageOnly): ?>
        <div class="card-body">
          <?php if ($tag !== ''): ?>
            <span class="badge bg-light text-muted mb-2"><?php echo esc_html($tag); ?></span>
          <?php endif; ?>

          <?php if ($h !== ''): ?>
            <h3 class="card-title mb-2"><?php echo esc_html($h); ?></h3>
          <?php endif; ?>

          <?php if ($txt !== ''): ?>
            <p class="card-text mb-3"><?php echo esc_html($txt); ?></p>
          <?php endif; ?>

          <?php if ($meta !== '' || $date !== ''): ?>
            <div class="card-meta border-top pt-2 m-2">
              <?php if ($meta !== ''): ?><div class="meta-line"><?php echo esc_html($meta); ?></div><?php endif; ?>
              <?php if ($date !== ''): ?><div class="meta-line"><?php echo esc_html($date); ?></div><?php endif; ?>
            </div>
          <?php endif; ?>

          <?php if ($url && $cardLinkBehavior !== 'none'): ?>
            <a class="btn btn-danger btn-sm mt-10" href="<?php echo $url; ?>"><?php echo esc_html($buttonText); ?></a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ($url && $cardLinkBehavior === 'stretched'): ?>
        <a class="stretched-link" href="<?php echo $url; ?>" aria-label="<?php echo esc_attr($h ?: 'Open'); ?>"></a>
      <?php endif; ?>
    </article>

    <?php if ($isImageOnly): ?>
      <?php if ($url && $cardLinkBehavior === 'button-only'): ?>
        <div class="tile-actions mt-2 text-center">
          <a class="btn btn-danger btn-sm" href="<?php echo $url; ?>"><?php echo esc_html($buttonText); ?></a>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <?php
};


/** Render */
?>
<section class="carousel-block<?php echo $bgStyle ? ' has-bg' : ''; ?>"<?php echo $bgStyle ? ' style="'.esc_attr($bgStyle).'"' : ''; ?>>
  <?php if ($title || $intro): ?>
    <header class="carousel-head">
      <?php if ($title): ?><h2 class="carousel-title"><?php echo esc_html($title); ?></h2><?php endif; ?>
      <?php if ($intro): ?><p class="carousel-intro"><?php echo esc_html($intro); ?></p><?php endif; ?>
    </header>
  <?php endif; ?>

  <?php if (!$isCarousel): ?>
    <!-- GRID MODE (no carousel behavior) -->
    <div class="container-fluid p-0 generic-grid">
      <div class="<?php echo esc_attr($rowColsClasses); ?>">
        <?php foreach ($items as $card) { $print_card($card); } ?>
      </div>
    </div>

  <?php else: ?>
    <!-- CAROUSEL MODE -->
    <?php
      // chunk by the *largest* configured column count
      $largest = 1;
      foreach ($bps as $bp) { if (!empty($cols[$bp])) $largest = max($largest, intval($cols[$bp])); }
      $perSlide = max(1, $largest);
      $slides = array_chunk($items, $perSlide);

      // data attributes
      $data = [
        'data-bs-ride'     => $autoplay ? 'carousel' : false,
        'data-bs-interval' => $autoplay ? $interval : false,
        'data-bs-pause'    => $pauseOnHover ? 'hover' : 'false',
        'data-bs-wrap'     => $wrap ? 'true' : 'false',
      ];
      $carouselDataAttr = '';
      foreach ($data as $k => $v) {
        if ($v !== false && $v !== null && $v !== '') {
          $carouselDataAttr .= sprintf(' %s="%s"', esc_attr($k), esc_attr($v));
        }
      }
    ?>
    <div id="<?php echo esc_attr($id); ?>"
         class="carousel slide generic-carousel"
         aria-label="<?php echo esc_attr($ariaLabel); ?>"
         data-cols-xs="<?php echo (int)($cols['xs'] ?? 1); ?>"
         data-cols-sm="<?php echo (int)($cols['sm'] ?? 2); ?>"
         data-cols-md="<?php echo (int)($cols['md'] ?? 3); ?>"
         data-cols-lg="<?php echo (int)($cols['lg'] ?? 4); ?>"
         data-cols-xl="<?php echo (int)($cols['xl'] ?? 5); ?>"
         <?php echo $carouselDataAttr; ?>>

      <?php if ($showIndicators && count($slides) > 1): ?>
        <!-- helper text for small screens only -->
        <div class="carousel-helper d-block d-lg-none position-absolute bottom-0 start-50 translate-middle-x mt-2 text-center">
          <span class="swipe-hint px-3 py-1 rounded-pill bg-light text-body-secondary shadow-sm" role="note">
            <span class="visually-hidden">Swipe left or right</span>
            <span aria-hidden="true" class="d-inline-flex align-items-center gap-2">
              <span class="arrow">&larr;</span>
              <span class="word">left</span>
              <span class="rule"></span>
              <span class="word">swipe</span>
              <span class="rule"></span>
              <span class="word">right</span>
              <span class="arrow">&rarr;</span>
            </span>
          </span>
        </div>
        <div class="carousel-indicators">
          <?php foreach ($slides as $i => $_): ?>
            <button type="button"
              data-bs-target="#<?php echo esc_attr($id); ?>"
              data-bs-slide-to="<?php echo esc_attr($i); ?>"
              <?php echo $i === 0 ? 'class="active" aria-current="true"' : ''; ?>
              aria-label="<?php printf('Slide %d', $i + 1); ?>"></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="carousel-inner">
        <?php foreach ($slides as $sIndex => $group): ?>
        <?php
          $isPartial = count($group) < $perSlide;
          $rowClassThisSlide = $rowColsClasses . ($isPartial ? ' justify-content-center' : '');
        ?>
          <div class="carousel-item <?php echo $sIndex === 0 ? 'active' : ''; ?>">
            <div class="<?php echo esc_attr($rowClassThisSlide); ?>">
              <?php foreach ($group as $card) { $print_card($card); } ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if ($showControls && count($slides) > 1): ?>
        <button class="carousel-control-prev d-none d-lg-flex" type="button" data-bs-target="#<?php echo esc_attr($id); ?>" data-bs-slide="prev" aria-label="Previous">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next d-none d-lg-flex" type="button" data-bs-target="#<?php echo esc_attr($id); ?>" data-bs-slide="next" aria-label="Next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</section>
