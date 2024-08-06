<?php
$additional_attributes = $attributes ?? '';
$attributes = [];
if (!isset($src) || empty($src))
    return;

if (isset($srcset) && !empty($srcset))
    $attributes['srcset'] = $srcset;

if (isset($sizes) && !empty($sizes))
    $attributes['sizes'] = $sizes;

$attributes['width'] = $width ?? '100';

$attributes['height'] = $height ?? '100';

$attributes['alt'] = $alt ?? '';

$attributes['loading'] = $loading ?? 'lazy';

$attributes['class'] = $class ?? '';

$attributes = array_map(function ($key, $value) {
    return "$key=\"$value\"";
}, array_keys($attributes), $attributes);

$attributes = implode(' ', $attributes);
$attributes += " $additional_attributes";
?>
<img src="<?= $src ?>" <?= $attributes ?>>