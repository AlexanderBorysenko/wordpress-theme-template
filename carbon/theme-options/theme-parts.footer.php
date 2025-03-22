<?php
use ThemeCore\ThemeModules\CarbonFields\Managers\ThemeOptionsManager;
use Carbon_Fields\Field;

ThemeOptionsManager::createChildContainer('Theme Parts', 'Footer')
    ->add_tab('General', [
        Field::make('image', 'crb_footer_logo', 'Footer Logo'),
    ])
    ->add_tab('Menu', [

    ]);