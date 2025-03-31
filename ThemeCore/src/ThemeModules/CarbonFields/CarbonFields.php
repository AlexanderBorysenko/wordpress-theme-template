<?php
namespace ThemeCore\ThemeModules\CarbonFields;

use Carbon_Fields\Carbon_Fields;
use ThemeCore\ThemeModules\ThemeModule;

class CarbonFields extends ThemeModule
{
    private static array $config;

    protected function __construct($config = [])
    {
        includePhpFiles(path_join(__DIR__, 'helpers'));

        self::$config = $config;
    }

    public static function getConfig(): array
    {
        return self::$config;
    }

    public function init()
    {
        add_action('after_setup_theme', function () {
            Carbon_Fields::boot();
        });

        add_action('carbon_fields_register_fields', function () {
            foreach (self::$config['sources'] as $source) {
                requireAll($source);
            }
        });
    }

}
