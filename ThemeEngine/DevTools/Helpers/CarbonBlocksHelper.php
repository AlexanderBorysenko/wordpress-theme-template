<?php
namespace DevTools\Helpers;

class CarbonBlocksHelper
{
    public static function getTheBlockRawDatabaseMarkup($blockName, $blockAttributes)
    {
        $attributesJson = json_encode($blockAttributes);
        $output = "<!-- wp:$blockName $attributesJson /-->\n\n";
        return $output;
    }
}