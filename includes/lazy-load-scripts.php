<?php
global $scriptsToLazyLoad;
// Add the scripts you want to lazy load here (patterns for files from the source/build/ folder)
$scriptsToLazyLoad = [
    // 'app-*.js',
];

add_action('wp_footer', function () {
    ?>
    <script>
    var loaded = false;

    function loadLazyScripts(srcList) {
        if (loaded) return;
        srcList.forEach(function(src) {
            var script = document.createElement('script');
            script.src = src;
            script.type = 'module'
            script.defer = true;
            document.body.appendChild(script);
        });
        loaded = true;
    }
    ["click", "scroll", "keypress", "mousemove", "touchmove"].forEach(function(event) {
        window.addEventListener(event, function() {
            console.log('Load scripts!');
            loadLazyScripts([
                <?php
                global $scriptsToLazyLoad;
                foreach ($scriptsToLazyLoad as $script) {
                    echo '"' . get_template_directory_uri() . '/source/build/' . resolve_source_build_filename($script) . '",';
                }
                ?>
            ]);
        }, {
            once: true
        });
    });
    </script>
    <?php
});
