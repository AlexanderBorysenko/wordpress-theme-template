<?php
/**
 * Includes all files from folders passed in $args
 *
 * @param mixed $args List of folders to recursively include
 */
function require_all()
{
    $args = func_get_args();
    $pattern = '*.php';

    // If the last argument looks like a custom pattern (contains a '*'), use it
    if (!empty($args) && is_string(end($args)) && strpos(end($args), '*') !== false) {
        $pattern = array_pop($args);
    }

    foreach ($args as $folder) {
        // if the folder argument contains a dot, assume it's a file path
        if (strpos($folder, ".") !== false) {
            require_once(get_template_directory() . "/$folder");
            continue;
        }

        // Get files matching the custom pattern (or default '*.php' pattern)
        $files = glob(get_template_directory() . "/$folder/$pattern");

        foreach ($files as $file) {
            require_once($file);
        }
    }
}
