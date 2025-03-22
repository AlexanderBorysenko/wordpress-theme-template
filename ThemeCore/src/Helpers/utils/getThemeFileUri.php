<?php
/**
 * Returns the URI to the file in the theme directory.
 *
 * @param string $pattern File pattern (for example, "source/components/header.php")
 * @return string URI to the file
 */
function getThemeFileUri($pattern)
{
    $file = getThemeFilePath($pattern);

    return $file ? get_template_directory_uri() . '/' . str_replace(get_template_directory(), '', $file) : '';
}