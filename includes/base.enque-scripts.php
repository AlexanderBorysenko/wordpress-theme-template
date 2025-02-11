<?php
/**
 * Disable default WordPress frontend assets.
 *
 * This function removes:
 * - Emoji detection scripts and styles.
 * - The wp-embed script.
 * - Gutenberg block styles (including global styles from theme.json).
 * - WooCommerce block styles (if applicable).
 * - Dashicons (optional).
 * - jQuery Migrate (optional for non-logged-in users).
 */
function disable_default_wp_assets()
{
    // Only run on the frontend.
    if (!is_admin()) {
        // Remove emoji support.
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        // Also remove emoji support in admin if desired (optional):
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');

        // Remove the embed script.
        wp_dequeue_script('wp-embed');

        // Remove Gutenberg block styles.
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('global-styles'); // For themes using theme.json

        // Remove WooCommerce block styles if WooCommerce is active.
        if (function_exists('is_woocommerce')) {
            wp_dequeue_style('wc-block-style');
        }

        // Optionally, remove dashicons on the frontend.
        wp_dequeue_style('dashicons');

        // Optionally, deregister jQuery Migrate for non-logged-in users.
        if (!is_user_logged_in()) {
            wp_deregister_script('jquery-migrate');
        }
    }
}
add_action('wp_enqueue_scripts', 'disable_default_wp_assets', 100);


/** 
 * Enqueue scripts and styles here
 *
 * @return void
 */
function wp_theme_assets()
{
    $jsAssets = get_config('assets.js', []);
    $cssAssets = get_config('assets.css', []);

    foreach ($jsAssets as $jsAsset) {
        wp_enqueue_script("wp_theme-$jsAsset", get_source_build_file_uri($jsAsset), [], null, true);
    }

    foreach ($cssAssets as $cssAsset) {
        wp_enqueue_style("wp_theme-$cssAsset", get_source_build_file_uri($cssAsset));
    }
}

add_action('wp_enqueue_scripts', 'wp_theme_assets', 101);

function wp_theme_admin_assets()
{
    $adminJsAssets = get_config('assets.adminJs', []);
    $adminCssAssets = get_config('assets.adminCss', []);

    foreach ($adminJsAssets as $jsAsset) {
        wp_enqueue_script("wp_theme-$jsAsset", get_source_build_file_uri($jsAsset), [], null, true);
    }

    foreach ($adminCssAssets as $cssAsset) {
        wp_enqueue_style("wp_theme-$cssAsset", get_source_build_file_uri($cssAsset));
    }
}

add_action('admin_enqueue_scripts', 'wp_theme_admin_assets');

add_action('wp_footer', function () {
    ?>
    <script>
        var loaded = false;

        function loadLazyScripts(srcList)
        {
            if (loaded) return;
            console.log('Load scripts!');
            srcList.forEach(function (src)
            {
                var script = document.createElement('script');
                script.src = src;
                script.type = 'module'
                script.defer = true;
                document.body.appendChild(script);
            });
            loaded = true;
        }
        ["click", "scroll", "keypress", "mousemove", "touchmove", "touchstart"].forEach(function (event)
        {
            window.addEventListener(event, function ()
            {
                loadLazyScripts([
                    <?php
                    $lazyloadJsAssets = get_config('assets.lazyJs', []);
                    foreach ($lazyloadJsAssets as $script) {
                        $sourceBuildFileUri = get_source_build_file_uri($script);
                        if ($sourceBuildFileUri) {
                            echo "$sourceBuildFileUri,\n";
                        } else {
                            echo "$script,\n";
                        }
                    }
                    ?>
                ]);
            }, { once: true });
        });
    </script>
    <?php
});