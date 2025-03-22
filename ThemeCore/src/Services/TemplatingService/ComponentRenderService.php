<?php
namespace ThemeCore\Services\TemplatingService;

use Closure;

/**
 * Handles the processing and rendering of components.
 */
class ComponentRenderService
{
    /**
     * @var string Component name
     */
    private string $component;

    /**
     * @var array HTML attributes
     */
    private array $htmlAttributes;

    /**
     * @var array Component properties
     */
    private array $props;

    private string $componentDomain = 'default';

    private static array $domains = [
        'default' => 'source/components',
    ];
    public static function defineDomain(string $domain, string $base): void
    {
        self::$domains[$domain] = $base;
    }

    /**
     * Constructor
     *
     * @param string $component Name of the component
     * @param array $htmlAttributes HTML attributes for the component
     * @param array $props Properties for the component
     */
    public function __construct($component, array $htmlAttributes = [], array $props = [])
    {
        if (strpos($component, '.')) {
            [
                $domain,
                $component
            ] = explode('.', $component);
            $this->componentDomain = $domain;
        }

        $this->component      = $component;
        $this->htmlAttributes = $htmlAttributes;
        $this->props          = $props;
    }

    /**
     * Process properties that contain closures
     *
     * @return array Processed properties
     */
    private function processProps(): array
    {
        $processedProps = $this->props;

        foreach ($processedProps as $key => $value) {
            if ($value instanceof Closure) {
                ob_start();
                call_user_func($value);
                $processedProps[$key] = ob_get_clean();
            }
        }

        return $processedProps;
    }

    /**
     * Render the component
     */
    public function render(): void
    {
        $props          = $this->processProps();
        $htmlAttributes = $this->htmlAttributes;
        $component      = $this->component;

        $htmlAttributes['data-component'] = $component;

        extract($props);

        $htmlAttributesString = function (...$attributes) use ($htmlAttributes) {
            return assembleHtmlAttributes($htmlAttributes, ...$attributes);
        };

        $componentBase = path_join(get_template_directory(), self::$domains[$this->componentDomain]);
        include path_join($componentBase, "$component.php");
    }

}
