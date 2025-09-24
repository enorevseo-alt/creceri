<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Allowed markup helpers */
function child_banner_allowed_line_html() {
  return array(
    'br' => array(),
    'span' => array('class' => true),
    'strong' => array(), 'b' => array(),
    'em' => array(), 'i' => array()
  );
}
function child_banner_allowed_lead_html() {
  return array(
    'a' => array('href'=>true,'title'=>true,'target'=>true,'rel'=>true),
    'br' => array(),
    'strong' => array(), 'b' => array(),
    'em' => array(), 'i' => array(),
    'span' => array('class' => true)
  );
}

/* Attributes */
$A            = is_array($attributes ?? null) ? $attributes : array();
$anchor       = $A['anchor']     ?? '';
$className    = $A['className']  ?? '';

$brand        = $A['brand']      ?? 'Creceri';
$brandSize    = isset($A['brandSize']) ? floatval($A['brandSize']) : 0; // px; 0 = auto

$line         = $A['line']       ?? 'E-commerce, UX,<br>and Digital Knowledge';
$lead         = $A['lead']       ?? 'Discover clear, accessible insights into e-commerce, UX design, and digital strategy. Learn from real-world frameworks and web development concepts.';

$buttonType   = in_array(($A['buttonType'] ?? 'Button'), array('Button','Search'), true) ? $A['buttonType'] : 'Button';
$btnText      = $A['buttonText'] ?? 'Explore';
$btnUrl       = $A['buttonUrl']  ?? '#explore';
$btnClass     = $A['buttonClass']?? 'btn btn-custom text-white';

$image        = is_array($A['image'] ?? null) ? $A['image'] : array();
$img_src      = $image['src'] ?? '/wp-content/themes/vite-ttf-child-creceri/assets/images/hero-banner/banner.png';
$img_alt      = $image['alt'] ?? 'Illustration';
$img_w        = !empty($image['width'])  ? intval($image['width'])  : 0;
$img_h        = !empty($image['height']) ? intval($image['height']) : 0;
$img_dec      = $image['decoding'] ?? 'async';
$img_load     = $image['loading']  ?? 'eager';

/* Layout options */
$imagePosition = in_array(($A['imagePosition'] ?? 'right'), array('left','right'), true) ? $A['imagePosition'] : 'right';
$imageLocation = in_array(($A['imageLocation'] ?? 'center'), array('top','center','bottom'), true) ? $A['imageLocation'] : 'center';
$imageSize     = in_array(($A['imageSize'] ?? 'l'), array('s','m','l','xl'), true) ? $A['imageSize'] : 'l';
$imageFlip     = in_array(($A['imageFlip'] ?? 'none'), array('none','mirror'), true) ? $A['imageFlip'] : 'none';

$textAlign     = in_array(($A['textAlign'] ?? 'auto'), array('auto','left','center','right'), true) ? $A['textAlign'] : 'auto';
$textScale     = isset($A['textScale']) ? floatval($A['textScale']) : 1.0;

/* Background controls */
$bgSpot        = in_array(($A['bgSpot'] ?? 'left'), array('left','right','top','bottom'), true) ? $A['bgSpot'] : 'left';
$bgAngle       = isset($A['bgAngle']) ? floatval($A['bgAngle']) : 230;

/* Shadow toggle */
$imgShadow     = ($A['imgShadow'] ?? 'On') === 'Off' ? 'off' : 'on';

/* Auto alignment: inset toward the center */
$resolvedAlign = ('auto' === $textAlign)
  ? ( 'right' === $imagePosition ? 'left' : 'right' )
  : $textAlign;

/* IDs & safe HTML */
$section_id    = $anchor ?: 'hero-' . wp_generate_password(6, false, false);
$title_id      = ($A['titleId'] ?? '') ?: $section_id . '-title';
$line_html     = wp_kses($line, child_banner_allowed_line_html());
$lead_html     = wp_kses($lead, child_banner_allowed_lead_html());

/* Classes + inline style */
$classes = array(
  'hero',
  'hero--img-' . $imagePosition,
  'hero--imgloc-' . $imageLocation,
  'hero--imgsize-' . $imageSize,
  'hero--align-' . $resolvedAlign,
  'hero--flip-' . $imageFlip,
  'hero--bg-' . $bgSpot,
  'hero--shadow-' . $imgShadow   // <-- On / Off
);
if ( $className ) { $classes[] = $className; }

/* Inline CSS variables */
$style_vars = array(
  '--text-scale:' . ($textScale ?: 1),
  '--bg-angle:'   . (is_numeric($bgAngle) ? $bgAngle . 'deg' : '230deg')
);

/* Clamp brandSize to 100–300 if provided */
if ( $brandSize >= 100 ) {
  $brandSize = min(300, $brandSize);
  $style_vars[] = '--brand-size:' . $brandSize . 'px';
  $classes[] = 'hero--brand-fixed';
}
$style_attr = implode(';', $style_vars);
?>
<section class="<?php echo esc_attr(implode(' ', $classes)); ?>"
         style="<?php echo esc_attr($style_attr); ?>"
         aria-labelledby="<?php echo esc_attr($title_id); ?>"
         id="<?php echo esc_attr($section_id); ?>">
  <div class="hero__inner">
    <div class="hero__copy">
      <h1 id="<?php echo esc_attr($title_id); ?>">
        <span class="hero__brand"><?php echo esc_html($brand); ?></span>
      </h1>
      <span class="hero__line"><?php echo $line_html; ?></span>

      <?php if (!empty($lead)) : ?>
        <p class="hero__lead"><?php echo $lead_html; ?></p>
      <?php endif; ?>

      <?php if ('Search' === $buttonType): ?>
        <form class="hero__form" role="search" method="get" action="<?php echo esc_url( home_url('/') ); ?>">
          <label class="screen-reader-text" for="<?php echo esc_attr($section_id); ?>-s"><?php esc_html_e('Search for:', 'vite-ttf-child-creceri'); ?></label>
          <input id="<?php echo esc_attr($section_id); ?>-s" class="hero__input" type="search" name="s" placeholder="<?php echo esc_attr($btnText ?: 'Search…'); ?>" />
          <button class="hero__submit btn btn-custom text-white" type="submit"><?php echo esc_html($btnText ?: 'Search'); ?></button>
        </form>
      <?php else: ?>
        <?php if (!empty($btnText) && !empty($btnUrl)) : ?>
          <a class="<?php echo esc_attr($btnClass); ?>" href="<?php echo esc_url($btnUrl); ?>">
            <?php echo esc_html($btnText); ?>
          </a>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <div class="hero__art">
      <picture>
        <img
          src="<?php echo esc_url($img_src); ?>"
          <?php if ($img_w) : ?>width="<?php echo esc_attr($img_w); ?>"<?php endif; ?>
          <?php if ($img_h) : ?>height="<?php echo esc_attr($img_h); ?>"<?php endif; ?>
          alt="<?php echo esc_attr($img_alt); ?>"
          loading="<?php echo esc_attr($img_load); ?>"
          decoding="<?php echo esc_attr($img_dec); ?>" />
      </picture>
    </div>
  </div>
</section>
