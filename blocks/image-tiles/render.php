<?php
  // Cleaned attributes
  $header_title = $attributes['title'] ?? '';
  $description  = $attributes['description'] ?? '';
  $video_url    = $attributes['video_url'] ?? '';
  // Support `item` or `items`, array or JSON
  $raw   = $attributes['item'] ?? ($attributes['items'] ?? []);
  $items = is_array($raw) ? $raw : (json_decode($raw, true) ?: []);
  $count = is_array($items) ? count($items) : 0;
  // Layout map by item count (preserves your current placements)
  $layouts = [
    1 => ['cols'=>6, 'auto_rows'=>60, 'pos'=>[
      'a'=>['col'=>1,'cspan'=>6,'row'=>1,'rspan'=>8],
    ]],
    2 => ['cols'=>6, 'auto_rows'=>60, 'pos'=>[
      'a'=>['col'=>1,'cspan'=>3,'row'=>1,'rspan'=>8],
      'b'=>['col'=>4,'cspan'=>3,'row'=>1,'rspan'=>8],
    ]],
    3 => ['cols'=>9, 'auto_rows'=>60, 'pos'=>[
      'a'=>['col'=>1,'cspan'=>3,'row'=>1,'rspan'=>8],
      'b'=>['col'=>4,'cspan'=>3,'row'=>1,'rspan'=>8],
      'c'=>['col'=>7,'cspan'=>3,'row'=>1,'rspan'=>8],
    ]],
    4 => ['cols'=>10, 'auto_rows'=>60, 'pos'=>[
      'a'=>['col'=>1,'cspan'=>3,'row'=>1,'rspan'=>8],
      'b'=>['col'=>4,'cspan'=>2,'row'=>1,'rspan'=>8],
      'c'=>['col'=>6,'cspan'=>3,'row'=>1,'rspan'=>8],
      'd'=>['col'=>9,'cspan'=>2,'row'=>1,'rspan'=>8],
    ]],
    5 => ['cols'=>9, 'auto_rows'=>60, 'pos'=>[
      'a'=>['col'=>1,'cspan'=>3,'row'=>1,'rspan'=>8],
      'b'=>['col'=>4,'cspan'=>2,'row'=>1,'rspan'=>5],
      'c'=>['col'=>4,'cspan'=>2,'row'=>6,'rspan'=>3],
      'd'=>['col'=>6,'cspan'=>2,'row'=>1,'rspan'=>8],
      'e'=>['col'=>8,'cspan'=>2,'row'=>1,'rspan'=>8],
    ]],
    6 => ['cols'=>10, 'auto_rows'=>60, 'pos'=>[
      'a'=>['col'=>1,'cspan'=>3,'row'=>1,'rspan'=>8],
      'b'=>['col'=>4,'cspan'=>2,'row'=>1,'rspan'=>5],
      'c'=>['col'=>4,'cspan'=>2,'row'=>6,'rspan'=>3],
      'd'=>['col'=>6,'cspan'=>2,'row'=>4,'rspan'=>5],
      'e'=>['col'=>6,'cspan'=>2,'row'=>1,'rspan'=>3],
      'f'=>['col'=>8,'cspan'=>3,'row'=>1,'rspan'=>8],
    ]],
    7 => ['cols'=>10, 'auto_rows'=>60, 'pos'=>[
      'a'=>['col'=>1,'cspan'=>3,'row'=>1,'rspan'=>8],
      'b'=>['col'=>4,'cspan'=>2,'row'=>1,'rspan'=>5],
      'c'=>['col'=>4,'cspan'=>2,'row'=>6,'rspan'=>3],
      'd'=>['col'=>6,'cspan'=>2,'row'=>4,'rspan'=>5],
      'e'=>['col'=>6,'cspan'=>2,'row'=>1,'rspan'=>3],
      'f'=>['col'=>8,'cspan'=>3,'row'=>1,'rspan'=>4],
      'g'=>['col'=>8,'cspan'=>3,'row'=>5,'rspan'=>4],
    ]],
    8 => ['cols'=>10, 'auto_rows'=>60, 'pos'=>[
      'a'=>['col'=>1,'cspan'=>3,'row'=>1,'rspan'=>4],
      'b'=>['col'=>1,'cspan'=>3,'row'=>5,'rspan'=>4],
      'c'=>['col'=>4,'cspan'=>2,'row'=>1,'rspan'=>5],
      'd'=>['col'=>4,'cspan'=>2,'row'=>6,'rspan'=>3],
      'e'=>['col'=>6,'cspan'=>2,'row'=>4,'rspan'=>5],
      'f'=>['col'=>6,'cspan'=>2,'row'=>1,'rspan'=>3],
      'g'=>['col'=>8,'cspan'=>3,'row'=>1,'rspan'=>4],
      'h'=>['col'=>8,'cspan'=>3,'row'=>5,'rspan'=>4],
    ]],
  ];
  // Fallback: simple left-to-right flow in 10 columns
  $fallback = function(int $n){
    $pos = []; $col = 1; $row = 1; $letter = 'a';
    for ($i=0; $i<$n; $i++) {
      $pos[$letter] = ['col'=>$col,'cspan'=>2,'row'=>$row,'rspan'=>4];
      $col += 2; if ($col > 10) { $col = 1; $row += 4; }
      $letter = chr(ord($letter)+1);
    }
    return ['cols'=>10,'auto_rows'=>60,'pos'=>$pos];
  };
  $layout = $layouts[$count] ?? $fallback($count);
  // Output compact CSS for only the tiles we render
  if ($count > 0) {
    $letters = range('a', chr(ord('a') + min($count, 26) - 1));
    echo "\n<style>\n";
    printf(
      ".mosaic{ display:grid; gap:12px; grid-auto-rows:%dpx; grid-template-columns: repeat(%d, 1fr); }\n",
      intval($layout['auto_rows']), intval($layout['cols'])
    );
    // Ensure mosaic is hidden on mobile regardless of cascade order
    echo "@media (max-width:680px){ .mosaic{ display:none !important; } .mosaic-shuffle{ display:block; } }\n";
    // Ensure visible next button indicator even if theme CSS is cached
    echo ".mosaic-shuffle{ position:relative; }\n";
    echo ".mosaic-shuffle__next{ position:absolute; right:10px; bottom:10px; padding:8px 12px; border-radius:999px; background:rgba(0,0,0,.62); color:#fff; border:0; display:flex; align-items:center; gap:8px; line-height:1; font-size:13px; font-weight:600; letter-spacing:.2px; box-shadow:0 6px 16px rgba(0,0,0,.25); z-index:2; cursor:pointer; animation:shufflePulse 1.8s ease-out infinite; }\n";
    echo ".mosaic-shuffle__next::after{ content:''; width:18px; height:18px; display:inline-block; background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='8 4 16 12 8 20'/%3E%3C/svg%3E\"); background-repeat:no-repeat; background-position:center; background-size:contain; transform:translateX(0); animation:shuffleNudge 1.2s ease-in-out infinite; }\n";
    echo "@keyframes shufflePulse{ 0%{ box-shadow:0 0 0 0 rgba(255,255,255,.35);} 70%{ box-shadow:0 0 0 10px rgba(255,255,255,0);} 100%{ box-shadow:0 0 0 0 rgba(255,255,255,0);} }\n";
    echo "@keyframes shuffleNudge{ 0%,100%{ transform:translateX(0);} 50%{ transform:translateX(4px);} }\n";
    foreach ($letters as $L) {
      if (!isset($layout['pos'][$L])) continue;
      $p = $layout['pos'][$L];
      printf(
        ".tile.%s{ grid-column:%d / span %d; grid-row:%d / span %d; }\n",
        esc_attr($L), intval($p['col']), intval($p['cspan']), intval($p['row']), intval($p['rspan'])
      );
    }
    echo "</style>\n";
  }
?>
<section class="collection container">
  <h1 class="collection__title"><?php echo esc_html($header_title); ?></h1>
  <p class="text-center"><?php echo esc_html($description); ?></p>
 
  <!-- Mosaic -->
  <div class="mosaic">
    <!-- Big left -->
    <?php 
          $s            = 'a';
        foreach ($items as $card) :
          $img         = $card['img']   ?? '';
          $h           = $card['heading'] ?? '';

    ?>
      <div class="tile <?php echo esc_attr($s); ?>">
        <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($h); ?>">
      </div>
    <?php 
        $s++; 
        endforeach;

    ?>
  </div>
  <?php 
    // Mobile shuffle fallback (hidden on desktop)
    if (!empty($items)):
      $first = $items[0];
      $firstSrc = isset($first['img']) ? esc_url($first['img']) : '';
      $firstAlt = isset($first['heading']) ? esc_attr($first['heading']) : '';
      $imagesPayload = [];
      foreach ($items as $c){
        $imagesPayload[] = [
          'src' => isset($c['img']) ? esc_url($c['img']) : '',
          'alt' => isset($c['heading']) ? esc_attr($c['heading']) : '',
        ];
      }
      $block_id = function_exists('wp_unique_id') ? wp_unique_id('mosaic-shuffle-') : uniqid('mosaic-shuffle-');
  ?>
  <div id="<?php echo esc_attr($block_id); ?>" class="mosaic-shuffle" data-images='{"images":<?php echo esc_attr( wp_json_encode( $imagesPayload ) ); ?>}'>
    <img src="<?php echo $firstSrc; ?>" alt="<?php echo $firstAlt; ?>" loading="lazy" />
    <button class="mosaic-shuffle__next" aria-label="Next image" type="button"></button>
  </div>
  <script>
  (function(){
    var root = document.getElementById('<?php echo esc_js($block_id); ?>');
    if(!root) return;
    try {
      var payload = JSON.parse(root.getAttribute('data-images')) || {};
      var list = Array.isArray(payload.images) ? payload.images : [];
      if(!list.length) return;
      var img = root.querySelector('img');
      var i = 0;
      var advance = function(){
        i = (i + 1) % list.length;
        var next = list[i] || {};
        if(next.src){
          img.classList.add('is-fading');
          setTimeout(function(){
            img.src = next.src; img.alt = next.alt || '';
            img.classList.remove('is-fading');
          }, 120);
        }
      };
      root.addEventListener('click', advance);
      var btn = root.querySelector('.mosaic-shuffle__next');
      if(btn){ btn.addEventListener('click', function(e){ e.preventDefault(); advance();}); }
    } catch(e){}
  })();
  </script>
  <?php endif; ?>
  <!-- Video teaser (kept outside mosaic so it doesn't affect grid) -->
  <div class="video-teaser">
    <iframe width="1236" height="695" src="https://www.youtube.com/embed/yuD34tEpRFw" title="Time Dilation - Einstein&#39;s Theory Of Relativity Explained!" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
  </div>
</section>
