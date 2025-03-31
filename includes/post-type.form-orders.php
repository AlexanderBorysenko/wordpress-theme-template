<?php
add_action('init', function () {
    register_post_type('form-orders', [
        'labels'              => [
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
        'menu_icon'           => 'dashicons-cart',
        'supports'            => [
            'title',
            'editor'
        ],

        'public'              => false,  // отключает публичный доступ полностью
        'show_ui'             => true,   // отображается в админке
        'exclude_from_search' => true,   // исключён из поиска
        'publicly_queryable'  => false,  // нет публичных страниц и архива
        'show_in_nav_menus'   => false,  // скрывает из меню навигации
        'has_archive'         => false,  // нет архива
        'show_in_rest'        => false,  // отключает REST API
    ]);
});