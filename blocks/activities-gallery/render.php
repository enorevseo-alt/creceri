<?php
/**
 * Activities Gallery — server-side render
 * Needs Bootstrap 5 CSS & JS on the front end.
 */

// Read attributes
$title = (string) ( $attributes['title'] ?? '' );
$intro = (string) ( $attributes['intro'] ?? '' );

$bg_mode = $attributes['bgMode'] ?? $attributes['backgroundType'] ?? 'plain';
$bg_col  = trim( (string) ( $attributes['bgColor'] ?? $attributes['backgroundValue'] ?? '' ) );

$items = ( isset( $attributes['items'] ) && is_array( $attributes['items'] ) ) ? $attributes['items'] : [];
$count = count( $items );

// Aspect ratios (for “flat” cards)
$card_aspect        = (string) ( $attributes['cardAspect']       ?? '16/9' );
$card_aspect_wide   = (string) ( $attributes['cardAspectWide']   ?? '21/9' );
$card_aspect_mobile = (string) ( $attributes['cardAspectMobile'] ?? '16/9' );

// NEW: default image crop position (global). e.g. "center bottom", "50% 100%"
$image_position = trim( (string) ( $attributes['imagePosition'] ?? 'center bottom' ) );

// ---- color helpers ------------------------------------------------------
$named = [
    'red'=>'#dc3545','blue'=>'#0d6efd','green'=>'#198754','teal'=>'#20c997',
    'orange'=>'#fd7e14','purple'=>'#6f42c1','pink'=>'#d63384','yellow'=>'#ffc107',
    'cyan'=>'#0dcaf0','black'=>'#000000','white'=>'#ffffff','gray'=>'#6c757d'
];

$normalize_hex = function ( $color ) use ( $named ) {
    if ( $color === '' ) return '';
    $color = strtolower( $color );
    if ( isset( $named[ $color ] ) ) return $named[ $color ];
    if ( preg_match( '/^#([0-9a-f]{3})$/i', $color ) ) {
        $hex = substr( $color, 1 );
        return sprintf( '#%1$s%1$s%2$s%2$s%3$s%3$s', $hex[0], $hex[1], $hex[2] );
    }
    if ( preg_match( '/^#([0-9a-f]{6})$/i', $color ) ) return $color;
    return '';
};

$adjust_brightness = function ( $hex, $steps ) {
    $hex = ltrim( $hex, '#' );
    $steps = max( -255, min( 255, (int) $steps ) );
    $r = max( 0, min( 255, hexdec( substr( $hex, 0, 2 ) ) + $steps ) );
    $g = max( 0, min( 255, hexdec( substr( $hex, 2, 2 ) ) + $steps ) );
    $b = max( 0, min( 255, hexdec( substr( $hex, 4, 2 ) ) + $steps ) );
    return sprintf( '#%02x%02x%02x', $r, $g, $b );
};

// Background style
$base = $normalize_hex( $bg_col );
if ( $bg_mode === 'solid' && $base ) {
    $wrapper_style = 'background:' . $base . ';';
} elseif ( $bg_mode === 'gradient' && $base ) {
    $light = $adjust_brightness( $base, 35 );
    $dark  = $adjust_brightness( $base, -35 );
    $wrapper_style = 'background-image:linear-gradient(135deg,' . $light . ',' . $dark . ');';
} else {
    $wrapper_style = 'background:#ffffff;';
}

// Pass CSS variables (aspect + image position)
$wrapper_style .= '--ta-aspect:' . $card_aspect . ';';
$wrapper_style .= '--ta-aspect-wide:' . $card_aspect_wide . ';';
$wrapper_style .= '--ta-aspect-mobile:' . $card_aspect_mobile . ';';
$wrapper_style .= '--ta-object-pos:' . ( $image_position ?: 'center bottom' ) . ';';

$uid = 'ta-' . wp_unique_id();

ob_start(); ?>
<section <?php echo get_block_wrapper_attributes( [
    'class' => 'activities-gallery',
    'style' => $wrapper_style,
] ); ?>>
    <div class="container-fluid p-0">

        <?php if ( $title ) : ?>
            <h2 class="wp-block-heading has-text-align-center" style="margin-bottom:.25rem"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>

        <?php if ( $intro ) : ?>
            <p class="has-text-align-center" style="margin-bottom:1rem"><?php echo esc_html( $intro ); ?></p>
        <?php endif; ?>

        <?php if ( $count > 0 ) : ?>

            <!-- Grid (md and up) -->
            <div class="row g-3 d-none d-md-flex">
                <?php foreach ( $items as $i => $item ) :
                    $img = is_array( $item ) ? ( $item['image'] ?? $item['imageURL'] ?? '' ) : '';
                    $t1  = is_array( $item ) ? ( $item['heading'] ?? '' ) : '';
                    $t2  = is_array( $item ) ? ( $item['subheading'] ?? '' ) : '';
                    $url = is_array( $item ) ? ( $item['url'] ?? '' ) : '';
                    $alt = is_array( $item ) ? ( $item['imageAlt'] ?? $t1 ?? '' ) : '';

                    // Per-item crop override (optional): item.objectPosition
                    $fig_style = '';
                    if ( is_array( $item ) && ! empty( $item['objectPosition'] ) ) {
                        $fig_style = ' style="--ta-object-pos:' . esc_attr( $item['objectPosition'] ) . ';"';
                    }

                    $is_last_wide = ( $count % 2 === 1 ) && ( $i === $count - 1 );
                    $col_class    = $is_last_wide ? 'col-12' : 'col-md-6';
                    $wide_class   = $is_last_wide ? ' ta-wide' : '';
                    ?>
                    <div class="<?php echo esc_attr( $col_class ); ?>">
                        <figure class="ta-card<?php echo esc_attr( $wide_class ); ?>"<?php echo $fig_style; ?>>
                            <?php if ( $url ) : ?><a class="stretched-link" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $t1 ?: 'View' ); ?>"><?php endif; ?>
                                <?php if ( $img ) : ?>
                                    <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
                                <?php else : ?>
                                    <div class="ta-placeholder"></div>
                                <?php endif; ?>
                            <?php if ( $url ) : ?></a><?php endif; ?>

                            <?php if ( $t1 || $t2 ) : ?>
                            <figcaption class="ta-overlay">
                                <?php if ( $t1 ) : ?><div class="ta-title"><?php echo esc_html( $t1 ); ?></div><?php endif; ?>
                                <?php if ( $t2 ) : ?><div class="ta-subtitle"><?php echo esc_html( $t2 ); ?></div><?php endif; ?>
                            </figcaption>
                            <?php endif; ?>
                        </figure>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Carousel (below md) -->
            <div id="<?php echo esc_attr( $uid ); ?>" class="carousel slide d-md-none" data-bs-touch="true" data-bs-interval="false">
                <?php if ( $count > 1 ) : ?>
                <div class="carousel-indicators">
                    <?php for ( $i = 0; $i < $count; $i++ ) : ?>
                        <button type="button" data-bs-target="#<?php echo esc_attr( $uid ); ?>" data-bs-slide-to="<?php echo esc_attr( $i ); ?>" <?php if ( $i === 0 ) echo 'class="active" aria-current="true"'; ?> aria-label="<?php echo esc_attr( 'Slide ' . ( $i + 1 ) ); ?>"></button>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

                <div class="carousel-inner">
                    <?php foreach ( $items as $i => $item ) :
                        $img = is_array( $item ) ? ( $item['image'] ?? $item['imageURL'] ?? '' ) : '';
                        $t1  = is_array( $item ) ? ( $item['heading'] ?? '' ) : '';
                        $t2  = is_array( $item ) ? ( $item['subheading'] ?? '' ) : '';
                        $url = is_array( $item ) ? ( $item['url'] ?? '' ) : '';
                        $alt = is_array( $item ) ? ( $item['imageAlt'] ?? $t1 ?? '' ) : '';

                        $fig_style = '';
                        if ( is_array( $item ) && ! empty( $item['objectPosition'] ) ) {
                            $fig_style = ' style="--ta-object-pos:' . esc_attr( $item['objectPosition'] ) . ';"';
                        }
                        ?>
                        <div class="carousel-item<?php echo $i === 0 ? ' active' : ''; ?>">
                            <figure class="ta-card"<?php echo $fig_style; ?>>
                                <?php if ( $url ) : ?><a class="stretched-link" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $t1 ?: 'View' ); ?>"><?php endif; ?>
                                    <?php if ( $img ) : ?>
                                        <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
                                    <?php else : ?>
                                        <div class="ta-placeholder"></div>
                                    <?php endif; ?>
                                <?php if ( $url ) : ?></a><?php endif; ?>

                                <?php if ( $t1 || $t2 ) : ?>
                                <figcaption class="ta-overlay">
                                    <?php if ( $t1 ) : ?><div class="ta-title"><?php echo esc_html( $t1 ); ?></div><?php endif; ?>
                                    <?php if ( $t2 ) : ?><div class="ta-subtitle"><?php echo esc_html( $t2 ); ?></div><?php endif; ?>
                                </figcaption>
                                <?php endif; ?>
                            </figure>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ( $count > 1 ) : ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo esc_attr( $uid ); ?>" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden"><?php esc_html_e( 'Previous', 'child' ); ?></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#<?php echo esc_attr( $uid ); ?>" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden"><?php esc_html_e( 'Next', 'child' ); ?></span>
                </button>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>
</section>
