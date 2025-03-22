<?php
namespace ThemeCore\ThemeModules\DocumentScrollbarWidthCssVariable;

use ThemeCore\ThemeModules\ThemeModule;

class DocumentScrollbarWidthCssVariable extends ThemeModule
{
    public function getFrontendScripts()
    {
        ob_start();
        ?>
        <script>
            function setScrollbarWidth()
            {
                document.documentElement.style.setProperty('--scrollbar-width', (window.innerWidth - document.documentElement.offsetWidth) + 'px');
            }
            setScrollbarWidth();
            document.addEventListener('resize', setScrollbarWidth);
        </script>
        <?php
        return ob_get_clean();
    }

    public function init(): void
    {
        add_action('wp_footer', function () {
            echo self::getFrontendScripts();
        }, 1);
    }

}