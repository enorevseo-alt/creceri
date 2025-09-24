<?php
/**
 * Block: Hero · Brand + Line + Lead + CTA + Art
 * Path: /wp-content/themes/your-child-theme/blocks/hero/render.php
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Helpers */
function hero_allowed_line_html() {
  // allow <br> and simple inline formatting in the line text
  return array(
    'br' => array(),
    'span' => array('class' => true),
    'strong' => array(), 'b' => array(),
    'em' => array(), 'i' => array()
  );
}
function hero_allowed_lead_html() {
  // safe inline markup for the lead paragraph
  return array(
    'a' => array('href'=>true,'title'=>true,'target'=>true,'rel'=>true),
    'br' => array(),
    'strong' => array(), 'b' => array(),
    'em' => array(), 'i' => array(),
    'span' => array('class' => true)
  );
}

/* Read attributes */
$attrs      = is_array( $attributes ?? null ) ? $attributes : array();
$anchor     = $attrs['anchor']     ?? '';          // optional section id (supports.anchor)
$className  = $attrs['className']  ?? '';          // additional classes from editor
$brand      = $attrs['brand']      ?? 'Creceri';
$line       = $attrs['line']       ?? 'E-commerce, UX,<br>and Digital Knowledge';
$lead       = $attrs['lead']       ?? 'Discover clear, accessible insights into e-commerce, UX design, and digital strategy. Learn from real-world frameworks and web development concepts.';
$btnText    = $attrs['buttonText'] ?? 'Explore';
$btnUrl     = $attrs['buttonUrl']  ?? '#explore';
$btnClass   = $attrs['buttonClass']?? 'btn btn-custom text-white';

$image      = is_array($attrs['image'] ?? null) ? $attrs['image'] : array();
$img_src    = $image['src'] ?? '/wp-content/themes/vite-ttf-child-creceri/assets/images/hero-banner/banner.png';
$img_alt    = $image['alt'] ?? '3D illustration of a person on a laptop with UX, plant, books and robot elements.';
$img_w      = !empty($image['width'])  ? intval($image['width'])  : 0;
$img_h      = !empty($image['height']) ? intval($image['height']) : 0;
$img_dec    = $image['decoding'] ?? 'async';       // "async" | "auto" | "sync"
$img_load   = $image['loading']  ?? 'eager';       // "lazy" | "eager"

$section_id = $anchor ?: 'hero-' . wp_generate_password(6, false, false);
$title_id   = ($attrs['titleId'] ?? '') ?: $section_id . '-title';

/* Escape/allow */
$line_html  = wp_kses( $line, hero_allowed_line_html() );
$lead_html  = wp_kses( $lead, hero_allowed_lead_html() );

/* Compose wrapper classes */
$wrapper_classes = trim( 'hero ' . $className );
?>
<section class="<?php echo esc_attr($wrapper_classes); ?>" aria-labelledby="<?php echo esc_attr($title_id); ?>" id="<?php echo esc_attr($section_id); ?>">
  <div class="hero__inner">
    <div class="hero__copy">
      <h1 id="<?php echo esc_attr($title_id); ?>">
        <span class="hero__brand" ><?php echo esc_html($brand); ?></span>
      </h1>
      <span class="hero__line"><?php echo $line_html; // already kses ?></span>

      <?php if ( ! empty( $lead ) ) : ?>
        <p class="hero__lead"><?php echo $lead_html; // already kses ?></p>
      <?php endif; ?>

      <?php if ( ! empty($btnText) && ! empty($btnUrl) ) : ?>
        <a class="<?php echo esc_attr($btnClass); ?>" href="<?php echo esc_url($btnUrl); ?>">
          <?php echo esc_html($btnText); ?>
        </a>
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
