<?php

use ThemeCore\Services\ConfigurationService\ConfigurationService;

function getThemeСonfig($key, $default = null)
{
    $instance = ConfigurationService::getInstance();

    if ($key === null) {
        return $instance;
    }

    return $instance->get($key, $default);
}