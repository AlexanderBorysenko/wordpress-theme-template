<?php
global $loaded_components;
function component($component, $props = [])
{
    if (!is_array($props)) {
        return;
    }
    extract($props);


    if (!isset($loaded_components[$component])) {
        array_push($loaded_components, $component);
    }

    include get_template_directory() . "/source/components/$component.php";
}

function filter_html_attributes($props)
{
    $html_attribute_keys = [
        'class',
        'id',
        'src',
        'href',
        'alt',
        'width',
        'height',
        'sizes',
        'srcset',
        'loading',
        'role',
        'tabindex',
        'style',
        'draggable',
        'dropzone',
        'hidden',
        'spellcheck'
    ];

    $html_attributes = ['class' => []];

    $processClassAttribute = function ($value) {
        if (!is_array($value)) {
            $value = explode(' ', $value);
        }
        $compiled_classes = [];
        foreach ($value as $class_key => $class_val) {
            if (is_string($class_val) && !empty($class_val)) {
                $compiled_classes[] = $class_val;
            } elseif (is_bool($class_val) && $class_val) {
                $compiled_classes[] = $class_key;
            }
        }
        return $compiled_classes;
    };

    $processAttribute = function ($key, $value) use (&$html_attributes, $html_attribute_keys, $processClassAttribute) {
        if ($key === 'class') {
            $html_attributes['class'] = array_merge(
                $html_attributes['class'],
                $processClassAttribute($value)
            );
        } elseif (preg_match('/^(aria-|data-|role)/', $key) || in_array($key, $html_attribute_keys)) {
            $html_attributes[$key] = $value;
        }
    };

    foreach ($props as $prop) {
        if (is_array($prop)) {
            foreach ($prop as $key => $value) {
                $processAttribute($key, $value);
            }
        }
    }

    return $html_attributes;
}

function assemble_html_attributes(...$props)
{
    $html_attributes = filter_html_attributes($props);

    if (isset($props['_exclude'])) {
        foreach ($props['_exclude'] as $exclude_key) {
            unset($html_attributes[$exclude_key]);
        }
    }

    foreach ($html_attributes as $key => &$value) {
        if (is_array($value)) {
            $value = implode(' ', array_filter($value));
        } elseif (is_bool($value)) {
            $value = $value ? $key : '';
        }
    }

    return array_to_html_attributes(array_filter($html_attributes, fn($value) => $value !== ''));
}
