<?php
namespace ThemeCore\ThemeModules\PostColorTheme;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use ThemeCore\ThemeModules\ThemeModule;

class PostColorTheme extends ThemeModule
{
    /** @var array */
    protected $config;

    /** @var array */
    protected $themes;

    /** @var string */
    protected $defaultTheme;

    /** @var array */
    protected $postTypes = [];

    /**
     * Initialize the PostColorTheme module.
     */
    protected function __construct($config = [])
    {
        includePhpFiles(path_join(__DIR__, 'helpers'));

        $this->config = $config;

        if (empty($config['themes'])) {
            throw new \Exception('Please provide "themes" config parameter for ColorTheme module like this: "themes" => ["light-theme" => "Light", "dark-theme" => "Dark"]');
        }

        $this->themes       = $config['themes'];
        $this->defaultTheme = $config['default_theme'] ?? array_key_first($this->themes);
        $this->postTypes    = $config['post_types'] ?? [];
    }

    /**
     * Initialize module functionality.
     */
    public function init()
    {
        $this->registerMetaFields();
        $this->addBodyClasses();
        $this->setupAdminScripts();
    }

    /**
     * Register theme selection fields.
     */
    protected function registerMetaFields()
    {
        add_action('carbon_fields_register_fields', function () {
            Container::make('post_meta', 'Theme Color')
                ->where('post_type', 'IN', $this->postTypes)
                ->add_fields([
                    Field::make('select', 'crb_theme', 'Theme')
                        ->add_options($this->themes)
                        ->set_default_value($this->defaultTheme)
                ])->set_context('side');
        });
    }

    /**
     * Add theme class to body.
     */
    protected function addBodyClasses()
    {
        add_filter('body_class', function ($classes) {
            return array_merge($classes, [$this->getPostColorTheme()]);
        });
    }

    /**
     * Set up admin scripts.
     */
    protected function setupAdminScripts()
    {
        add_action('admin_footer', function () {
            echo $this->getAdminInlineScript();
        });
    }

    /**
     * Get the current post's color theme.
     */
    public function getPostColorTheme()
    {
        return carbon_get_the_post_meta('crb_theme') ?: $this->defaultTheme;
    }

    public function getAvailableThemes()
    {
        return $this->themes;
    }

    /**
     * Get admin JavaScript for theme preview.
     */
    public function getAdminInlineScript()
    {
        ob_start();
        ?>
        <script>
            (function ($)
            {
                $(document).ready(function ()
                {
                    const themeSelect = $('select[name*="[_crb_theme]"]')[0];
                    if (!themeSelect) return;

                    function applyTheme(themeName)
                    {
                        if (document.body.dataset.theme)
                        {
                            document.body.classList.remove(document.body.dataset.theme);
                        }
                        document.body.classList.add(themeName);
                        document.body.dataset.theme = themeName;
                    }

                    // Apply initial theme
                    applyTheme(themeSelect.value);

                    // Listen for changes
                    $(themeSelect).on('change', function ()
                    {
                        applyTheme(this.value);
                    });
                });
            })(jQuery);
        </script>
        <?php
        return ob_get_clean();
    }

}