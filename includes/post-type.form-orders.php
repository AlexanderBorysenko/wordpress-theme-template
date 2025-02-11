<?php

function register_form_orders_post_type()
{
    $labels = [
        'name' => 'Form Orders',
        'singular_name' => 'Form Order',
        'menu_name' => 'Form Orders',
        'name_admin_bar' => 'Form Order',
        'archives' => 'Form Order Archives',
        'attributes' => 'Form Order Attributes',
        'parent_item_colon' => 'Parent Form Order:',
        'all_items' => 'All Form Orders',
        'add_new_item' => 'Add New Form Order',
        'add_new' => 'Add New',
        'new_item' => 'New Form Order',
        'edit_item' => 'Edit Form Order',
        'update_item' => 'Update Form Order',
        'view_item' => 'View Form Order',
        'view_items' => 'View Form Orders',
        'search_items' => 'Search Form Order',
        'not_found' => 'Not found',
        'not_found_in_trash' => 'Not found in Trash',
        'featured_image' => 'Featured Image',
        'set_featured_image' => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image' => 'Use as featured image',
        'insert_into_item' => 'Insert into Form Order',
        'uploaded_to_this_item' => 'Uploaded to this Form Order',
        'items_list' => 'Form Orders list',
        'items_list_navigation' => 'Form Orders list navigation',
        'filter_items_list' => 'Filter Form Orders list',
    ];

    $args = [
        'label' => 'Form Order',
        'labels' => $labels,
        'menu_icon' => 'dashicons-cart',
        'supports' => ['title', 'editor'],
        'hierarchical' => false,
        'public' => true,
        'menu_position' => 5,
        'has_archive' => false,
        'publicly_queryable' => false,
        'show_in_rest' => false,
        'rest_base' => 'form-orders',
    ];

    register_post_type('form-orders', $args);
}

add_action('init', 'register_form_orders_post_type');