<?php
function create_blog_post_type()
{
    register_post_type(
        'blog',
        [
            'labels' => [
                'name' => 'Blog',
                'singular_name' => 'Blog',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Blog',
                'edit' => 'Edit',
                'edit_item' => 'Edit Blog',
                'new_item' => 'New Blog',
                'view' => 'View Blog',
                'view_item' => 'View Blog',
                'search_items' => 'Search Blog',
                'not_found' => 'No Blog found',
                'not_found_in_trash' => 'No Blog found in Trash',
                'parent' => 'Parent Blog'
            ],
            'public' => true,
            'supports' => [
                'title',
                'editor',
                'excerpt',
                'author',
                'thumbnail',
            ],
            'taxonomies' => [''],
            'menu_icon' => 'dashicons-format-aside',
            'has_archive' => true,
            'rewrite' => ['slug' => 'blog'],
            'show_in_rest' => true,
            'hierarchical' => false,
        ]
    );
}
add_action('init', 'create_blog_post_type');