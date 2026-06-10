<?php

return [
    'package' => 'com_acme_vue_notes',
    'name' => 'Vue Notes',
    'enable' => true,
    'theme' => 'default',
    'frontend' => [
        'profile' => 'spa',
        'stack' => 'vue',
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
            'routes/api.php',
        ],
    ],
];
