<?php
namespace ThemeCore\ThemeModules\EnableSVG;

use ThemeCore\ThemeModules\ThemeModule;

/**
 * EnableSVG module: Allows SVG uploads and registers SVG mime type in WordPress.
 */
class EnableSVG extends ThemeModule
{
    protected function __construct(array $config = [])
    {
    }

    public function init()
    {
        // Allow SVG upload
        add_filter('upload_mimes', function ($mimes) {
            $mimes['svg']  = 'image/svg+xml';
            $mimes['svgz'] = 'image/svg+xml';
            return $mimes;
        });

        // Fix SVG display in media library
        add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if ('svg' === strtolower($ext)) {
                $data['ext']  = 'svg';
                $data['type'] = 'image/svg+xml';
            }
            return $data;
        }, 10, 4);

        // Optional: Add basic SVG sanitization for uploads (for extra security)
        add_filter('wp_handle_upload_prefilter', function ($file) {
            if (
                isset($file['type']) &&
                $file['type'] === 'image/svg+xml' &&
                isset($file['tmp_name']) &&
                file_exists($file['tmp_name'])
            ) {
                $svg = file_get_contents($file['tmp_name']);
                // Basic check: SVG must start with <svg
                if (strpos($svg, '<svg') === false) {
                    $file['error'] = __('Invalid SVG file.', 'theme-text-domain');
                }
            }
            return $file;
        });
    }

}
