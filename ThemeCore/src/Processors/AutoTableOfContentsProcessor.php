<?php
namespace ThemeCore\Processors;

use DOMDocument;
use DOMNode;
use DOMXPath;

/**
 * Core content processor responsible for adding IDs to headings and building TOC data.
 */
class AutoTableOfContentsProcessor
{
    private string $wrapperClass;
    private array  $wrappingSubstringPairs;
    private array  $targetHeadingLevels;
    private array  $tocData                = [];
    private array  $usedIds                = []; // Track used IDs

    /**
     * Initialize processor with configuration.
     */
    public function __construct(array $config = [])
    {
        $this->wrapperClass           = $config['wrapperClass'] ?? 'table-of-contents-wrapper';
        $this->wrappingSubstringPairs = $config['wrappingSubstringPairs'] ?? [];
        $this->targetHeadingLevels    = $config['targetHeadingLevels'] ?? [
            1,
            2,
            3
        ];
    }

    /**
     * Process content to add IDs to headings and build TOC data.
     */
    public function processContent(string $content, bool $processAllHeadings = false): string
    {
        // Reset table of contents data and used IDs
        $this->tocData = [];
        $this->usedIds = [];

        if (trim($content) === '') {
            return $content;
        }

        // Rest of the method remains the same
        $headingTags = array_map(fn($level) => "h{$level}", $this->targetHeadingLevels);

        // Build appropriate XPath queries
        if ($processAllHeadings) {
            [
                $query,
                $headingsQuery,
            ] = $this->buildAllHeadingsQueries($headingTags);
        } else {
            [
                $query,
                $headingsQuery,
            ] = $this->buildWrapperQueries($headingTags);
        }

        // Load content into DOMDocument
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // Process headings and add IDs
        $this->addIdsToHeadings($xpath, $query);

        // Populate table of contents data
        $collectedIDs = $this->populateTableOfContents($xpath, $headingsQuery);

        // For wrapper-based processing, handle headings between comment markers
        if (!$processAllHeadings) {
            $this->processHeadingsBetweenCommentMarkers($xpath, $collectedIDs);
        }

        return $dom->saveHTML();
    }

    /**
     * Generate a unique ID from text in kebab case.
     */
    private function generateUniqueId(string $text): string
    {
        // Convert to kebab case - lowercase, replace non-alphanumeric with hyphens
        $id = strtolower(trim($text));
        $id = preg_replace('/[^a-z0-9]+/', '-', $id);
        $id = trim($id, '-');

        if (empty($id)) {
            $id = 'heading';
        }

        // Ensure uniqueness
        $baseId  = $id;
        $counter = 1;

        while (in_array($id, $this->usedIds, true)) {
            $id = $baseId . '-' . $counter;
            $counter++;
        }

        $this->usedIds[] = $id;
        return $id;
    }

    /**
     * Add IDs to headings that don't have them.
     */
    private function addIdsToHeadings(DOMXPath $xpath, string $query): void
    {
        $nodes = $xpath->query($query);
        if ($nodes !== false) {
            foreach ($nodes as $node) {
                if (!$node->hasAttribute('id')) {
                    $node->setAttribute('id', $this->generateUniqueId($node->textContent));
                } else {
                    // Track existing IDs to avoid duplicates
                    $this->usedIds[] = $node->getAttribute('id');
                }
            }
        }
    }

    /**
     * Process a single heading node.
     */
    private function processHeadingNode(DOMNode $node, array &$collectedIDs): void
    {
        if (!$node->hasAttribute('id')) {
            $node->setAttribute('id', $this->generateUniqueId($node->textContent));
        }

        $id = $node->getAttribute('id');
        if (!in_array($id, $collectedIDs, true)) {
            $this->tocData[] = [
                'title' => trim($node->textContent),
                'id'    => $id,
            ];
            $collectedIDs[]  = $id;
        }
    }

    /**
     * Get the table of contents data.
     */
    public function getTableOfContentsData(): array
    {
        return $this->tocData;
    }

    /**
     * Build queries for processing all headings.
     */
    private function buildAllHeadingsQueries(array $headingTags): array
    {
        $queryParts       = [];
        $queryPartsWithId = [];
        foreach ($headingTags as $tag) {
            $queryParts[]       = "//{$tag}";
            $queryPartsWithId[] = "//{$tag}[@id]";
        }
        return [
            implode(' | ', $queryParts),
            implode(' | ', $queryPartsWithId),
        ];
    }

    /**
     * Build queries for wrapper-based processing.
     */
    private function buildWrapperQueries(array $headingTags): array
    {
        $queryParts       = [];
        $queryPartsWithId = [];
        foreach ($headingTags as $tag) {
            $queryParts[]       = "//*[contains(concat(' ', normalize-space(@class), ' '), ' {$this->wrapperClass} ')]//{$tag}";
            $queryPartsWithId[] = "//*[contains(concat(' ', normalize-space(@class), ' '), ' {$this->wrapperClass} ')]//{$tag}[@id]";
        }
        return [
            implode(' | ', $queryParts),
            implode(' | ', $queryPartsWithId),
        ];
    }

    /**
     * Populate table of contents data from headings.
     */
    private function populateTableOfContents(DOMXPath $xpath, string $headingsQuery): array
    {
        $headingNodes = $xpath->query($headingsQuery);
        $collectedIDs = [];

        if ($headingNodes !== false) {
            foreach ($headingNodes as $heading) {
                $this->tocData[] = [
                    'title' => trim($heading->textContent),
                    'id'    => $heading->getAttribute('id'),
                ];
                $collectedIDs[]  = $heading->getAttribute('id');
            }
        }

        return $collectedIDs;
    }

    /**
     * Process headings found between comment markers.
     */
    private function processHeadingsBetweenCommentMarkers(DOMXPath $xpath, array &$collectedIDs): void
    {
        foreach ($this->wrappingSubstringPairs as $pair) {
            $openMarker  = $pair[0];
            $closeMarker = $pair[1];

            $openMarkerContent  = trim(str_replace([
                '<!--',
                '-->',
            ], '', $openMarker));
            $closeMarkerContent = trim(str_replace([
                '<!--',
                '-->',
            ], '', $closeMarker));

            $openComments = $xpath->query("//comment()[starts-with(normalize-space(.), '{$openMarkerContent}')]");
            if ($openComments === false)
                continue;

            foreach ($openComments as $openComment) {
                $node = $openComment->nextSibling;
                while ($node !== null) {
                    if ($node->nodeType === XML_COMMENT_NODE && trim($node->data) === $closeMarkerContent) {
                        break;
                    }

                    if ($node->nodeType === XML_ELEMENT_NODE) {
                        $this->processElementForHeadings($node, $collectedIDs);
                    }

                    $node = $node->nextSibling;
                }
            }
        }
    }

    /**
     * Process an element for headings.
     */
    private function processElementForHeadings(DOMNode $node, array &$collectedIDs): void
    {
        foreach ($this->targetHeadingLevels as $level) {
            $tag = "h{$level}";

            if ($node->nodeName === $tag) {
                $this->processHeadingNode($node, $collectedIDs);
            }

            $descendants = $node->getElementsByTagName($tag);
            foreach ($descendants as $descendant) {
                $this->processHeadingNode($descendant, $collectedIDs);
            }
        }
    }

}
