<?php
/**
 * Scrolls the page to the given position if the session storage contains the `shouldScroll` variable
 */

add_action('wp_head', function () {
    ?>
    <script>
        window.addEventListener('DOMContentLoaded', function ()
        {
            var shouldScroll = sessionStorage.getItem('shouldScroll');
            if (shouldScroll)
            {
                console.log('scrolling to', shouldScroll);
                window.scrollTo({
                    top: parseInt(shouldScroll, 10),
                    left: 0,
                    behavior: 'instant'
                });
                sessionStorage.removeItem('shouldScroll');
            }
        });
    </script>
    <?php
}, 1);