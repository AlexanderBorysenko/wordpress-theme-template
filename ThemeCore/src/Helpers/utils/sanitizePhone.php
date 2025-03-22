<?php
function sanitizePhone($input)
{
    // Use a regular expression to replace all non-numeric characters with an empty string
    $output = preg_replace('/\D/', '', $input);
    return $output;
}