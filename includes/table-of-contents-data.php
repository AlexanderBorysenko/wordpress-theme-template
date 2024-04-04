<?php
global $tableOfContentsData;
$tableOfContentsData = array();

function add_unique_id_to_headings($content)
{
    if (!$content)
        return $content;

    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    $xpath = new DOMXPath($dom);

    $nodes = $xpath->query('//h2 | //h3');
    foreach ($nodes as $node) {
        if (!$node->hasAttribute('id')) {
            $node->setAttribute('id', 'heading-' . uniqid());
        }
    }

    $headings = $xpath->query('//h2[@id] | //h3[@id]');
    if ($headings->length) {
        global $tableOfContentsData;
        $tableOfContentsData = array();
    }
    foreach ($headings as $heading) {
        global $tableOfContentsData;
        $tableOfContentsData[] = array(
            'title' => strip_tags($heading->textContent),
            'id' => $heading->getAttribute('id')
        );
    }

    $html = $dom->saveHTML();
    return $html;
}
add_filter('the_content', 'add_unique_id_to_headings', 5);

