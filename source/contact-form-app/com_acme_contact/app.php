<?php

return [
    'package' => 'com_acme_contact',
    'name' => 'Contact',
    'enable' => true,
    'theme' => 'default',
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
