<?php

use Carbon_Fields\Field;
use ThemeCore\ThemeModules\CarbonFields\Managers\ThemeOptionsManager;

ThemeOptionsManager::createChildContainer('Theme Parts', 'Header')
    ->add_fields([
        Field::make('image', 'crb_logo', 'Logo'),
        Field::make('complex', 'crb_header_menu', 'Header Menu 🌐')
            ->add_fields([
                Field::make('text', 'title', 'Title'),
                Field::make('text', 'link', 'Link'),
            ])->set_layout('tabbed-vertical')->set_header_template('<%- title %>'),
    ]);