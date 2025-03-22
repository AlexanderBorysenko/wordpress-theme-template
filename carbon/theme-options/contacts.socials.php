<?php
use Carbon_Fields\Field;
use ThemeCore\ThemeModules\CarbonFields\Managers\ThemeOptionsManager;

ThemeOptionsManager::createChildContainer('Contacts', 'Social Media Links')
    ->add_fields([
        Field::make('text', 'crb_facebook_link', 'Facebook Link')->set_default_value(''),
        Field::make('text', 'crb_instagram_link', 'Instagram Link')->set_width(50)->set_default_value(''),
        Field::make('text', 'crb_instagram_name', 'Instagram Name')->set_width(50)->set_default_value('@'),
        Field::make('text', 'crb_youtube_link', 'YouTube Link')->set_default_value(''),
        Field::make('text', 'crb_threads_link', 'Threads Link')->set_default_value(''),
        Field::make('text', 'crb_n_link', 'N Link')->set_default_value(''),
    ]);