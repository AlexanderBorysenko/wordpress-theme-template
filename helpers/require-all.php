<?php
/**
 * Includes all files from folders passed in $args
 *
 * @param mixed $args List of folders to recursively include
 */
function require_all($args)
{
    $folders = func_get_args();

    foreach ($folders as $folder) {
        if (!!strpos($folder, ".")) {
            require_once (get_template_directory() . "/$folder");
            continue;
        }

        $files = glob(get_template_directory() . "/$folder/*.php");

        foreach ($files as $file) {
            require_once ($file);
        }
    }
}
