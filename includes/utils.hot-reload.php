<?php
/**
 * Hot reloads the page when the target files are changed
 */

function get_version()
{
    $hotReloadTriggers = get_config('hotReloadTriggers', []);
    $versions = [];

    foreach ($hotReloadTriggers as $targetFile) {
        try {
            $version = filemtime(get_source_build_file_uri($targetFile));
            if ($version)
                $versions[] = $version;
        } catch (Exception $e) {
            continue;
        }
    }

    // Return the latest version(timestamp) of the target files
    return max($versions);
}
function ajax_get_version()
{
    wp_send_json(get_version());
    wp_die();
}

add_action('wp_ajax_get_current_version', 'ajax_get_version');
add_action('wp_ajax_nopriv_get_current_version', 'ajax_get_version');

function handle_hot_reload()
{
    ?>
    <script>
        var currentVersion = <?= get_version() ?>;
        setInterval(function ()
        {
            fetch('/wp-admin/admin-ajax.php?action=get_current_version')
                .then(response => response.json())
                .then(version =>
                {
                    if (!version) return;

                    if (version !== currentVersion)
                    {
                        sessionStorage.setItem('shouldScroll', window.scrollY);
                        location.reload();
                    }
                });
        }, 500);
    </script>
    <?php
}

if (
    current_user_can('administrator') &&
    count(get_config('hotReloadTriggers', [])) > 0
) {
    add_action('wp_head', 'handle_hot_reload', 1);
}
