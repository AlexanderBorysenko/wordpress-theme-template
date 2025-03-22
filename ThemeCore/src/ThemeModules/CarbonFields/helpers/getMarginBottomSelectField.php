<?php

use ThemeCore\ThemeModules\CarbonFields\CarbonFields;

function getMarginBottomSelectField($name = 'margin_bottom', $caption = 'Margin Bottom')
{
    $margins = CarbonFields::getConfig()['margins'];

    return getSelectField($name, $caption, $margins, '', 'bottom');
}