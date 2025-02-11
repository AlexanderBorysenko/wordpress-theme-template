<?php
function theme_blocks_categories($categories, $post)
{
    $custom_categories = [
        [
            'slug' => 'theme-layout',
            'title' => 'Layout',
            'icon' => 'welcome-widgets-menus',
        ],
        [
            'slug' => 'theme-blocks',
            'title' => 'Theme Blocks',
            'icon' => 'welcome-widgets-menus',
        ],
    ];
    array_unshift($categories, ...$custom_categories);
    return $categories;
}
add_filter('block_categories_all', 'theme_blocks_categories', 10, 2);