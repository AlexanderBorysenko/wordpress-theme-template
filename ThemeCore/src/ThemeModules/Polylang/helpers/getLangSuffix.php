<?php
function getLangSuffix()
{
    if (function_exists('pll_current_language')) {
        return '_' . pll_current_language();
    }
    return '';
}