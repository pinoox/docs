<?php

return [
    'package' => 'com_acme_react_tasks',
    'name' => 'React Tasks',
    'enable' => true,
    'theme' => 'default',
    'frontend' => [
        'profile' => 'spa',
        'stack' => 'react',
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
            'routes/api.php',
        ],
    ],
];
