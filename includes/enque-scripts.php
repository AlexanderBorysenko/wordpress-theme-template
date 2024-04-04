<?php
function clear_styles_and_scripts()
{
    if (is_user_logged_in())
        return;

    global $wp_scripts;
    global $wp_styles;

    foreach ($wp_scripts->queue as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }

    foreach ($wp_styles->queue as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
}
add_action('wp_enqueue_scripts', 'clear_styles_and_scripts', 100);

function wp_theme_scripts()
{
    wp_enqueue_style('wp_theme-main-style', get_template_directory_uri() . '/source/build/' . resolveBuildFileName('app-*.css'), array());
}
add_action('wp_enqueue_scripts', 'wp_theme_scripts', 101);

function wp_theme_admin_scripts()
{
    wp_enqueue_style('wp_theme-admin-style', get_template_directory_uri() . '/source/build/' . resolveBuildFileName('wp-admin-*.css'), array());
    // wp_enqueue_script('wp_theme-wp-admin-js', get_template_directory_uri() . '/build/wp-admin-' . _S_VERSION . '.min.js', array(), _S_VERSION, true);
}
add_action('admin_enqueue_scripts', 'wp_theme_admin_scripts');