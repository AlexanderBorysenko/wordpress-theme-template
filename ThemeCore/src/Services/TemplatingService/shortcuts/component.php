<?php

use ThemeCore\Services\TemplatingService\ComponentRenderService;

function component(string $component, array $htmlAttributes = [], array $props = [])
{

    $processor = new ComponentRenderService($component, $htmlAttributes, $props);
    $processor->render();
}