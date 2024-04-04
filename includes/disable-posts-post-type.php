<?php
function remove_posts_type()
{ // Remove post type links
    remove_menu_page('edit.php');
}

function remove_posts_quickdraft()
{ // Remove "quick drafts" post from dashboard
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
}
function remove_posts_from_menu($wp_admin_bar)
{ // Remove "New post" links
    $wp_admin_bar->remove_node('new-post');
}
add_action('admin_menu', 'remove_posts_type');
add_action('admin_bar_menu', 'remove_posts_from_menu', 9999);
add_action('wp_dashboard_setup', 'remove_posts_quickdraft', 9999);

//if is post post type return 404
function rr_404_my_event($qry)
{
    if (is_singular('post') || is_post_type_archive('post') || is_tax('category') || is_tax('post_tag') || is_tax('post_format')) {
        $qry->set_404();
        status_header(404);
    }
}
add_action('pre_get_posts', 'rr_404_my_event');

//add noindex tag to a post "post" post_type and "category", "post_tag", "post_format" taxonomies
add_filter('wp_robots', function ($robots) {
    if (is_singular('post') || is_post_type_archive('post') || is_tax('category') || is_tax('post_tag') || is_tax('post_format')) {
        $robots['noindex'] = true;
    }
    return $robots;
});
