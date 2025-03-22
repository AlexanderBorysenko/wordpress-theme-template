<?php

use ThemeCore\ThemeModules\CarbonFields\CarbonFields;

function getMobileMarginBottomSelectField($name = 'mobile_margin_bottom', $caption = 'Mobile Margin Bottom')
{
    $margins = CarbonFields::getConfig()['mobile-margins'];

    return getSelectField($name, $caption, $margins, '', 'bottom');
}
