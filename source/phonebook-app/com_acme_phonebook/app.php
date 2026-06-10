<?php

return [
    'package' => 'com_acme_phonebook',
    'name' => 'Phonebook',
    'enable' => true,
    'theme' => 'default',
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
