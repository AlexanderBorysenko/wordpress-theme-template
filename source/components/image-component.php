<?php
/**
 * Image Component
 * @param string|int|object $reference
 * @param string $size (optional)
 */

if (!empty($reference)) {
    $image = get_image($reference, !empty($size) ? $size : 'full');
    if (!$image)
        return;
    ?>
    <img <?= assemble_html_attributes($image, $props) ?>>
    <?php
}