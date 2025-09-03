<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$items = ( ! empty( $attributes['items'] ) && is_array( $attributes['items'] ) ) ? $attributes['items'] : [];
if ( empty( $items ) ) { return ''; }

$uid      = 'ci-' . wp_unique_id();
$heading  = isset( $attributes['title'] ) ? (string) $attributes['title'] : '';
$summary  = isset( $attributes['paragraph'] ) ? (string) $attributes['paragraph'] : '';
$speed    = isset( $attributes['speed'] ) ? (float) $attributes['speed'] : 30;
$card_w   = isset( $attributes['cardWidth'] ) ? (int) $attributes['cardWidth'] : 260;
$card_h   = isset( $attributes['cardHeight'] ) ? (int) $attributes['cardHeight'] : 160;
$gap      = isset( $attributes['gap'] ) ? (int) $attributes['gap'] : 16;
$align    = ! empty( $attributes['align'] ) ? 'align' . sanitize_html_class( $attributes['align'] ) : '';

$loop_items = array_merge( $items, $items );

$style_vars = sprintf(
  '--ci-card-w:%dpx;--ci-card-h:%dpx;--ci-gap:%dpx;--ci-speed:%ss;',
  $card_w, $card_h, $gap, $speed
);

ob_start(); ?>

<?php if ( $heading !== '' || $summary !== '' ) : ?>
  <div class="ci-head <?php echo esc_attr( $align ); ?>">
    <?php if ( $heading !== '' ) : ?>
      <h2 class="ci-heading"><?php echo esc_html( $heading ); ?></h2>
    <?php endif; ?>
    <?php if ( $summary !== '' ) : ?>
      <p class="ci-summary"><?php echo esc_html( $summary ); ?></p>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div id="<?php echo esc_attr( $uid ); ?>"
     class="ci-wrap <?php echo esc_attr( $align ); ?>"
     style="<?php echo esc_attr( $style_vars ); ?>">
  <div class="container-fluid p-0">
    <div class="ci-track"
         role="list"
         data-loop-n="<?php echo (int) count( $items ); ?>"
         aria-label="<?php echo esc_attr__( 'Infinite carousel', 'childtheme' ); ?>">
      <?php
      $original_count = count( $items );
      foreach ( $loop_items as $i => $item ) :
        $img   = ! empty( $item['img'] )   ? esc_url( $item['img'] )   : '';
        $title = ! empty( $item['title'] ) ? wp_kses_post( $item['title'] ) : '';
        $text  = ! empty( $item['text'] )  ? wp_kses_post( $item['text'] )  : '';
        $alt   = ! empty( $item['alt'] )   ? $item['alt'] : wp_strip_all_tags( $title );

        // Eager-load the first copy (immediate), lazy-load the duplicated copy
        $loading = ( $i < $original_count ) ? 'eager' : 'lazy';
        ?>
        <article class="ci-card" role="listitem" aria-label="<?php echo esc_attr( $alt ); ?>">
          <?php if ( $img ) : ?>
            <img
              class="ci-img skip-lazy no-lazy"
              data-no-lazy="1"
              src="<?php echo $img; ?>"
              alt="<?php echo esc_attr( $alt ); ?>"
              loading="<?php echo esc_attr( $loading ); ?>"
              decoding="async"
              fetchpriority="<?php echo $loading === 'eager' ? 'high' : 'auto'; ?>"
            />
          <?php endif; ?>
          <div class="ci-label">
            <?php if ( $title ) : ?>
              <h3 class="ci-title"><?php echo $title; ?></h3>
            <?php endif; ?>
            <?php if ( $text ) : ?>
              <p class="ci-sub"><?php echo $text; ?></p>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</div>
