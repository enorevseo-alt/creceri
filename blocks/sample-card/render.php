<?php
// $attributes is provided by WP when rendering
$attrs = wp_parse_args($attributes, [
  'title' => '', 'text' => '', 'imageUrl' => '', 'imageAlt' => '',
  'buttonText' => '', 'buttonUrl' => '', 'backgroundColor' => '#ffffff'
]);
?>
<div class="sample-card" style="background-color: <?php echo esc_attr($attrs['backgroundColor']); ?>">
  <?php if ($attrs['imageUrl']) : ?>
    <div class="sample-card__media">
      <img src="<?php echo esc_url($attrs['imageUrl']); ?>" alt="<?php echo esc_attr($attrs['imageAlt']); ?>">
    </div>
  <?php endif; ?>
  <div class="sample-card__content">
    <?php if ($attrs['title']) : ?>
      <h3 class="sample-card__title"><?php echo wp_kses_post($attrs['title']); ?></h3>
    <?php endif; ?>
    <?php if ($attrs['text']) : ?>
      <p class="sample-card__text"><?php echo wp_kses_post($attrs['text']); ?></p>
    <?php endif; ?>
    <?php if ($attrs['buttonText'] || $attrs['buttonUrl']) : ?>
      <div class="sample-card__actions">
        <a class="sample-card__button" href="<?php echo esc_url($attrs['buttonUrl'] ?: '#'); ?>">
          <?php echo esc_html($attrs['buttonText']); ?>
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>
