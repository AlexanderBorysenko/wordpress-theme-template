<?php
function component($component, $props = [])
{
    extract($props);
    include get_template_directory() . "/source/components/$component.php";
}