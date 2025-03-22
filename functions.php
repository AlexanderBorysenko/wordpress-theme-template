<?php
use ThemeCore\Services\TemplatingService\ComponentRenderService;
use ThemeCore\ThemeModules\CarbonFields\CarbonFields;
use ThemeCore\ThemeModules\DocumentScrollbarWidthCssVariable\DocumentScrollbarWidthCssVariable;
use ThemeCore\ThemeModules\HotReload\HotReload;
use ThemeCore\ThemeModules\PageAutoTableOfContetns\PageAutoTableOfContetns;
use ThemeCore\ThemeModules\Polylang\Polylang;
use ThemeCore\ThemeModules\ReCaptcha\ReCaptcha;
use ThemeCore\ThemeModules\ScrollSaver\ScrollSaver;
use ThemeCore\ThemeModules\ThemeAssetsLoader\ThemeAssetsLoader;

/**
 * wp_theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package wp_theme
 */

require_once get_template_directory() . '/ThemeCore/connect.php';

disablePostsPostType();
disableComments();

ComponentRenderService::defineDomain('default', getThemeСonfig('components.base'));

Polylang::initModule(['strings' => getThemeСonfig('translation-strings')]);
CarbonFields::initModule(getThemeСonfig('carbonfields'));
ThemeAssetsLoader::initModule(getThemeСonfig('assets'));
DocumentScrollbarWidthCssVariable::initModule();
HotReload::initModule(getThemeСonfig('hot-reload'));
ScrollSaver::initModule();
ReCaptcha::initModule(getThemeСonfig('recaptcha'));
PageAutoTableOfContetns::initModule(getThemeСonfig('table-of-contents'));

requireAll('includes');
requireAll(getThemeСonfig('components.base'), '*.includes.php');

require_once get_template_directory() . '/gutenberg/init.php';