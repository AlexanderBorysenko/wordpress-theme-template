<?php
global $tableOfContentsData;
$tableOfContentsData = [];

/**
 * Add unique IDs to specified heading levels and collect them for a table of contents.
 *
 * Depending on the post type, headings are processed either in the entire content (auto‐process)
 * or only within a designated element (wrapper class). Additionally, for non–auto–process posts,
 * headings enclosed between any of the defined comment–marker pairs are also processed.
 *
 * Configurable parameters:
 *   - $autoProcessPostTypes: Post types for which all headings are processed.
 *   - $wrapperClass: CSS class name used to mark a container for headings.
 *   - $wrappingSubstringPairs: Array of pairs of comment markers. Headings found between an opening
 *       and a closing marker from any pair are processed.
 *   - $targetHeadingLevels: Array of heading levels (as integers) to target (e.g. [1,2,3] for h1, h2, h3).
 *
 * @param string $content The original HTML content.
 * @return string The modified HTML content.
 */
function add_unique_id_to_headings(string $content): string
{
    if (trim($content) === '') {
        return $content;
    }

    // =========== CONFIGURABLE ===========
    // Post types for which all headings are auto-processed.
    $autoProcessPostTypes = ['blog'];
    // CSS class of the container where headings should be processed.
    $wrapperClass = 'table-of-contents-target';
    // Pairs of comment markers that wrap content areas to process.
    // Each pair is an array: [openingMarker, closingMarker]
    $wrappingSubstringPairs = [
        ['<!-- wp:carbon-fields/page-typography-content-wrapper -->', '<!-- /wp:carbon-fields/page-typography-content-wrapper -->']
        // Можно добавить и другие пары.
    ];
    // Heading levels to target.
    $targetHeadingLevels = [1, 2, 3];
    // =========== CONFIGURABLE ===========

    $postType = get_post_type();

    // Build an array of heading tag names based on target levels.
    $headingTags = array_map(fn($level) => "h{$level}", $targetHeadingLevels);

    // Build XPath queries depending on whether we auto-process all headings.
    if (in_array($postType, $autoProcessPostTypes, true)) {
        // Process all headings of the targeted levels.
        $queryParts = [];
        $queryPartsWithId = [];
        foreach ($headingTags as $tag) {
            $queryParts[] = "//{$tag}";
            $queryPartsWithId[] = "//{$tag}[@id]";
        }
        $query = implode(' | ', $queryParts);
        $headingsQuery = implode(' | ', $queryPartsWithId);
    } else {
        // Process only headings inside the designated wrapper element.
        $queryParts = [];
        $queryPartsWithId = [];
        foreach ($headingTags as $tag) {
            $queryParts[] = "//*[contains(concat(' ', normalize-space(@class), ' '), ' {$wrapperClass} ')]//{$tag}";
            $queryPartsWithId[] = "//*[contains(concat(' ', normalize-space(@class), ' '), ' {$wrapperClass} ')]//{$tag}[@id]";
        }
        $query = implode(' | ', $queryParts);
        $headingsQuery = implode(' | ', $queryPartsWithId);
    }

    // Load content into DOMDocument.
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $html = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    // First: process headings selected by XPath.
    $nodes = $xpath->query($query);
    if ($nodes !== false) {
        foreach ($nodes as $node) {
            if (!$node->hasAttribute('id')) {
                $node->setAttribute('id', 'heading-' . uniqid());
            }
        }
    }

    // Reset and populate the global table of contents data.
    global $tableOfContentsData;
    $tableOfContentsData = [];
    $headingNodes = $xpath->query($headingsQuery);
    if ($headingNodes !== false) {
        foreach ($headingNodes as $heading) {
            $tableOfContentsData[] = [
                'title' => trim($heading->textContent),
                'id' => $heading->getAttribute('id'),
            ];
        }
    }
    // Maintain a list of collected heading IDs to prevent duplicates.
    $collectedIDs = array_map(fn($entry) => $entry['id'], $tableOfContentsData);

    // For non–auto–process posts, additionally process headings found between specified comment markers.
    if (!in_array($postType, $autoProcessPostTypes, true)) {
        foreach ($wrappingSubstringPairs as $pair) {
            [$openMarker, $closeMarker] = $pair;
            // Убираем ограждения комментария для получения "чистого" маркера.
            $openMarkerContent = trim(str_replace(['<!--', '-->'], '', $openMarker));
            $closeMarkerContent = trim(str_replace(['<!--', '-->'], '', $closeMarker));

            // Используем starts-with для поиска комментариев, начинающихся с нужного маркера.
            $openComments = $xpath->query("//comment()[starts-with(normalize-space(.), '{$openMarkerContent}')]");
            if ($openComments !== false) {
                foreach ($openComments as $openComment) {
                    // Перебор последующих соседних узлов до нахождения закрывающего комментария.
                    $node = $openComment->nextSibling;
                    while ($node !== null) {
                        // Если найден комментарий, начинающийся с закрывающего маркера (при необходимости можно также использовать starts-with), то выходим из цикла.
                        if ($node->nodeType === XML_COMMENT_NODE && trim($node->data) === $closeMarkerContent) {
                            break;
                        }
                        // Обрабатываем элементы: если узел является целевым заголовком или содержит таковые.
                        if ($node->nodeType === XML_ELEMENT_NODE) {
                            foreach ($targetHeadingLevels as $level) {
                                $tag = "h{$level}";
                                // Если сам узел является заголовком.
                                if ($node->nodeName === $tag) {
                                    if (!$node->hasAttribute('id')) {
                                        $node->setAttribute('id', 'heading-' . uniqid());
                                    }
                                    $id = $node->getAttribute('id');
                                    if (!in_array($id, $collectedIDs, true)) {
                                        $tableOfContentsData[] = [
                                            'title' => trim($node->textContent),
                                            'id' => $id,
                                        ];
                                        $collectedIDs[] = $id;
                                    }
                                }
                                // Также обрабатываем все потомки, являющиеся заголовками.
                                $descendants = $node->getElementsByTagName($tag);
                                foreach ($descendants as $descendant) {
                                    if (!$descendant->hasAttribute('id')) {
                                        $descendant->setAttribute('id', 'heading-' . uniqid());
                                    }
                                    $id = $descendant->getAttribute('id');
                                    if (!in_array($id, $collectedIDs, true)) {
                                        $tableOfContentsData[] = [
                                            'title' => trim($descendant->textContent),
                                            'id' => $id,
                                        ];
                                        $collectedIDs[] = $id;
                                    }
                                }
                            }
                        }
                        $node = $node->nextSibling;
                    }
                }
            }
        }
    }

    return $dom->saveHTML();
}
add_filter('the_content', 'add_unique_id_to_headings', 5);
