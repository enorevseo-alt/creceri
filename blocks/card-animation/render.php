<?php
/**
 * Block: Guides & Insights (Rail Cards)
 * Path: /wp-content/themes/your-child-theme/blocks/guides/render.php
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Helpers */
function gi_allowed_text_html() {
  // minimal inline markup for intro and item text
  return array(
    'a' => array('href'=>true,'title'=>true,'target'=>true,'rel'=>true),
    'br'=>array(), 'strong'=>array(), 'b'=>array(), 'em'=>array(), 'i'=>array(),
    'span'=>array('class'=>true)
  );
}
function gi_slug( $str, $fallback = 'guides' ) {
  $slug = sanitize_title( (string) $str );
  return $slug ? $slug : $fallback . '-' . wp_rand(100,999);
}

/* Read attributes */
$attrs      = is_array( $attributes ?? null ) ? $attributes : array();
$className  = $attrs['className'] ?? '';

$title      = $attrs['title']   ?? 'Guides & Insights';
$intro      = $attrs['intro']   ?? 'Explore actionable guides and expert tips covering e-commerce, design, marketing, and more. Our resources are built to help SMEs adapt, innovate, and thrive in the digital age.';
$titleId    = ($attrs['titleId'] ?? '') ?: 'guides-title';

$items      = is_array($attrs['items'] ?? null) ? $attrs['items'] : array();
$khtml      = gi_allowed_text_html();

/* Ensure at least one active item (fallback to first) */
$has_active = false;
foreach ($items as $it) {
  if (!empty($it['active'])) { $has_active = true; break; }
}
if (!$has_active && !empty($items)) {
  $items[0]['active'] = true;
}

/* Wrapper classes */
$wrapper_classes = trim('guides ' . $className);
?>
<section class="<?php echo esc_attr($wrapper_classes); ?>" aria-labelledby="<?php echo esc_attr($titleId); ?>">
  <div class="g-wrap">
    <header class="g-head">
      <h2 id="<?php echo esc_attr($titleId); ?>"><?php echo esc_html($title); ?></h2>
      <?php if ( $intro ) : ?>
        <p><?php echo wp_kses( $intro, $khtml ); ?></p>
      <?php endif; ?>
    </header>

    <ul class="g-rail" role="list">
      <?php
      $i = 0;
      foreach ( $items as $item ) :
        $i++;
        $active  = !empty($item['active']);
        $title   = $item['title'] ?? '';
        $text    = $item['text']  ?? '';
        $url     = $item['url']   ?? '';
        $img     = is_array($item['image'] ?? null) ? $item['image'] : array();
        $img_src = $img['src'] ?? '';
        $img_alt = $img['alt'] ?? '';
        $bgColor = $item['bgColor'] ?? ''; // e.g. "#ffffff" or "white"
        $li_cls  = 'g-item' . ( $active ? ' active' : '' );
        $aria    = $title ? sprintf( 'Open %s', $title ) : 'Open';
        $style_bg = $bgColor ? 'background:' . esc_attr($bgColor) . ';' : '';
      ?>
        <li class="<?php echo esc_attr($li_cls); ?>" data-bg="<?php echo esc_attr($i); ?>" style="<?php echo esc_attr($style_bg); ?>">
          <a class="g-link" href="<?php echo esc_url($url ?: '#'); ?>" aria-label="<?php echo esc_attr($aria); ?>">
            <span class="g-media" aria-hidden="true"
                  <?php if ($img_src): ?>
                    style="background-image:url('<?php echo esc_url($img_src); ?>');"
                  <?php endif; ?>></span>
            <div class="g-overlay">
              <?php if ( $title ) : ?><h3><?php echo esc_html($title); ?></h3><?php endif; ?>
              <?php if ( $text  ) : ?><p><?php echo wp_kses($text, $khtml); ?></p><?php endif; ?>
            </div>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
