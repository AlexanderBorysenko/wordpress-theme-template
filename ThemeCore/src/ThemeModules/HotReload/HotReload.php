<?php
namespace ThemeCore\ThemeModules\HotReload;

use Exception;
use ThemeCore\ThemeModules\ThemeModule;

/**
 * Handler for hot reloading functionality in development environment
 */
class HotReload extends ThemeModule
{
    private $triggers;

    protected function __construct(array $config = [])
    {
        $this->triggers = $config['triggers'];
    }

    /**
     * Get a last modified time of the target files for a hot reloader
     *
     * @return int Latest modification timestamp
     */
    public function getLatestHotReloadTriggerUpdateTime()
    {
        $versions = [];

        foreach ($this->triggers as $targetFile) {
            try {
                $version = filemtime(getThemeFilePath($targetFile));
                if ($version)
                    $versions[] = $version;
            } catch (Exception $e) {
                continue;
            }
        }

        return !empty($versions) ? max($versions) : 0;
    }

    /**
     * Inject hot reload JavaScript into the page head
     */
    public function getHotReloadScript()
    {
        ob_start();
        ?>
        <script>
            var currentVersion = <?= $this->getLatestHotReloadTriggerUpdateTime() ?>;
            setInterval(function ()
            {
                fetch('/wp-admin/admin-ajax.php?action=get_current_version')
                    .then(response => response.json())
                    .then(version =>
                    {
                        if (!version) return;

                        if (version !== currentVersion)
                        {
                            sessionStorage.setItem('scrollPosition', window.scrollY);
                            setTimeout(function ()
                            {
                                location.reload();
                            }, 1000); // Wait 1 second before reload
                        }
                    });
            }, 1000);
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Initialize the hot reloader functionality
     */
    public function init()
    {
        registerAjaxAction('get_current_version', function () {
            echo json_encode($this->getLatestHotReloadTriggerUpdateTime());
            wp_die();
        });

        // Add hot reload script for administrators
        if (
            current_user_can('administrator') &&
            count($this->triggers) > 0
        ) {
            add_action(
                'wp_head',
                function () {
                    echo $this->getHotReloadScript();
                },
                1
            );
        }
    }

}