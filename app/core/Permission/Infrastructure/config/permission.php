<?php

return [

    'nav' => [
        // ===== Dashboard =====
        [
            'type'      => 'group',
            'label'     => 'Dashboard',
            'icon'      => 'bi bi-speedometer2',
            'children'  => [
                [
                    'to'      => '/',
                    'icon'    => 'bi bi-bar-chart',
                    'label'   => 'Overview',
                    'ability' => 'erp.overview.index',
                ],
            ],
        ],

        // ===== Sales =====
        [
            'type'      => 'group',
            'label'     => 'Sales',
            'icon'      => 'bi bi-cart',
            'children'  => [
                [
                    'to'      => '/orders',
                    'icon'    => 'bi bi-cart',
                    'label'   => 'Orders',
                    'ability' => 'erp.order.index',
                ],
                [
                    'to'      => '/customers',
                    'icon'    => 'bi bi-people',
                    'label'   => 'Customers',
                    'ability' => 'erp.customer.index',
                ],
                [
                    'to'      => '/customer-groups',
                    'icon'    => 'bi bi-people',
                    'label'   => 'Customer group',
                    'ability' => 'erp.customergroup.index',
                ],
                [
                    'to'      => '/invoice-outs',
                    'icon'    => 'bi bi-receipt',
                    'label'   => 'Invoice outs',
                    'ability' => 'erp.invoiceout.index',
                ],
                [
                    'to'      => '/custom-invoice-outs',
                    'icon'    => 'bi bi-receipt',
                    'label'   => 'Custom invoice outs',
                    'ability' => 'erp.custominvoiceout.index',
                ],
                [
                    'to'      => '/shippings',
                    'icon'    => 'bi bi-truck',
                    'label'   => 'Shipping providers',
                    'ability' => 'erp.shipping.index',
                ],
                [
                    'to'      => '/stock-outs',
                    'icon'    => 'bi bi-file-earmark-text',
                    'label'   => 'Stock outs',
                    'ability' => 'erp.stockout.index',
                ],
            ],
        ],

        // ===== Purchasing =====
        [
            'type'      => 'group',
            'label'     => 'Purchasing',
            'icon'      => 'bi bi-currency-dollar',
            'children'  => [
                [
                    'to'      => '/purchases',
                    'icon'    => 'bi bi-currency-dollar',
                    'label'   => 'Purchases',
                    'ability' => 'erp.purchase.index',
                ],
                [
                    'to'      => '/suppliers',
                    'icon'    => 'bi bi-people',
                    'label'   => 'Suppliers',
                    'ability' => 'erp.supplier.index',
                ],
                [
                    'to'      => '/invoice-ins',
                    'icon'    => 'bi bi-receipt',
                    'label'   => 'Invoice ins',
                    'ability' => 'erp.invoicein.index',
                ],
                [
                    'to'      => '/custom-invoice-ins',
                    'icon'    => 'bi bi-receipt',
                    'label'   => 'Custom invoice ins',
                    'ability' => 'erp.custominvoicein.index',
                ],
                [
                    'to'      => '/stock-ins',
                    'icon'    => 'bi bi-file-earmark-text',
                    'label'   => 'Stock ins',
                    'ability' => 'erp.stockin.index',
                ],
            ],
        ],

        // ===== Inventory =====
        [
            'type'      => 'group',
            'label'     => 'Inventory',
            'icon'      => 'bi bi-box-seam',
            'children'  => [
                [
                    'to'      => '/products',
                    'icon'    => 'bi bi-box',
                    'label'   => 'Products',
                    'ability' => 'erp.product.index',
                ],
                [
                    'to'      => '/category-product',
                    'icon'    => 'bi bi-box',
                    'label'   => 'Product categories',
                    'ability' => 'erp.categoryproduct.index',
                ],
                [
                    'to'      => '/price-list',
                    'icon'    => 'bi bi-box',
                    'label'   => 'Price list',
                    'ability' => 'erp.pricelist.index',
                ],
                [
                    'to'      => '/warehouses',
                    'icon'    => 'bi bi-building',
                    'label'   => 'Warehouses',
                    'ability' => 'erp.warehouse.index',
                ],
                [
                    'to'      => '/inventories',
                    'icon'    => 'bi bi-clipboard-data',
                    'label'   => 'Inventories',
                    'ability' => 'erp.inventory.index',
                ],
                [
                    'to'      => '/inventories-adjustments',
                    'icon'    => 'bi bi-clipboard-data',
                    'label'   => 'Adjustments',
                    'ability' => 'erp.inventoryadjustment.index',
                ],
            ],
        ],

        // ===== Organization =====
        [
            'type'      => 'group',
            'label'     => 'Organization',
            'icon'      => 'bi bi-people-fill',
            'children'  => [
                [
                    'to'      => '/users',
                    'icon'    => 'bi bi-people-fill',
                    'label'   => 'Employees',
                    'ability' => 'erp.user.index',
                ],
            ],
        ],

        // ===== System =====
        [
            'type'      => 'group',
            'label'     => 'System',
            'icon'      => 'bi bi-gear',
            'children'  => [
                [
                    'to'      => '/settings',
                    'icon'    => 'bi bi-gear',
                    'label'   => 'Settings',
                    'ability' => 'erp.business.update',
                ],
                [
                    'to'      => '/permission-group',
                    'icon'    => 'bi bi-key',
                    'label'   => 'Permissions',
                    'ability' => 'erp.permissiongroup.index',
                ],
                [
                    'to'      => '/extensions',
                    'icon'    => 'bi bi-gear-wide-connected',
                    'label'   => 'Extension',
                    'ability' => 'erp.extension.index',
                ],
                [
                    'to'      => '/activity-logs',
                    'icon'    => 'bi bi-diagram-3',
                    'label'   => 'Logs',
                    'ability' => 'erp.activitylog.index',
                ],
            ],
        ],
    ]
];
