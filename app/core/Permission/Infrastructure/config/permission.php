<?php

return [
    'permissions' => [

        'Employee' => [
            'erp.user.index',
            'erp.user.show',
            'erp.user.create',
            'erp.user.update',
            'erp.user.delete',
        ],

        'Customer' => [
            'erp.customer.index',
            'erp.customer.show',
            'erp.customer.create',
            'erp.customer.update',
            'erp.customer.delete',
            'erp.customer.creatordershipping',
        ],

        'Customer group' => [
            'erp.customergroup.index',
            'erp.customergroup.show',
            'erp.customergroup.create',
            'erp.customergroup.update',
            'erp.customergroup.delete',
        ],

        'Supplier' => [
            'erp.supplier.index',
            'erp.supplier.show',
            'erp.supplier.create',
            'erp.supplier.update',
            'erp.supplier.delete',
        ],

        'Price list' => [
            'erp.pricelist.index',
            'erp.pricelist.show',
            'erp.pricelist.create',
            'erp.pricelist.update',
            'erp.pricelist.delete',
        ],

        'Shipping' => [
            'erp.shipping.index',
            'erp.shipping.show',
            'erp.shipping.create',
            'erp.shipping.update',
            'erp.shipping.delete',
        ],

        'Order' => [
            'erp.order.index',
            'erp.order.show',
            'erp.order.create',
            'erp.order.update',
            'erp.order.delete',
            'erp.order.approved',
            'erp.order.cancelled',
        ],

        'Order shipping' => [
            'erp.ordershipping.index',
            'erp.ordershipping.show',
            'erp.ordershipping.create',
            'erp.ordershipping.update',
            'erp.ordershipping.delete',
        ],

        'Order item' => [
            'erp.orderitem.index',
            'erp.orderitem.show',
            'erp.orderitem.create',
            'erp.orderitem.update',
            'erp.orderitem.delete',
            'erp.orderitem.completed',
            'erp.orderitem.summary',
            'erp.orderitem.cancelled',
        ],

        'Product' => [
            'erp.product.index',
            'erp.product.show',
            'erp.product.create',
            'erp.product.update',
            'erp.product.delete',
        ],

        'Product categories' => [
            'erp.categoryproduct.index',
            'erp.categoryproduct.show',
            'erp.categoryproduct.create',
            'erp.categoryproduct.update',
            'erp.categoryproduct.delete',
        ],

        'Inventory' => [
            'erp.inventory.index',
            'erp.inventory.show',
            'erp.inventory.create',
            'erp.inventory.update',
            'erp.inventory.delete',
        ],

        'Inventory adjustment' => [
            'erp.inventoryadjustment.create',
            'erp.inventoryadjustment.index',
        ],

        'Invoice in' => [
            'erp.invoicein.index',
            'erp.invoicein.show',
            'erp.invoicein.create',
            'erp.invoicein.update',
            'erp.invoicein.delete',
            'erp.invoicein.cancelled',
            'erp.invoicein.approved',
        ],

        'Invoice out' => [
            'erp.invoiceout.index',
            'erp.invoiceout.show',
            'erp.invoiceout.create',
            'erp.invoiceout.update',
            'erp.invoiceout.delete',
            'erp.invoiceout.unapproved',
            'erp.invoiceout.approved',
        ],

        'Custom invoice out' => [
            'erp.custominvoiceout.create',
            'erp.custominvoiceout.update',
            'erp.custominvoiceout.delete',
            'erp.custominvoiceout.index',
        ],

        'Custom invoice in' => [
            'erp.custominvoicein.create',
            'erp.custominvoicein.update',
            'erp.custominvoicein.delete',
            'erp.custominvoicein.index',
        ],

        'Purchase' => [
            'erp.purchase.index',
            'erp.purchase.show',
            'erp.purchase.create',
            'erp.purchase.update',
            'erp.purchase.delete',
            'erp.purchase.cancelled',
            'erp.purchase.approved',
            'erp.purchase.requested',
        ],

        'Purchase item' => [
            'erp.purchaseitem.index',
            'erp.purchaseitem.show',
            'erp.purchaseitem.create',
            'erp.purchaseitem.update',
            'erp.purchaseitem.delete',
        ],

        'Purchase tax' => [
            'erp.purchasetax.create',
        ],

        'Stock in' => [
            'erp.stockin.index',
            'erp.stockin.show',
            'erp.stockin.create',
            'erp.stockin.update',
            'erp.stockin.delete',
            'erp.stockin.cancelled',
            'erp.stockin.received',
        ],

        'Stock out' => [
            'erp.stockout.index',
            'erp.stockout.show',
            'erp.stockout.create',
            'erp.stockout.update',
            'erp.stockout.delete',
            'erp.stockout.completed',
            'erp.stockout.shipped',
        ],

        'Stock movement in' => [
            'erp.stockmovementin.index',
            'erp.stockmovementin.show',
            'erp.stockmovementin.create',
            'erp.stockmovementin.update',
            'erp.stockmovementin.delete',
            'erp.stockmovementin.completed',
        ],

        'Stock movement out' => [
            'erp.stockmovementout.index',
            'erp.stockmovementout.show',
            'erp.stockmovementout.create',
            'erp.stockmovementout.update',
            'erp.stockmovementout.delete',
        ],

        'Warehouse' => [
            'erp.warehouse.index',
            'erp.warehouse.show',
            'erp.warehouse.create',
            'erp.warehouse.update',
            'erp.warehouse.delete',
        ],

        'Business' => [
            'erp.business.update',
        ],

        'Overview' => [
            'erp.overview.index',
        ],

        'Extension' => [
            'erp.extension.index',
            'erp.extension.create',
            'erp.extension.update',
            'erp.extension.delete',
        ],

        'Permission group' => [
            'erp.permissiongroup.index',
            'erp.permissiongroup.show',
            'erp.permissiongroup.create',
            'erp.permissiongroup.update',
            'erp.permissiongroup.delete',
        ],

        'Permission' => [
            'erp.permission.index',
            'erp.permission.show',
            'erp.permission.create',
            'erp.permission.update'
        ],

        'Permission group user' => [
            'erp.permissiongroupuser.create',
            'erp.permissiongroupuser.delete'
        ],
        'Workflow Notification' => [
            'erp.notification.workflow',
        ]
    ],

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
                    'ability' => 'erp.notification.index',
                ],
            ],
        ],
    ],

];
