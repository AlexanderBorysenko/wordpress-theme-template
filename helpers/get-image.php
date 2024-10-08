<?php
/**
 * Retrieves image data by ID and size
 *
 * @param int $id The attachment ID
 * @param string $size The image size (default: 'full')
 * @return array An array containing the image data or false if the ID is empty
 */
function get_image($id, $size = 'full')
{
    if (!$id)
        return [];

    $image = wp_get_attachment_image_src($id, $size);
    if (!$image)
        return [];
    $image_data = [
        'src' => $image[0],
        'width' => $image[1],
        'height' => $image[2],
        'alt' => get_post_meta($id, '_wp_attachment_image_alt', true),
        'full' => wp_get_attachment_image_src($id, 'full')[0],
        'srcset' => wp_get_attachment_image_srcset($id, $size),
        'sizes' => wp_get_attachment_image_sizes($id, $size)
    ];
    return $image_data;
}
