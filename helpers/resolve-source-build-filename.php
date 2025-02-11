<?php
/**
 * Resolves the build file name based on the given pattern (from source/build directory)
 *
 * @param string $pattern The pattern to match the build file name
 * @return string The resolved build file name
 */
function resolve_source_build_filename($pattern)
{
    $directory = get_template_directory() . '/source/build/';
    $files = glob($directory . $pattern);
    return isset($files[0]) ? basename($files[0]) : '';
}

function get_source_build_file_uri($pattern)
{
    return get_template_directory_uri() . '/source/build/' . resolve_source_build_filename($pattern);
}