<?php

return [
    'install' => [
        'commands' => [
            [
                'name' => 'app:npmbuild',
                'description' => 'Build frontend assets',
                'risk' => 'low',
            ]
        ],

        'migrations' => [
            '2026_02_12_223808_create_fastmode_table.php',
        ],
    ],

    'uninstall' => [
        'commands' => [
            [
                'name' => 'app:npmbuild',
                'description' => 'Rebuild frontend after uninstall',
                'risk' => 'low',
            ]
        ],

        'migrations' => [
            '2026_02_12_223808_create_fastmode_table.php',
        ],
    ],
];
