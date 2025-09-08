<?php 
$title        = $attributes['title'] ?? '';
$sub_title    = $attributes['sub_title'] ?? '';
$footer_text  = $attributes['footer_text'] ?? '';
$category_data        = $attributes['category_data'] ?? [];
$category_list        = $attributes['category_list'] ?? [];
?>
<?php
  // Allow per-instance gradient colors from block attributes or template usage
  $bg_start = $attributes['bgStart'] ?? ($attributes['bg_start'] ?? '');
  $bg_end   = $attributes['bgEnd']   ?? ($attributes['bg_end']   ?? '');
  $position   = $attributes['position']   ?? ($attributes['position']   ?? '');
  $align_content = $attributes['position']  ?? ($attributes['position']   ?? '');

  // Named directions: "to right", "to left", "to top", "to bottom".
  // Angles: "45deg", "135deg", etc.
  $bg_dir   = $attributes['bgDirection'] ?? ($attributes['bg_direction'] ?? ($attributes['bgAngle'] ?? ($attributes['bg_angle'] ?? '')));
  $style_inline = '';
  if (!empty($bg_start)) { $style_inline .= '--bg-start:' . esc_attr($bg_start) . ';'; }
  if (!empty($bg_end))   { $style_inline .= '--bg-end:'   . esc_attr($bg_end)   . ';'; }
  if (!empty($bg_dir))   { $style_inline .= '--bg-angle:' . esc_attr($bg_dir)   . ';'; }
?>
<section class="connect-strip" <?php echo $style_inline ? 'style="' . esc_attr($style_inline) . '"' : ''; ?> >
  <div class="container " <?php echo $align_content ? 'style="text-align:' . esc_attr($align_content) . ';"' : ''; ?> >
    <h2 class="title"><?php echo esc_html( $title ); ?></h2>
    <p class="sub"><?php echo esc_html( $sub_title ); ?></p>

    <div class="features row" >
      <!-- Feature 1 -->
      <?php if (!empty($category_data)) : ?>
        <?php foreach ($category_data as $card) :
          $img          = $card['img'] ?? '';
          $h3           = $card['h3'] ?? '';
          $description  = $card['description'] ?? '';
        ?>
            <article class="feature text-<?php echo $position?>">
                <!-- SIM + pin icon -->
              <?php if($img!=null):?>
              <img src="<?php echo esc_url( $img ); ?>" alt="SIM card icon" class="icon"/>
              <?php endif; ?>
              <h3><?php echo esc_html( $h3 ); ?></h3>
              <p><?php echo esc_html( $description ); ?></p>
            </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

     <div class="features_list" >
      <!-- Feature 1 -->
      <?php if (!empty($category_list)) : ?>
        <?php foreach ($category_list as $card_data) :
          $list_data          = $card_data['description'] ?? '';
        ?>
            <ul>
              <?php if($list_data!=null):?>
                  <li><?php echo $list_data; ?></li>
              <?php endif; ?>
            </ul>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>             
    <p class="tip">
      <?php echo esc_html( $footer_text ); ?></p>
  </div>
</section>
