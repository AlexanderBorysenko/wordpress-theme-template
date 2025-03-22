<?php
// add the filter
add_filter('wpseo_breadcrumb_separator', function () {
    return '<svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M7 5.5L10 9L7 12.5" stroke="#BD3D40" stroke-width="1.8" stroke-linejoin="round"/>
    <path d="M7 5.5L10 9L7 12.5" stroke="#EB562E" stroke-width="1.8" stroke-linejoin="round"/>
    </svg>';
}, 10, 1);