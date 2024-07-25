<?php
namespace DevTools\Helpers;

class DOMHelper
{
    static function getBodyClass(\DOMDocument $dom): string
    {
        $body = $dom->getElementsByTagName('body');
        if ($body->length > 0) {
            return $body->item(0)->getAttribute('class');
        }
        return '';
    }

    static function getMetaTagContent(\DOMDocument $dom, string $name): string
    {
        $metas = $dom->getElementsByTagName('meta');
        foreach ($metas as $meta) {
            if ($meta->getAttribute('name') === $name) {
                return $meta->getAttribute('content');
            }
        }
        return '';
    }

    static function getElementTextContent(\DOMDocument $dom, string $selector): string
    {
        $tags = $dom->getElementsByTagName($selector);

        return $tags->item(0)->textContent;
    }

    static function getImagesSrc(\DOMDocument $dom): array
    {
        $images = $dom->getElementsByTagName('img');
        $sources = [];
        foreach ($images as $image) {
            $sources[] = $image->getAttribute('src');
        }
        return $sources;
    }

    static function nodeToDOM(\DOMNode $node): \DOMDocument
    {
        $dom = new \DOMDocument();
        $dom->appendChild($dom->importNode($node, true));
        return $dom;
    }

    static function updateImagesSrc(\DOMDocument $dom, $oldSrc, string $newSrc): \DOMDocument
    {
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $image) {
            if ($image->getAttribute('src') !== $oldSrc) {
                continue;
            }
            $image->removeAttribute('srcset');
            $image->removeAttribute('sizes');
            $image->setAttribute('src', $newSrc);
        }

        return $dom;
    }

    static function stringToDOM(string $content): \DOMDocument
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        return $dom;
    }

    static function getNodeInnerHtml(\DOMNode $node): string
    {
        $innerHTML = '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $node->ownerDocument->saveHTML($child);
        }
        return $innerHTML;
    }

    static function unwrapNode(\DOMNode $node): void
    {
        while ($node->firstChild) {
            $node->parentNode->insertBefore($node->firstChild, $node);
        }
        $node->parentNode->removeChild($node);
    }
}