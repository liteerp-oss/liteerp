<?php

return [
    'install' => [
        'commands' => [
            [
                'name' => 'app:npmbuild',
                'description' => 'Build frontend assets',
                'risk' => 'low',
            ],
        ],

        'migrations' => [
            '2026_01_28_102448_create_smtp_table.php',
        ],
    ],

    'uninstall' => [
        'commands' => [
            [
                'name' => 'app:npmbuild',
                'description' => 'Rebuild frontend after uninstall',
                'risk' => 'low',
            ],
        ],

        'migrations' => [
            // rollback handled by core
            '2026_01_28_102448_create_smtp_table.php'
        ],
    ],
];
