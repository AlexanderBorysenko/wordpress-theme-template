<?php
return [
    'recaptcha' => [
        'siteKey' => '',
        'secretKey' => '',
    ],
    'telegram' => [
        'botToken' => '',
        'chatId' => '',
    ],

    'hotReloadTriggers' => [
        'app-*.js',
        'app-*.css',
    ],
    // from source/build directory
    'assets' => [
        'js' => [
        ],
        'lazyJs' => [
            'app-*.js',
        ],
        'css' => [
            'app-*.css',
        ],
        'adminJs' => [
            'wp-admin-*.js',
        ],
        'adminCss' => [
            'wp-admin-*.css',
        ],
    ],
];