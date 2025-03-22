<?php
/**
 * Image Component
 * @param string|int|object $reference
 * @param string $size
 * @param bool $lazy
 */

if (!empty($reference)) {
    $image = getImageData($reference, !empty($size) ? $size : 'full');
    if (!$image)
        return;

    $alt = $image['alt'] ?? false;
    if ($htmlAttributes['alt'] ?? false) {
        $alt = $htmlAttributes['alt'];
    }
    ?>
    <img <?= $htmlAttributesString([
        'src'    => $image['src'] ?? '',
        'alt'    => $alt,
        'width'  => $image['width'] ?? '100',
        'height' => $image['height'] ?? '100',
        'srcset' => $image['srcset'] ?? '',
        'sizes'  => function () use ($image) {
            if (empty($image['sizes']))
                return false;
            return $image['sizes'] ?? '';
        },
        'lazy'   => $lazy ?? true,
    ]) ?>>
    <?php
}