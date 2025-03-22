<?php
add_action('init', function () {
    register_post_type('form-orders', [
        'labels'             => [
            'name'               => 'Form Orders',
            'singular_name'      => 'Form Order',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Form Order',
            'edit'               => 'Edit',
            'edit_item'          => 'Edit Form Order',
            'new_item'           => 'New Form Order',
            'view'               => 'View Form Order',
            'view_item'          => 'View Form Order',
            'search_items'       => 'Search Form Order',
            'not_found'          => 'No Form Order found',
            'not_found_in_trash' => 'No Form Order found in Trash',
            'parent'             => 'Parent Form Order'
        ],
        'menu_icon'          => 'dashicons-cart',
        'supports'           => [
            'title',
            'editor'
        ],
        'hierarchical'       => false,
        'public'             => true,
        'menu_position'      => 5,
        'has_archive'        => false,
        'publicly_queryable' => false,
        'show_in_rest'       => false,
    ]);
});