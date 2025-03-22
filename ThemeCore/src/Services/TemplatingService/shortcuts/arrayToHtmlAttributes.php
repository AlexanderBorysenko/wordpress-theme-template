<?php

use ThemeCore\Services\TemplatingService\HtmlAttributesService;

function arrayToHtmlAttributes(array $attributes): string
{
    return HtmlAttributesService::arrayToString($attributes);
}