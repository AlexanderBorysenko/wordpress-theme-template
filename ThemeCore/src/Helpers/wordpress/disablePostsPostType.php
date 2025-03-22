<?php
function disablePostsPostType()
{
    /**
     * Remove "posts" post type from admin menu.
     */
    add_action('admin_menu', function () {
        remove_menu_page('edit.php');
    });

    /**
     * Remove "posts" post type from admin bar.
     */
    add_action(
        'admin_bar_menu',
        function ($wpAdminBar) {
            $wpAdminBar->remove_node('new-post');
        },
        9999
    );

    /**
     * Remove "posts" post type from dashboard.
     */
    add_action('wp_dashboard_setup', function () {
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    }, 9999);

    /**
     * Disable "posts" post type from query.
     */
    add_action('pre_get_posts', function ($qry) {
        if (is_singular('post') || is_post_type_archive('post') || is_tax('category') || is_tax('post_tag') || is_tax('post_format')) {
            $qry->set_404();
            status_header(404);
        }
    });

    /**
     * Make sure "posts" post type is not indexed by search engines.
     */
    add_filter('wp_robots', function ($robots) {
        if (is_singular('post') || is_post_type_archive('post') || is_tax('category') || is_tax('post_tag') || is_tax('post_format')) {
            $robots['noindex'] = true;
        }
        return $robots;
    });

}