<?php
namespace ThemeCore\ThemeModules\PreventOnLoadCssTransitions;

use Exception;
use ThemeCore\ThemeModules\ThemeModule;

/**
 * Handler for hot reloading functionality in development environment
 */
class PreventOnLoadCssTransitions extends ThemeModule
{
    private $triggers;

    protected function __construct(array $config = [])
    {
    }

    /**
     * Inject hot reload JavaScript into the page head
     */
    public function getFrontendCode()
    {
        ob_start();
        ?>
        <style>
            html:not(.allow-transitions) * {
                transition: none !important;
                animation: none !important;
            }
        </style>
        <script>
            window.addEventListener('load', function ()
            {
                setTimeout(function ()
                {
                    document.documentElement.classList.add('allow-transitions');
                }, 100);
            });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Initialize the hot reloader functionality
     */
    public function init()
    {
        add_action(
            'wp_head',
            function () {
                echo $this->getFrontendCode();
            },
            1
        );
    }

}