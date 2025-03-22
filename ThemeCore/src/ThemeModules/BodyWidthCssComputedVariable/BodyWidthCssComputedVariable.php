<?php
namespace ThemeCore\ThemeModules\BodyWidthCssComputedVariable;

use ThemeCore\ThemeModules\ThemeModule;

class BodyWidthCssComputedVariable extends ThemeModule
{
    private string $cssVariableName;

    protected function __construct(array $config)
    {
        $this->cssVariableName = $config['cssVariableName'];
    }

    public function getFrontendScripts()
    {
        ob_start();
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function ()
            {
                document.documentElement.style.setProperty(
                    "--<?= $this->cssVariableName ?>",
                    `${document.body.clientWidth}px`
                )
            });
            window.addEventListener('resize', () =>
            {
                document.documentElement.style.setProperty('--<?= $this->cssVariableName ?>', `${document.body.clientWidth}px`);
            });
        </script>
        <?php
        return ob_get_clean();
    }

    public function init(): void
    {
        add_action('wp_head', function () {
            echo self::getFrontendScripts();
        }, 1);
    }

}