<?php
    function twentytwentyfive_child_enqueue_styles() {
        // Load parent theme styles
        wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

        // Load child theme styles
        wp_enqueue_style('child-style', get_stylesheet_uri(), array('parent-style'));

        // Load Bootstrap CSS
        wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');

        // Load Bootstrap JS
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), null, true);

        // Load your custom JS
        wp_enqueue_script(
            'custom-js',
            get_stylesheet_directory_uri() . '/assets/js/main.js',
            array(),
            null,
            true
        );
    }
    add_action('wp_enqueue_scripts', 'twentytwentyfive_child_enqueue_styles');

    // Optional: Disable block editor on "home" page
    function disable_editor_on_home($can_edit, $post) {
        if (is_admin() && $post && $post->post_name === 'home') {
            return false;
        }
        return $can_edit;
    }
    add_filter('use_block_editor_for_post', 'disable_editor_on_home', 10, 2);

    // Register the block exactly once, using metadata.
    add_action('init', function () {
      register_block_type_from_metadata(__DIR__ . '/blocks/section-cards');
    });

    add_action('init', function () {
        register_block_type_from_metadata(__DIR__ .  '/blocks/section-split-features');
    });

    add_action('init', function () {
        register_block_type(__DIR__ . '/blocks/archive-grid');
    });

    // Redirect unknown *front-end* pages to Home and show a toast.
    // Do NOT run for admin, AJAX, or REST requests.
    add_action('template_redirect', function () {
    // Never interfere with admin/AJAX
    if ( is_admin() || wp_doing_ajax() ) return;

    // Resolve REST prefix safely (defaults to wp-json)
    $rest_prefix = function_exists('rest_get_url_prefix') ? rest_get_url_prefix() : 'wp-json';
    $uri = $_SERVER['REQUEST_URI'] ?? '';

    // Skip REST (both constant + /wp-json/... + ?rest_route=)
    if (
        (defined('REST_REQUEST') && constant('REST_REQUEST')) ||               // <-- no IDE warning
        strncmp($uri, '/'.$rest_prefix.'/', strlen('/'.$rest_prefix.'/')) === 0 ||
        (isset($_GET['rest_route']) && $_GET['rest_route'] !== '')
    ) {
        return;
    }

    // Only redirect normal HTML 404s
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $wants_html = (stripos($accept, 'text/html') !== false);

    if ( $wants_html && is_404() && ! is_front_page() ) {
        $target = add_query_arg('notice', 'missing', home_url('/'));
        wp_safe_redirect($target, 302);
        exit;
    }
    });

    // Show a Bootstrap toast when redirected from a missing page
    add_action('wp_footer', function () {
    if (!isset($_GET['notice']) || $_GET['notice'] !== 'missing') return;
    ?>
        <div class="toast-container position-fixed top-0 end-0 p-4" style="z-index:2000">
            <div id="missingToast"
                class="toast text-bg-danger border-0"
                role="alert" aria-live="assertive" aria-atomic="true">
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
                var t = new bootstrap.Toast(el, { delay: 5000 });
                t.show();
            } else {
                // super simple fallback
                el && (el.style.display = 'block');
                setTimeout(function(){ el && (el.style.display='none'); }, 3000);
            }

            // Clean the URL so ?notice=missing disappears after load
            if (history.replaceState) {
                var url = new URL(window.location.href);
                url.searchParams.delete('notice');
                history.replaceState({}, '', url.pathname + (url.search ? '?' + url.search : '') + url.hash);
            }
            })();
        </script>
    <?php
    });

    // Block bots and crawlers from indexing the site
    function block_all_bots() {
        if (!is_admin()) { // Only on frontend
            header('X-Robots-Tag: noindex, nofollow', true);
        }
    }
    add_action('send_headers', 'block_all_bots');
