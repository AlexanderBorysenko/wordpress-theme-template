<?php
function isPathActive($targetPath)
{
    // remove first and last slash
    $targetPath = trim($targetPath, '/');
    $targetPath = rawurlencode($targetPath);

    // get current path
    $currentPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    if ($targetPath === $currentPath) {
        return true;
    }
    return false;
}