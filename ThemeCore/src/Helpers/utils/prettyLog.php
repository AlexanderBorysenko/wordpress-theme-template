<?php

if (!function_exists('prettyLog')) {
    function prettyLog()
    {
        echo '<pre>';
        foreach (func_get_args() as $var) {
            var_dump($var);
        }
        echo '</pre>';
    }

}