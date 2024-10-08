<?php
function get_vd($var, $die = true)
{
    if (!current_user_can('administrator'))
        return;
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    if ($die) {
        die();
    }
}