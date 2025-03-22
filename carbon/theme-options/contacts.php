<?php

use Carbon_Fields\Field;
use ThemeCore\ThemeModules\CarbonFields\Managers\ThemeOptionsManager;

ThemeOptionsManager::createRootContainer('Contacts', false)
    ->set_icon('dashicons-phone')
    ->add_fields([
        Field::make('text', 'crb_email', 'Email')->set_default_value('mail@example.com'),
        Field::make('text', 'crb_phone', 'Phone')->set_default_value('(000) 000-0000'),
    ]);