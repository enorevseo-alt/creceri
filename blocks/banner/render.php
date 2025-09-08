<?php
  $title        = $attributes['title'] ?? '';
  $img          = $attributes['img'] ?? '';
  $description  = $attributes['description'] ?? '';
  $banner_size  = $attributes['banner_size'] ?? 'small';
  $show_button  = $attributes['showButton'] ?? false;
  $button_text  = $attributes['buttonText'] ?? '';
  $button_url   = $attributes['buttonUrl'] ?? '';

  // layout tweaks
  $height        = $banner_size === 'large' ? '703px' : '403px';
  $align_content = $banner_size === 'large' ? 'end' : 'center';

  // IMPORTANT: this injects alignwide/alignfull (and spacing, etc.)
  $wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'wp-block-child-banner hero-spotlight'
  ] );
?>
<section <?php echo $wrapper_attributes; ?> role="banner" style="min-height: <?php echo esc_attr($height); ?>;">
  <?php if ($img) : ?>
    <div class="hero-spotlight__bg"
         style="background-image: url('<?php echo esc_url($img); ?>'); height: <?php echo esc_attr($height); ?>;">
    </div>
  <?php endif; ?>

  <div class="hero-spotlight__overlay" style="height: <?php echo esc_attr($height); ?>;"></div>

  <div class="hero-spotlight__content" style="align-self: <?php echo esc_attr($align_content); ?> !important;">
    <?php if ($title !== ''): ?>
      <h1 class="hero-spotlight__title"><?php echo esc_html($title); ?></h1>
    <?php endif; ?>

    <?php if ($description !== ''): ?>
      <p class="hero-spotlight__text"><?php echo esc_html($description); ?></p>
    <?php endif; ?>

    <?php if ($show_button && $button_text !== ''): ?>
      <?php if ($button_url): ?>
        <a class="hero-spotlight__btn" href="<?php echo esc_url($button_url); ?>">
          <?php echo esc_html($button_text); ?>
        </a>
      <?php else: ?>
        <button class="hero-spotlight__btn" type="button">
          <?php echo esc_html($button_text); ?>
        </button>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
