<?php
/**
 * All Inclusive – Twenty Twenty-Five Child
 */

/* -----------------------  Assets  ----------------------- */
add_action('wp_enqueue_scripts', function () {
  // OPTIONAL: If you want Bootstrap to dominate and reduce block theme globals,
  // uncomment the next line. (It may affect core block styling.)
  // remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');

  $dir = get_stylesheet_directory();
  $uri = get_stylesheet_directory_uri();

  // Small helpers
  $ver = function ($rel) use ($dir) {
    $f = $dir . $rel;
    return file_exists($f) ? filemtime($f) : null;
  };
  $add_style = function ($handle, $rel, $deps = []) use ($uri, $ver) {
    $file_uri = $uri . $rel;
    $v = $ver($rel);
    if ($v !== null) wp_enqueue_style($handle, $file_uri, $deps, $v);
  };
  $add_script = function ($handle, $rel, $deps = [], $in_footer = true) use ($uri, $ver) {
    $file_uri = $uri . $rel;
    $v = $ver($rel);
    if ($v !== null) wp_enqueue_script($handle, $file_uri, $deps, $v, $in_footer);
  };

  /* ---- CSS: vendor first, then your layers ---- */
  // Bootstrap
  wp_enqueue_style(
    'bootstrap',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    [],
    '5.3.3'
  );

  // Parent & child styles (keep light; your real CSS lives in /assets/css/*)
  // Parent first (optional but safe), then child (style.css with theme header / tiny globals)
  wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css', ['bootstrap']);
  wp_enqueue_style('child-style', get_stylesheet_uri(), ['bootstrap','parent-style']);

  // Global layout CSS (site-wide)
  $add_style('ai-header', '/assets/css/layout/header.css', ['child-style']);
  $add_style('ai-footer', '/assets/css/layout/footer.css', ['ai-header']);

  // Page-specific CSS
  if (is_front_page()) {
    $add_style('ai-homepage', '/assets/css/pages/homepage.css', ['ai-footer']);
  }
  // Examples to extend as you create files:
  // if (is_page('about'))      { $add_style('ai-about', '/assets/css/pages/about.css', ['ai-footer']); }
  // if (is_page('blogs') || is_home()) { $add_style('ai-blogs', '/assets/css/pages/blogs.css', ['ai-footer']); }

  /* ---- JS: vendor then your script(s) ---- */
  wp_enqueue_script(
    'bootstrap',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
    [],
    '5.3.3',
    true
  );

  // Your main JS (menus, mobile submenu, hero bg carousel init, etc.)
  $add_script('ai-main', '/assets/js/main.js', ['bootstrap'], true);
});

/* -------------------  Disable editor on “home” (optional)  ------------------- */
function ai_disable_editor_on_home($can_edit, $post) {
  if (is_admin() && $post && $post->post_name === 'home') return false;
  return $can_edit;
}
add_filter('use_block_editor_for_post', 'ai_disable_editor_on_home', 10, 2);


/* -----------------------  Custom blocks  ----------------------- */
add_action('init', function () {
  register_block_type_from_metadata(__DIR__ . '/blocks/hero-banner');
  register_block_type_from_metadata(__DIR__ . '/blocks/carousel');
  register_block_type_from_metadata(__DIR__ . '/blocks/carousel-infinite');
});


/* -------- Redirect unknown front-end pages to Home and show a toast -------- */
add_action('template_redirect', function () {
  if (is_admin() || wp_doing_ajax()) return;

  $rest_prefix = function_exists('rest_get_url_prefix') ? rest_get_url_prefix() : 'wp-json';
  $uri = $_SERVER['REQUEST_URI'] ?? '';

  // Skip REST requests
  if (
    (defined('REST_REQUEST') && constant('REST_REQUEST')) ||
    strncmp($uri, '/' . $rest_prefix . '/', strlen('/' . $rest_prefix . '/')) === 0 ||
    (isset($_GET['rest_route']) && $_GET['rest_route'] !== '')
  ) return;

  $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
  $wants_html = (stripos($accept, 'text/html') !== false);

  if ($wants_html && is_404() && !is_front_page()) {
    $target = add_query_arg('notice', 'missing', home_url('/'));
    wp_safe_redirect($target, 302);
    exit;
  }
});

add_action('wp_footer', function () {
  if (!isset($_GET['notice']) || $_GET['notice'] !== 'missing') return; ?>
  <div class="toast-container position-fixed top-0 end-0 p-4" style="z-index:2000">
    <div id="missingToast" class="toast text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-body">
        <p class="text-white fs-6 fw-semibold mb-0">
          The page you’re looking for doesn’t exist or has moved. You were redirected to Home.
        </p>
      </div>
    </div>
  </div>
  <script>
    (function () {
      var el = document.getElementById('missingToast');
      if (el && window.bootstrap && bootstrap.Toast) {
        new bootstrap.Toast(el, { delay: 5000 }).show();
      } else {
        el && (el.style.display = 'block');
        setTimeout(function(){ el && (el.style.display='none'); }, 3000);
      }
      if (history.replaceState) {
        var url = new URL(window.location.href);
        url.searchParams.delete('notice');
        history.replaceState({}, '', url.pathname + (url.search ? '?' + url.search : '') + url.hash);
      }
    })();
  </script>
<?php });

// Render Gutenberg blocks inside excerpts (so SSR blocks appear).
add_filter( 'the_excerpt', 'do_blocks', 9 );

/* -----------------------  Block bots (front-end)  ----------------------- */
add_action('send_headers', function () {
  if (!is_admin()) {
    header('X-Robots-Tag: noindex, nofollow', true);
  }
});
