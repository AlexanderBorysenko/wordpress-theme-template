<?php
namespace ThemeCore\ThemeModules\Polylang;

use ThemeCore\ThemeModules\ThemeModule;

class Polylang extends ThemeModule
{
    private array $strings = [];

    protected function __construct(array $config = [])
    {
        includePhpFiles(path_join(__DIR__, 'helpers'));

        $this->strings = $config['strings'];
    }

    public function init()
    {
        if (!function_exists('pll_register_string'))
            return;

        foreach ($this->strings as $string) {
            pll_register_string($string, $string, 'theme');
        }
    }

    public function getStrings()
    {
        return $this->strings;
    }

}