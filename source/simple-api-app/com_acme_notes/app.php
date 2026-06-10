<?php

return [
    'package' => 'com_acme_notes',
    'name' => 'Notes',
    'enable' => true,
    'theme' => 'default',
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
            'routes/api.php',
        ],
    ],
];
