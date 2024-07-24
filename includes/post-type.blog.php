<?php
// create blog post type with blog_category taxonomy
function create_blog_post_type()
{
    register_post_type(
        'blog',
        array(
            'labels' => array(
                'name' => __('Blog'),
                'singular_name' => __('Blog'),
                'add_new' => __('Add New'),
                'add_new_item' => __('Add New Blog'),
                'edit' => __('Edit'),
                'edit_item' => __('Edit Blog'),
                'new_item' => __('New Blog'),
                'view' => __('View Blog'),
                'view_item' => __('View Blog'),
                'search_items' => __('Search Blog'),
                'not_found' => __('No Blog found'),
                'not_found_in_trash' => __('No Blog found in Trash'),
                'parent' => __('Parent Blog')
            ),
            'public' => true,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'author',
                'thumbnail',
            ),
            'taxonomies' => array(''),
            'menu_icon' => 'dashicons-format-aside',
            'has_archive' => true,
            'rewrite' => array('slug' => 'blog'),
            'show_in_rest' => true,
            'hierarchical' => false,
        )
    );
}
add_action('init', 'create_blog_post_type');