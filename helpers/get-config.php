<?php
function get_config($key, $default = null)
{
    $config = include __DIR__ . '/../config.php';
    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (is_array($value) && array_key_exists($k, $value)) {
            $value = $value[$k];
        } else {
            return $default;
        }
    }

    return $value;
}