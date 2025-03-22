<?php

use ThemeCore\Services\TemplatingService\HtmlAttributesService;

/**
 * Собирает HTML-атрибуты из нескольких массивов.
 *
 * @param array ...$attributeArrays Массивы атрибутов
 * @return string Строка HTML-атрибутов
 */
/**
 * Shorthand for HtmlAttributesProcessor::assemble()
 */
function assembleHtmlAttributes(...$attributeArrays): string
{
    return HtmlAttributesService::assemble(...$attributeArrays);
}