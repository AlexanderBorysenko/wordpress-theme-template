<?php

use Carbon_Fields\Field;

function get_select_field($name, $caption, $available_options, $default_value, $option_key)
{
    $options = array_flip(
        array_map(
            function ($option) use ($option_key) {
                return $option[$option_key];
            },
            $available_options
        )
    );
    return Field::make('select', $name, $caption)
        ->set_options($options)
        ->set_default_value($default_value);
}

global $available_margins;
$available_margins = [
    'Initial' => [
        'bottom' => '',
    ],
    '1' => [
        'bottom' => 'mb-1',
    ],
    '1-5' => [
        'bottom' => 'mb-1-5',
    ],
    '2' => [
        'bottom' => 'mb-2',
    ],
    '3' => [
        'bottom' => 'mb-3',
    ],
    '4' => [
        'bottom' => 'mb-4',
    ],
    '5' => [
        'bottom' => 'mb-5',
    ],
    '6-5' => [
        'bottom' => 'mb-6',
    ],
];

function get_margin_bottom_select_field($name = 'margin_bottom', $caption = 'Margin Bottom')
{
    global $available_margins;

    return get_select_field($name, $caption, $available_margins, '', 'bottom');
}

function get_container_select_field($name = 'container', $caption = 'Container')
{
    return Field::make('select', $name, $caption)
        ->set_options([
            'container' => 'Container',
            'container-fluid' => 'Container Fluid',
            'slim-container' => 'Slim Container',
            'extra-slim-container' => 'Extra Slim Container',
        ])
        ->set_default_value('container');
}
