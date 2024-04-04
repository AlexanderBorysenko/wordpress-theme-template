<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

$basic_options_container = Container::make('theme_options', 'Menus')
    ->set_icon('dashicons-menu')
    ->add_fields([
    ]);

Container::make('theme_options', 'Footer')
    ->set_page_parent($basic_options_container)
    ->add_fields([
        Field::make('complex', 'crb_footer_menu', 'Footer Menu')
            ->add_fields('column', 'Column', [
                Field::make('text', 'title', 'Title'),
                Field::make('complex', 'items', 'Items')
                    ->add_fields([
                        Field::make('text', 'title', 'Title'),
                        Field::make('text', 'href', 'Href'),
                    ])->set_layout('tabbed-vertical')->set_header_template('<%- title %>'),
            ])->set_layout('tabbed-horizontal')->set_header_template('<%- title %>'),
    ]);
