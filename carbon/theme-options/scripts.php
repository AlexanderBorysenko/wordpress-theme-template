<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

$basic_options_container = Container::make('theme_options', 'Scripts')
    ->add_fields([
        Field::make('header_scripts', 'crb_header_script', 'Header Script'),
        Field::make('footer_scripts', 'crb_footer_script', 'Footer Script'),
    ]);

