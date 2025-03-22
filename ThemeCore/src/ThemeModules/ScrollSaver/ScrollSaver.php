<?php
namespace ThemeCore\ThemeModules\ScrollSaver;

use ThemeCore\ThemeModules\ThemeModule;

class ScrollSaver extends ThemeModule
{
    protected function __construct(array $config = [])
    {
    }

    public function getFrontendScripts()
    {
        ob_start();
        ?>
        <script>
            window.addEventListener('DOMContentLoaded', function ()
            {
                var scrollPosition = sessionStorage.getItem('scrollPosition');
                if (scrollPosition)
                {
                    window.scrollTo({
                        top: parseInt(scrollPosition, 10),
                        left: 0,
                        behavior: 'instant'
                    });
                    sessionStorage.removeItem('scrollPosition');
                }
            });
        </script>
        <?php
        return ob_get_clean();
    }

    public function init()
    {
        add_action('wp_head', function () {
            echo $this->getFrontendScripts();
        }, 1);
    }

}