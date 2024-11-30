<?php
/**
 * Enqueue scripts and styles here
 *
 * @return void
 */
function c_valley_scripts()
{
    wp_enqueue_style('c_valley-main-style', get_template_directory_uri() . '/source/build/' . resolve_source_build_filename('app-*.css'), array());
    wp_enqueue_script('c_valley-main-script', get_template_directory_uri() . '/source/build/' . resolve_source_build_filename('app-*.js'), array(), null, true);
}

add_action('wp_enqueue_scripts', 'c_valley_scripts', 101);

// connect wp-admin styles
function c_valley_admin_styles()
{
    wp_enqueue_style('c_valley-admin-styles', get_template_directory_uri() . '/source/build/' . resolve_source_build_filename('wp-admin-*.css'));
    wp_enqueue_script('c_valley-admin-scripts', get_template_directory_uri() . '/source/build/' . resolve_source_build_filename('wp-admin-*.js'), array(), null, true);
}

add_action('admin_enqueue_scripts', 'c_valley_admin_styles');

// lazyload scripts urls
global $scriptsToLazyLoad;
$scriptsToLazyLoad = [
    // get_template_directory_uri() . '/source/build/' . resolve_source_build_filename('app-*.js')
];

add_action('wp_footer', function () {
    ?>
    <script>
        var loaded = false;

        function loadLazyScripts(srcList)
        {
            if (loaded) return;
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
        ["click", "scroll", "keypress", "mousemove", "touchmove"].forEach(function (event)
        {
            window.addEventListener(event, function ()
            {
                console.log('Load scripts!');
                loadLazyScripts([
                    <?php
                    global $scriptsToLazyLoad;
                    foreach ($scriptsToLazyLoad as $script) {
                        echo $script;
                    }
                    ?>
                ]);
            }, { once: true });
        });
    </script>
    <?php
});