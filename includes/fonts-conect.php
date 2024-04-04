<?php
function connect_fonts()
{
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anek+Latin:wdth,wght@75..125,100..800&display=swap"
        rel="stylesheet">
    <?php
}
add_action('wp_head', 'connect_fonts');
add_action('admin_head', 'connect_fonts');