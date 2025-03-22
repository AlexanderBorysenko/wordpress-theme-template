<?php
function isPathActive($path)
{
    // remove first and last slash
    $stripped_path = trim($path, '/');
    // get current path
    $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    if ($stripped_path === $current_path) {
        return true;
    }
    return false;
}