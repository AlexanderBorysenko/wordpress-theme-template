<?php
function strip_html($string)
{
    // replace br tags with " " (space)
    $string = str_replace('<br>', ' ', $string);

    return preg_replace('/<[^>]*>/', '', $string);
}