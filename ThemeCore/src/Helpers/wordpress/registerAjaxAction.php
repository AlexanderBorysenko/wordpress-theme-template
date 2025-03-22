<?php
function registerAjaxAction($action, $callback)
{
    add_action("wp_ajax_$action", $callback);
    add_action("wp_ajax_nopriv_$action", $callback);
}