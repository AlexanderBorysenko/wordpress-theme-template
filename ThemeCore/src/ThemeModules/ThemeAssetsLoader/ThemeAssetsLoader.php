<?php
namespace ThemeCore\ThemeModules\ThemeAssetsLoader;

use ThemeCore\ThemeModules\ThemeModule;

/**
 * Theme Assets Handler class
 * 
 * Default config keys:
 * - js: array of JavaScript files to load on the frontend
 * - lazyJs: array of JavaScript files to load lazily (default: [])
 * - adminJs: array of JavaScript files to load in admin area
 * - css: array of CSS files to load on the frontend
 * - adminCss: array of CSS files to load in admin area
 * - inlineHeadJs: array of JavaScript files to include inline in the head (default: [])
 * - inlineFooterJs: array of JavaScript files to include inline in the footer (default: [])
 *
 * @param array $config Configuration array with assets
 */
class ThemeAssetsLoader extends ThemeModule
{
    private $config;

    /**
     * ThemeAssetsLoader constructor.
     * 
     * @param array $config - Configuration for the assets loader
     * @throws \Exception
     */
    protected function __construct(array $config = [])
    {
        $this->config = $config;

        if (!is_array($this->config)) {
            throw new \Exception('Invalid config provided for ThemeAssetsLoader');
        }
        if (!array_key_exists('js', $this->config)) {
            throw new \Exception('Invalid config provided for ThemeAssetsLoader. Missing "js" key');
        }
        if (!array_key_exists('lazyJs', $this->config)) {
            $this->config['lazyJs'] = [];
        }
        if (!array_key_exists('inlineHeadJs', $this->config)) {
            $this->config['inlineHeadJs'] = [];
        }
        if (!array_key_exists('inlineFooterJs', $this->config)) {
            $this->config['inlineFooterJs'] = [];
        }
        if (!array_key_exists('adminJs', $this->config)) {
            throw new \Exception('Invalid config provided for ThemeAssetsLoader. Missing "adminJs" key');
        }
        if (!array_key_exists('css', $this->config)) {
            throw new \Exception('Invalid config provided for ThemeAssetsLoader. Missing "css" key');
        }
        if (!array_key_exists('adminCss', $this->config)) {
            throw new \Exception('Invalid config provided for ThemeAssetsLoader. Missing "adminCss" key');
        }
    }

    /**
     * Disable all default WordPress frontend assets.
     */
    public function disableDefaultAssets()
    {
        // Skip for logged in users.
        if (is_user_logged_in()) {
            return;
        }

        // Only run on the frontend.
        if (!is_admin()) {
            global $wp_styles;

            $deque_styles = [];
            foreach ($wp_styles->queue as $handle) :
                $deque_styles[] = $handle;
            endforeach;

            wp_dequeue_style($deque_styles);

            global $wp_scripts;
            if (isset($wp_scripts) && is_object($wp_scripts) && property_exists($wp_scripts, 'queue')) {
                $deque_scripts = [];
                foreach ($wp_scripts->queue as $handle) :
                    $deque_scripts[] = $handle;
                endforeach;
                wp_dequeue_script($deque_scripts);
            }

            remove_action('wp_print_styles', 'print_emoji_styles');
        }
    }

    /**
     * Enqueue scripts and styles based on configuration.
     */
    public function enqueueAssets()
    {
        $jsAssets  = $this->config['js'];
        $cssAssets = $this->config['css'];

        foreach ($jsAssets as $jsAsset) {
            wp_enqueue_script("wp_theme-$jsAsset", getThemeFileUri($jsAsset), [], null, true);
        }

        foreach ($cssAssets as $cssAsset) {
            $cssUri = getThemeFileUri($cssAsset);
            wp_enqueue_style("wp_theme-$cssAsset", $cssUri);
        }
    }

    /**
     * Modify the output of enqueued CSS to preload them.
     */
    public function modifyStyleLoaderTags($html, $handle, $href, $media, $prefix = 'wp_theme-')
    {
        // Target only our theme's CSS handles.
        if (strpos($handle, $prefix) === 0) {
            $html = "<link rel='preload' href='$href' as='style' onload=\"this.onload=null;this.rel='stylesheet'\" media='$media' />";
            $html .= "<link rel='stylesheet' href='$href'>";
        }
        return $html;
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public function enqueueAdminAssets()
    {
        $adminJsAssets  = $this->config['adminJs'];
        $adminCssAssets = $this->config['adminCss'];

        foreach ($adminJsAssets as $jsAsset) {
            wp_enqueue_script("wp_theme-$jsAsset", getThemeFileUri($jsAsset), [], null, true);
        }

        foreach ($adminCssAssets as $cssAsset) {
            wp_enqueue_style("wp_theme-$cssAsset", getThemeFileUri($cssAsset));
        }
    }

    /**
     * Insert inline JavaScript in the head.
     */
    public function insertInlineHeadJs()
    {
        $inlineHeadJsFiles = $this->config['inlineHeadJs'];

        if (!empty($inlineHeadJsFiles)) {
            foreach ($inlineHeadJsFiles as $jsFile) {
                $jsFilePath = getThemeFilePath($jsFile);
                if (file_exists($jsFilePath)) {
                    echo '<script type="module">';
                    echo file_get_contents($jsFilePath);
                    echo '</script>';
                }
            }
        }
    }

    /**
     * Insert inline JavaScript in the footer.
     */
    public function insertInlineFooterJs()
    {
        $inlineFooterJsFiles = $this->config['inlineFooterJs'];

        if (!empty($inlineFooterJsFiles)) {
            foreach ($inlineFooterJsFiles as $jsFile) {
                $jsFilePath = get_template_directory() . '/' . $jsFile;
                if (file_exists($jsFilePath)) {
                    echo '<script type="module">';
                    echo file_get_contents($jsFilePath);
                    echo '</script>';
                }
            }
        }
    }

    /**
     * Enqueue lazy load scripts.
     */
    public function enqueueLazyJs()
    {
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
                        $lazyloadJsAssets = $this->config['lazyJs'];
                        foreach ($lazyloadJsAssets as $script) {
                            $sourceBuildFileUri = getThemeFileUri($script);
                            if ($sourceBuildFileUri) {
                                echo "'{$sourceBuildFileUri}',";
                            } else {
                                echo "'{$script}',";
                            }
                        }
                        ?>
                    ]);
                }, { once: true });
            });
        </script>
        <?php
    }

    public function init()
    {
        /**
         * Disable default WP assets
         */
        add_action('wp_enqueue_scripts', function () {
            $this->disableDefaultAssets();
        }, 100);

        /**
         * Enqueue theme assets for front-end
         */
        add_action('wp_enqueue_scripts', function () {
            $this->enqueueAssets();
        }, 101);

        /**
         * Modify style loader tags to add preload attributes
         */
        add_filter('style_loader_tag', function () {
            return $this->modifyStyleLoaderTags(...func_get_args());
        }, 10, 4);

        /**
         * Enqueue assets for admin side only
         */
        add_action('admin_enqueue_scripts', function () {
            $this->enqueueAdminAssets();
        });

        /**
         * Insert inline JavaScript in the head
         */
        add_action('wp_head', function () {
            $this->insertInlineHeadJs();
        });

        /**
         * Insert inline JavaScript in the footer
         */
        add_action('wp_footer', function () {
            $this->insertInlineFooterJs();
        }, 5);

        /**
         * Add script to lazy load some js files
         */
        add_action('wp_footer', function () {
            $this->enqueueLazyJs();
        });
    }

}
