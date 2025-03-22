<?php

use Carbon_Fields\Field;
use ThemeCore\ThemeModules\CarbonFields\Managers\ThemeOptionsManager;

ThemeOptionsManager::createChildContainer('Theme Parts', 'Contact Forms')
    ->add_fields([
        Field::make('text', addLangSuffix('main_contact_form_title'), 'Contact Modal Title 🌐'),
        Field::make('textarea', addLangSuffix('main_contact_form_text'), 'Contact Modal Text 🌐'),
    ]);