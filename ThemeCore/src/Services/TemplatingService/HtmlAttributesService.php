<?php
namespace ThemeCore\Services\TemplatingService;

use Closure;

class HtmlAttributesService
{
    /**
     * Assembles HTML attributes from multiple attribute arrays.
     *
     * @param array ...$attributeArrays Arrays of attributes to combine
     * @return string HTML attributes string
     */
    public static function assemble(...$attributeArrays): string
    {
        $htmlAttributes = [];

        foreach ($attributeArrays as $attributes) {
            foreach ((array) $attributes as $key => $value) {
                if ($key === 'class') {
                    $htmlAttributes['class'] = array_merge(
                        $htmlAttributes['class'] ?? [],
                        is_array($value) ? $value : (empty($value) ? [] : [$value])
                    );
                } else {
                    $htmlAttributes[$key] = $value;
                }
            }
        }

        return self::arrayToString($htmlAttributes);
    }

    /**
     * Преобразует ассоциативный массив атрибутов в строку для вставки в HTML тег.
     *
     * @param array $attributes Ассоциативный массив атрибутов, например: ['id' => 'foo', 'class' => 'bar']
     * @return string Строка HTML-атрибутов, например: id="foo" class="bar"
     */
    public static function arrayToString(array $attributes): string
    {
        $htmlAttributes = [];

        $shouldSkip = fn($value) => $value === null || $value === false || $value === '';

        $prepareValue = function ($key, $value) use (&$prepareValue, $shouldSkip) {
            if ($value instanceof Closure) {
                $value = $value();
            }

            if ($value === true) {
                return esc_attr($key);
            }
            if (is_string($value)) {
                return $value;
            }
            if ($shouldSkip($value)) {
                return false;
            }
            if (is_array($value)) {
                $subValues = [];
                foreach ($value as $subKey => $subVal) {
                    $subValues[] = $prepareValue($subKey, $subVal);
                }
                return implode(' ', array_filter($subValues));
            }
            return esc_attr($value);
        };

        foreach ($attributes as $key => $value) {
            $value = $prepareValue($key, $value);
            if ($value !== false) {
                $htmlAttributes[] = sprintf('%s="%s"', esc_attr($key), esc_attr($value));
            }
        }

        return implode(' ', $htmlAttributes);
    }

}