<?php

/**
 * c_valley functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package c_valley
 */

require_once get_template_directory() . '/helpers/require-all.php';
require_all('helpers');


require_once get_template_directory() . '/vendor/autoload.php';
require_once get_template_directory() . '/source/components-core.php';

require_all('includes');
require_all('source/components', '*.includes.php');
require_once get_template_directory() . '/carbon/init.php';

if (is_admin()) {
    DevTools\DevTools::getInstance();
}