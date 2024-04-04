<?php

/**
 * wp_theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package wp_theme
 */

require_once (get_template_directory() . '/vendor/autoload.php');
require_once (get_template_directory() . '/core-helpers.php');
require_once (get_template_directory() . '/source/components-core.php');

require_all('includes');
require_once (get_template_directory() . '/carbon/init.php');
require_once (get_template_directory() . '/gutenberg-blocks/register-blocks.php');

// DISABLE CF 7 FORMATING
add_filter('wpcf7_autop_or_not', '__return_false');
// define('WPCF7_AUTOP', false);
