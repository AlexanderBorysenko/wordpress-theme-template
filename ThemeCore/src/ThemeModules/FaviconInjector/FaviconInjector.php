<?php
namespace ThemeCore\ThemeModules\FaviconInjector;

use ThemeCore\ThemeModules\ThemeModule;

class FaviconInjector extends ThemeModule
{
    private $base;
    private $pattern;

    protected function __construct($config = [])
    {
        $this->base    = $config['base'];
        $this->pattern = $config['pattern'];
    }

    /**
     * Initialize the favicon functionality
     */
    public function init()
    {
        // Remove default WordPress site icon
        add_action('wp_head', [
            $this,
            'removeDefaultSiteIcon'
        ], 1);

        // Add our custom favicon links
        add_action('wp_head', function () {
            echo $this->getFaviconLinkTags();
        });
        add_action('admin_head', function () {
            echo $this->getFaviconLinkTags();
        });
        add_action('login_head', function () {
            echo $this->getFaviconLinkTags();
        });
    }

    /**
     * Remove WordPress default site icon
     */
    public function removeDefaultSiteIcon()
    {
        remove_action('wp_head', 'wp_site_icon', 99);
    }

    /**
     * Inject custom favicon links into head
     */
    public function getFaviconLinkTags()
    {
        ob_start();
        $faviconDir = path_join(get_template_directory(), $this->base);
        $faviconUrl = path_join(get_template_directory_uri(), $this->base);

        // Get all .png files in the favicon directory
        $favicons = glob("{$faviconDir}/*.png");

        if (empty($favicons)) {
            return;
        }

        foreach ($favicons as $favicon) {
            $filename = basename($favicon);

            // Skip if filename doesn't match the icon pattern
            if (!preg_match($this->pattern, $filename, $matches)) {
                continue;
            }

            $size       = $matches[1];
            $dimensions = "{$size}x{$size}";

            echo sprintf(
                "<link rel=\"icon\" type=\"image/png\" sizes=\"%s\" href=\"%s/%s\">\n",
                $dimensions,
                $faviconUrl,
                $filename
            );
        }
        return ob_get_clean();
    }

}