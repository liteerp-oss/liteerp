<?php

return [
    'install' => [
        'commands' => [
            // [
            //     'name' => 'app:npmbuild',
            //     'description' => 'Build frontend assets',
            //     'risk' => 'low',
            // ],
        ],

        'migrations' => [
            // '2025_01_01_000000_create_example_table.php',
        ],
    ],

    'uninstall' => [
        'commands' => [
            // [
            //     'name' => 'app:npmbuild',
            //     'description' => 'Rebuild frontend after uninstall',
            //     'risk' => 'low',
            // ],
        ],

        'migrations' => [
            // rollback handled by core
        ],
    ],
];
