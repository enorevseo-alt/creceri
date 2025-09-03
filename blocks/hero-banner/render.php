<?php
/**
 * Server-rendered block: child/hero-landing
 *
 * Expected $attributes:
 * - title, lead
 * - slides: [ [url, alt], ... ]
 * - autoplay, interval, pauseOnHover, touch, fade
 * - searchAction, searchPlaceholder
 */

$A = isset($attributes) && is_array($attributes) ? $attributes : [];

$title   = trim($A['title'] ?? '');
$lead    = trim($A['lead'] ?? '');
$slides  = is_array($A['slides'] ?? null) ? $A['slides'] : [];

$autoplay     = !empty($A['autoplay']);
$interval     = max(500, intval($A['interval'] ?? 2000));
$pauseOnHover = !empty($A['pauseOnHover']);
$touch        = array_key_exists('touch', $A) ? (bool)$A['touch'] : true;
$fade         = !empty($A['fade']);

$searchAction      = trim($A['searchAction'] ?? '/blogs/');
$searchPlaceholder = trim($A['searchPlaceholder'] ?? 'Search Blogs');

/** Build safe action URL (allow absolute or site-relative) */
$action_url = (preg_match('~^https?://~i', $searchAction))
  ? $searchAction
  : home_url( $searchAction );

/** Unique ID for the background carousel */
$id = 'heroBg_' . wp_generate_uuid4();

$wrapper_attrs = get_block_wrapper_attributes( [
  'class' => 'hero-landing alignfull'
] );
?>

<section <?php echo $wrapper_attrs; ?> aria-label="<?php echo esc_attr( $title !== '' ? $title : 'Homepage hero' ); ?>">

  <?php if (!empty($slides)): ?>
    <!-- Background carousel (Bootstrap 5) -->
    <div id="<?php echo esc_attr($id); ?>"
         class="carousel slide <?php echo $fade ? 'carousel-fade' : ''; ?>"
         data-bs-ride="<?php echo $autoplay ? 'carousel' : 'false'; ?>"
         data-bs-interval="<?php echo $autoplay ? esc_attr($interval) : 'false'; ?>"
         data-bs-pause="<?php echo $pauseOnHover ? 'hover' : 'false'; ?>"
         data-bs-touch="<?php echo $touch ? 'true' : 'false'; ?>"
         aria-hidden="true">
      <div class="carousel-inner">
        <?php foreach ($slides as $i => $s):
          $url = esc_url($s['url'] ?? '');
          if ($url === '') continue;
          $alt = esc_attr($s['alt'] ?? '');
          $is_active = $i === 0 ? ' active' : '';
        ?>
          <div class="carousel-item<?php echo $is_active; ?>">
            <img class="hero-bg" src="<?php echo $url; ?>" alt="<?php echo $alt; ?>">
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Foreground content -->
  <div class="container">
    <div class="row">
      <div class="col-12 col-lg-7">
        <div class="hero-copy">
          <?php if ($title !== ''): ?>
            <h1 class="hero-title"><?php echo esc_html($title); ?></h1>
          <?php endif; ?>

          <?php if ($lead !== ''): ?>
            <p class="hero-lead"><?php echo esc_html($lead); ?></p>
          <?php endif; ?>

          <!-- Search input only (submit with Enter) -->
          <form class="hero-search" action="<?php echo esc_url($action_url); ?>" method="get" role="search" aria-label="Search blogs">
            <label for="<?php echo esc_attr($id); ?>-search" class="visually-hidden"><?php esc_html_e('Search blogs'); ?></label>
            <span class="search-icon" aria-hidden="true">🔍</span>
            <input id="<?php echo esc_attr($id); ?>-search"
                   type="search"
                   name="s"
                   placeholder="<?php echo esc_attr($searchPlaceholder); ?>">
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
