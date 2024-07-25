<?php

/**
 * wp_theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package wp_theme
 */

require_once(get_template_directory() . '/helpers/require-all.php');
require_all('helpers');


require_once(get_template_directory() . '/vendor/autoload.php');
require_once(get_template_directory() . '/source/components-core.php');

require_all('includes');
require_once(get_template_directory() . '/carbon/init.php');

if (is_admin()) {
    DevTools\DevTools::getInstance();
}