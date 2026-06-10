<?php

return [
    'package' => 'com_acme_vite_shop',
    'name' => 'Vite Shop',
    'enable' => true,
    'theme' => 'default',
    'frontend' => [
        'profile' => 'hybrid',
        'stack' => 'vite',
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
        ],
    ],
];
