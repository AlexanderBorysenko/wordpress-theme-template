<?php
function c_valley_theme_support()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_post_type_support('page', 'excerpt');
}
add_action('after_setup_theme', 'c_valley_theme_support');