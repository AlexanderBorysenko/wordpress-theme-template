<?php
/**
 * Get a post by the ID or object or array. or whenewer that contains the ID.
 * @param $post_data int|WP_Post|array|string|object
 */

function ultimate_get_post($post_data)
{
    if (is_a($post_data, 'WP_Post')) {
        return $post_data;
    }

    if (is_numeric($post_data)) {
        $post_id = $post_data;
        $post = get_post($post_id);
    } elseif (is_array($post_data) && isset($post_data['id'])) {
        $post_id = $post_data['id'];
        $post = get_post($post_id);
    } elseif (is_object($post_data) && isset($post_data->ID)) {
        $post_id = $post_data->ID;
        $post = get_post($post_id);
    } elseif (is_string($post_data) && filter_var($post_data, FILTER_VALIDATE_URL)) {
        $post_id = url_to_postid($post_data);
        $post = get_post($post_id);
    } elseif (is_string($post_data) && +$post_data) {
        $post_id = $post_data;
        $post = get_post($post_id);
    } else {
        $post = null;
    }

    return $post;
}
