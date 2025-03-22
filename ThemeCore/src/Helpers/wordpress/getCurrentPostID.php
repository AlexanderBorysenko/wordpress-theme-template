<?php
/**
 * Retrieves the ID of the current page
 *
 * @return int The ID of the current page
 */
function getCurrentPostID()
{
    static $current_page_ID;

    global $post, $wp_query, $wpdb;

    if (!is_null($wp_query) && isset($wp_query->post) && isset($wp_query->post->ID) && !empty($wp_query->post->ID))
        $current_page_ID = $wp_query->post->ID;
    else if (function_exists('get_the_id') && !empty(get_the_id()))
        $current_page_ID = get_the_id();
    else if (!is_null($post) && isset($post->ID) && !empty($post->ID))
        $current_page_ID = $post->ID;
    else if ((isset($_GET['action']) && sanitize_text_field($_GET['action']) == 'edit') && $post = ((isset($_GET['post']) && is_numeric($_GET['post'])) ? absint($_GET['post']) : false))
        $current_page_ID = $post;
    else if ($p = ((isset($_GET['p']) && is_numeric($_GET['p'])) ? absint($_GET['p']) : false))
        $current_page_ID = $p;
    else if ($page_id = ((isset($_GET['page_id']) && is_numeric($_GET['page_id'])) ? absint($_GET['page_id']) : false))
        $current_page_ID = $page_id;
    else if (!is_admin() && $wpdb) {
        $actual_link = rtrim($_SERVER['REQUEST_URI'], '/');
        $parts       = explode('/', $actual_link);
        if (!empty($parts)) {
            $slug = end($parts);
            if (!empty($slug)) {
                if (
                    $post_id = $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT ID FROM {$wpdb->posts} 
                        WHERE 
                            `post_status` = %s
                        AND
                            `post_name` = %s
                        AND
                            TRIM(`post_name`) <> ''
                        LIMIT 1",
                            'publish',
                            sanitize_title($slug)
                        )
                    )
                ) {
                    $current_page_ID = absint($post_id);
                }
            }
        }
    } else if (!is_admin() && 'page' == get_option('show_on_front') && !empty(get_option('page_for_posts'))) {
        $current_page_ID = get_option('page_for_posts');
    }

    return $current_page_ID;
}