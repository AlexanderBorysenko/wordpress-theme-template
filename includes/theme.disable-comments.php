<?php
// disble wordpress comments
function disable_comments()
{
    // Removes from admin menu
    remove_menu_page('edit-comments.php');
    // Removes from post and pages
    remove_post_type_support('post', 'comments');
    remove_post_type_support('page', 'comments');
    // Removes from admin bar
    remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    // Removes from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    // Removes from quick edit
    add_filter('quick_edit_dropdown_pages_args', function ($args) {
        $args['meta_key'] = '_wp_page_template';
        $args['meta_value'] = 'page-templates/page-quick-possession.php';
        return $args;
    });
}

add_action('admin_init', 'disable_comments');