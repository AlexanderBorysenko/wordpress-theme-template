<?php
$additional_attributes = $attributes ?? '';
$attributes = [];
if (isset($src) && !empty($src)) {
    if (isset($srcset) && !empty($srcset)) {
        $attributes['srcset'] = $srcset;
        $attributes['sizes'] = $sizes;
    }

    $attributes['width'] = $width ?? '100';

    $attributes['height'] = $height ?? '100';

    $attributes['alt'] = $alt ?? 'image';

    $attributes['loading'] = $loading ?? 'lazy';

    $attributes['class'] = $class ?? '';

    $attributes = array_to_html_attributes($attributes);

    $attributes .= " $additional_attributes";
    ?>
    <img src="<?= $src ?>" <?= $attributes ?>>
    <?php
}