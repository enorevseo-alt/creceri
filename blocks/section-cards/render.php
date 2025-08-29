<?php
/**
 * Reusable "Section · Cards" (dynamic, server-rendered).
 */
if ( ! function_exists( 'child_render_cards_section' ) ) {
  function child_render_cards_section( array $atts ): string {
    $title   = $atts['title']   ?? '';
    $intro   = $atts['intro']   ?? '';
    $ctaText = $atts['ctaText'] ?? '';
    $ctaUrl  = $atts['ctaUrl']  ?? '';
    $items   = is_array( $atts['items'] ?? null ) ? $atts['items'] : [];

    $variant = $atts['variant'] ?? 'plain';
    $cols    = $atts['cols']    ?? ['xs'=>1,'sm'=>2,'lg'=>4];

    $show_read = ! empty( $atts['showReadLink'] );
    $global_read = isset( $atts['readLinkText'] ) && $atts['readLinkText'] !== '' ? $atts['readLinkText'] : 'Read more';
    $linkBehavior = $show_read ? 'none' : ( $atts['cardLinkBehavior'] ?? 'stretched' );

    if ( empty( $items ) ) return '';

    // Compute Bootstrap col classes from desired column counts.
    $col_xs = max(1, (int)($cols['xs'] ?? 1));
    $col_sm = max(1, (int)($cols['sm'] ?? 2));
    $col_lg = max(1, (int)($cols['lg'] ?? 4));
    $class_xs = 'col-'    . (int) floor(12 / $col_xs);
    $class_sm = 'col-sm-' . (int) floor(12 / $col_sm);
    $class_lg = 'col-lg-' . (int) floor(12 / $col_lg);

    $uid = 'sc-' . wp_unique_id();

    // Wrapper attributes (adds align classes if used in editor).
    $wrapper_attrs = function_exists('get_block_wrapper_attributes')
      ? get_block_wrapper_attributes( [ 'class' => 'section-cards variant-' . sanitize_html_class($variant) ] )
      : 'class="section-cards variant-' . esc_attr($variant) . ' alignwide"';

    ob_start(); ?>
    <section id="<?php echo esc_attr($uid); ?>" <?php echo $wrapper_attrs; ?>>
      <div class="section-cards__head text-center mb-3">
        <?php if ($title) : ?><h2 class="fw-bold mb-1"><?php echo esc_html($title); ?></h2><?php endif; ?>
        <?php if ($intro) : ?><p class="section-intro text-muted mb-0"><?php echo esc_html($intro); ?></p><?php endif; ?>
      </div>

      <div class="container px-0">
        <div class="row g-3 g-md-4 justify-content-center">
          <?php foreach ($items as $card) :
            $img = $card['image']   ?? '';
            $h   = $card['heading'] ?? '';
            $t   = $card['text']    ?? '';
            $u   = $card['url']     ?? '';
          ?>
          <div class="card-container <?php echo esc_attr("$class_xs $class_sm $class_lg"); ?>">
            <article class="sc-card card h-100 border-0 position-relative<?php echo $show_read ? ' sc-card--static' : ''; ?>">
              <?php if ($img) : ?>
                <img class="card-img-top sc-card__img"
                    src="<?php echo esc_url($img); ?>"
                    alt="<?php echo esc_attr($h ?: 'card image'); ?>"
                    loading="lazy" decoding="async">
              <?php endif; ?>

              <div class="card-body">
                <?php if ($h) : ?><h3 class="h6 fw-bold mb-2 text-danger"><?php echo esc_html($h); ?></h3><?php endif; ?>
                <?php if ($t) : ?><p class="mb-0 text-muted small fw-semibold"><?php echo esc_html($t); ?></p><?php endif; ?>

                <?php
                  // Visible read link when enabled
                  $per_item_read = isset($card['linkText']) && $card['linkText'] !== '' ? $card['linkText'] : null;
                  $read_text     = $per_item_read ?? $global_read;
                  if ( $u && $show_read ) :
                ?>
                  <p class="sc-read mt-2 mb-0">
                    <a class="sc-read__link" href="<?php echo esc_url($u); ?>">
                      <?php echo esc_html($read_text); ?>
                    </a>
                  </p>
                <?php endif; ?>
              </div>

              <?php if ($u && $linkBehavior === 'stretched') : ?>
                <a href="<?php echo esc_url($u); ?>" class="stretched-link" aria-label="<?php echo esc_attr($h ?: 'Open'); ?>"></a>
              <?php endif; ?>
            </article>

          </div>
          <?php endforeach; ?>
        </div>

        <?php if ($ctaText && $ctaUrl) : ?>
          <div class="text-center mt-4">
            <a class="btn btn-outline-danger btn-sm" href="<?php echo esc_url($ctaUrl); ?>">
              <?php echo esc_html($ctaText); ?>
            </a>
          </div>
        <?php endif; ?>
      </div>
    </section>
    <?php
    return ob_get_clean();
  }
}

// Block render entrypoint.
$attributes = is_array($attributes ?? null) ? $attributes : [];
echo child_render_cards_section($attributes);
