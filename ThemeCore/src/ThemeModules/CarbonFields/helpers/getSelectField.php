<?php
use Carbon_Fields\Field;

function getSelectField($name, $caption, $available_options, $default_value, $option_key)
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