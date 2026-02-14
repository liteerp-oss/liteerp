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
            '2026_02_14_000000_create_inventory_tracking_table.php',
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
            '2026_02_14_000000_create_inventory_tracking_table.php',
        ],
    ],
];
