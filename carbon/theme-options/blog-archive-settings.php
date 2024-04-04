<?php
/**
 * This file contains the configuration for the blog archive page settings in the WordPress theme.
 *
 * Creates a theme options container for the archive page settings.
 * The container includes fields for the hero title and hero content of the blog archive page.
 *
 * @package WP_Theme
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make('theme_options', 'Archive Page Settings')
    ->set_page_parent('edit.php?post_type=blog')
    ->add_fields([
        Field::make('text', 'crb_blog_archive_hero_title', 'Hero Title'),
        Field::make('rich_text', 'crb_blog_archive_hero_content', 'Hero Content'),
    ]);
