<?php
function register_form_orders_post_type()
{
    $labels = array(
        'name' => _x('Form Orders', 'Post Type General Name', 'text_domain'),
        'singular_name' => _x('Form Order', 'Post Type Singular Name', 'text_domain'),
        'menu_name' => _x('Form Orders', 'Admin Menu text', 'text_domain'),
        'name_admin_bar' => _x('Form Order', 'Add New on Toolbar', 'text_domain'),
        'archives' => __('Form Order Archives', 'text_domain'),
        'attributes' => __('Form Order Attributes', 'text_domain'),
        'parent_item_colon' => __('Parent Form Order:', 'text_domain'),
        'all_items' => __('All Form Orders', 'text_domain'),
        'add_new_item' => __('Add New Form Order', 'text_domain'),
        'add_new' => __('Add New', 'text_domain'),
        'new_item' => __('New Form Order', 'text_domain'),
        'edit_item' => __('Edit Form Order', 'text_domain'),
        'update_item' => __('Update Form Order', 'text_domain'),
        'view_item' => __('View Form Order', 'text_domain'),
        'view_items' => __('View Form Orders', 'text_domain'),
        'search_items' => __('Search Form Order', 'text_domain'),
        'not_found' => __('Not found', 'text_domain'),
        'not_found_in_trash' => __('Not found in Trash', 'text_domain'),
        'featured_image' => __('Featured Image', 'text_domain'),
        'set_featured_image' => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image' => __('Use as featured image', 'text_domain'),
        'insert_into_item' => __('Insert into Form Order', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this Form Order', 'text_domain'),
        'items_list' => __('Form Orders list', 'text_domain'),
        'items_list_navigation' => __('Form Orders list navigation', 'text_domain'),
        'filter_items_list' => __('Filter Form Orders list', 'text_domain'),
    );
    $args = [
        'label' => __('Form Order', 'text_domain'),
        'labels' => $labels,
        'menu_icon' => 'dashicons-cart',
        'supports' => array('title', 'editor'),
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