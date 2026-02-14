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
            '2026_02_03_000000_create_leave_requests_table.php',
            '2026_02_03_000001_create_time_attendances_table.php',
            '2026_02_03_000002_create_end_of_day_reports_table.php',
            '2026_02_03_000003_create_month_summary_table.php',
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
        ],
    ],
];