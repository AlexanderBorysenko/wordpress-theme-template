<?php
function removePostTypeArchiveSlug($postType)
{
    // Remove post type slug from permalinks
    add_filter('post_type_link', function ($permalink, $post) use ($postType) {
        if ($post->post_type === $postType) {
            return str_replace('/' . $postType . '/', '/', $permalink);
        }
        return $permalink;
    }, 10, 2);

    // Add custom rewrite rules to handle the modified permalinks
    add_filter('rewrite_rules_array', function ($rules) use ($postType) {
        $newRules               = [];
        $newRules['([^/]+)/?$'] = 'index.php?post_type=' . $postType . '&name=$matches[1]';
        return $newRules + $rules;
    });

    // Handle post type identification in URL requests
    add_filter('request', function ($query) use ($postType) {
        if (isset($query['name']) && !isset($query['post_type'])) {
            $post = get_page_by_path($query['name'], OBJECT, $postType);
            if ($post) {
                $query['post_type'] = $postType;
                $query['p']         = $post->ID;
                unset($query['name']);
            }
        }
        return $query;
    });

    // Modify post type registration to disable archive
    add_filter('register_post_type_args', function ($args, $post_type) use ($postType) {
        if ($post_type === $postType) {
            $args['has_archive'] = false;
            if (isset($args['rewrite']) && is_array($args['rewrite'])) {
                $args['rewrite']['slug']       = '';
                $args['rewrite']['with_front'] = false;
            } else {
                $args['rewrite'] = [
                    'slug'       => '',
                    'with_front' => false,
                ];
            }
        }
        return $args;
    }, 10, 2);
}