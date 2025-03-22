<?php

use Carbon_Fields\Field;

function getTagSelectField($name = 'tag', $caption = 'Tag Selection')
{
    $options = [
        'initial' => 'Initial',
        'h1'      => 'H1',
        'h2'      => 'H2',
        'h3'      => 'H3',
        'h4'      => 'H4',
        'h5'      => 'H5',
        'p'       => 'P',
        'div'     => 'Div',
    ];

    return Field::make('select', $name, $caption)
        ->set_options($options)
        ->set_default_value('select');
}