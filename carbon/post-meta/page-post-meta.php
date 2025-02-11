<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make('post_meta', 'Page Data')
    ->where('post_type', '=', 'page')
    ->add_fields([
        // Field::make('text', 'crb_location', 'Location'),
    ])->set_context('side');
