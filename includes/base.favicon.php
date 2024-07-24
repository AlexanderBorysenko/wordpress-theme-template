<?php
function add_dynamic_favicons()
{
    $favicon_dir = get_template_directory() . '/images/favicon/';
    $favicon_url = get_template_directory_uri() . '/images/favicon/';

    // Get all .png files in the favicon directory
    $favicons = glob("{$favicon_dir}*.png");
    if ($favicons) {
        foreach ($favicons as $favicon) {
            // Extract the filename
            $favicon_filename = basename($favicon);
            // Extract the size from the filename
            if (preg_match('/icon-(\d+)\.png/', $favicon_filename, $matches)) {
                $size = $matches[1];
                $size = "{$size}x{$size}";
                echo '<link rel="icon" type="image/png" sizes="' . esc_attr($size) . '" href="' . esc_url($favicon_url . $favicon_filename) . '">' . "\n";
            }
        }
    }
}

// Hook into wp_head to add favicons to the <head> section
add_action('wp_head', 'add_dynamic_favicons');
