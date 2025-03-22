<?php
namespace ThemeCore\ThemeModules\PageAutoTableOfContetns;

use ThemeCore\Processors\AutoTableOfContentsProcessor;
use ThemeCore\ThemeModules\ThemeModule;

class PageAutoTableOfContetns extends ThemeModule
{
    /**
     * @var array Table of contents data
     */
    private $tableOfContentsData = [];

    private array $config = [];

    /**
     * Private constructor to prevent direct instantiation
     */
    protected function __construct(
        array $config,
    ) {
        $this->config = $config;

        includePhpFiles(path_join(__DIR__, 'shortcuts'));
    }

    /**
     * Apply the table of contents processing to content.
     */
    public function init()
    {
        // WordPress hook integration
        add_filter('the_content', function (string $content) {
            $handler = new AutoTableOfContentsProcessor([
                'wrappingSubstringPairs' => $this->config['wrappingSubstringPairs'] ?? [],
            ]);

            $processedContent = $handler->processContent(
                $content,
                in_array(
                    get_post_type(),
                    $this->config['postTypes'] ?? [],
                    true
                )
            );

            // Store data in the singleton instead of a global variable
            self::getInstance()->tableOfContentsData = $handler->getTableOfContentsData();

            return $processedContent;
        }, 5);
    }

    /**
     * Get table of contents data
     *
     * @return array
     */
    public function getTableOfContentsData(): array
    {
        return $this->tableOfContentsData;
    }

}