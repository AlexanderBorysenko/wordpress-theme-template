<?php
/**
 * Returns the path to the file in the theme directory.
 *
 * @param string $pattern File pattern (for example, "source/components/header.php")
 * @return string Path to the file
 */
function getThemeFilePath($pattern)
{

    $files = glob(path_join(get_template_directory(), $pattern));

    return $files[0] ?? '';
}