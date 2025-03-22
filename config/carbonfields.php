<?php
return [
    'sources'        => [
        'carbon/post-meta/*.php',
        'carbon/term-meta/*.php',
        'carbon/theme-options/*.php',
        'carbon/user-meta/*.php',
        path_join(getThemeСonfig('components.base'), '*.carbon.php')
    ],
    'margins'        => [
        'Initial' => [
            'bottom' => ''
        ],
        '1'       => [
            'bottom' => 'mb-1'
        ],
        '2'       => [
            'bottom' => 'mb-2'
        ],
        '3'       => [
            'bottom' => 'mb-3'
        ],
        '4'       => [
            'bottom' => 'mb-4'
        ],
        '5'       => [
            'bottom' => 'mb-5'
        ],
    ],
    'mobile-margins' => [
        'None' => [
            'bottom' => '',
        ],
        '0-25' => [
            'bottom' => 'mb-mobile-0-25',
        ],
        '0-5'  => [
            'bottom' => 'mb-mobile-0-5',
        ],
        '1'    => [
            'bottom' => 'mb-mobile-1',
        ],
    ]
];