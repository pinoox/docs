<?php

return [
    'package' => 'com_acme_tasks',
    'name' => 'Tasks',
    'enable' => true,
    'theme' => 'default',
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
