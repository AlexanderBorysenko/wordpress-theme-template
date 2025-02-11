<?php
function array_to_html_attributes($attributes)
{
    $attributes = array_map(function ($key, $value) {
        $value = esc_attr($value);
        return "$key=\"$value\"";
    }, array_keys($attributes), $attributes);

    return implode(' ', $attributes);
}