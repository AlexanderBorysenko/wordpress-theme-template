<?php

use Carbon_Fields\Field;

global $available_margins;
$available_margins = [
    'Initial' => [
        'bottom' => '',
    ],
    '1' => [
        'bottom' => 'mb-1',
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
    '6' => [
        'bottom' => 'mb-6',
    ],
];

function get_margin_bottom_select_field($name = 'margin_bottom', $caption = 'Margin Bottom')
{
    global $available_margins;

    return get_select_field($name, $caption, $available_margins, '', 'bottom');
}

global $available_paddings;
$available_paddings = [
    'Initial' => [
        'bottom' => '',
        'top' => '',
    ],
    '1' => [
        'bottom' => 'pb-1',
        'top' => 'pt-1',
    ],
    '2' => [
        'bottom' => 'pb-2',
        'top' => 'pt-2',
    ],
    '3' => [
        'bottom' => 'pb-3',
        'top' => 'pt-3',
    ],
    '4' => [
        'bottom' => 'pb-4',
        'top' => 'pt-4',
    ],
    '5' => [
        'bottom' => 'pb-5',
        'top' => 'pt-5',
    ],
];

function get_padding_bottom_select_field($name = 'padding_bottom', $caption = 'Padding Bottom')
{
    global $available_paddings;
    return get_select_field($name, $caption, $available_paddings, '', 'bottom');
}

function get_padding_top_select_field($name = 'padding_top', $caption = 'Padding Top')
{
    global $available_paddings;
    return get_select_field($name, $caption, $available_paddings, '', 'top');
}


// 

function get_background_select_field($name = 'background', $caption = 'Has Background')
{
    return Field::make('select', $name, $caption)
        ->set_options(
            [
                '' => 'None',
                'has-semi-grey-background-bottom-connection' => 'Semi Grey Background Bottom Connection',
                'has-semi-grey-background' => 'Semi Grey Background',
                'has-shifted-white-background' => 'Shifted White Background',
                'has-shifted-white-background-top-connection' => 'Shifted White Background Top Connection',
            ]
        )
        ->set_default_value('');
}