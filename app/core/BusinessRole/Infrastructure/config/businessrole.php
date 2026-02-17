<?php

return [
    'default_role' => 'admin',
    'limit' => 500,
    'roles' => [

        'admin' => [
            // USER
            'erp.user.index',
            'erp.user.show',
            'erp.user.create',
            'erp.user.update',
            'erp.user.delete',

            // CUSTOMER
            'erp.customer.index',
            'erp.customer.show',
            'erp.customer.create',
            'erp.customer.update',
            'erp.customer.delete',

            // CUSTOMER GROUP 
            'erp.customergroup.index',
            'erp.customergroup.show',
            'erp.customergroup.create',
            'erp.customergroup.update',
            'erp.customergroup.delete',

            // SUPPLIER
            'erp.supplier.index',
            'erp.supplier.show',
            'erp.supplier.create',
            'erp.supplier.update',
            'erp.supplier.delete',

            // PRICE LIST
            'erp.pricelist.index',
            'erp.pricelist.show',
            'erp.pricelist.create',
            'erp.pricelist.update',
            'erp.pricelist.delete',

            // SHIPPING PROVIDER
            'erp.shipping.index',
            'erp.shipping.show',
            'erp.shipping.create',
            'erp.shipping.update',
            'erp.shipping.delete',

            // ORDER
            'erp.order.index',
            'erp.order.show',
            'erp.order.create',
            'erp.order.update',
            'erp.order.delete',
            'erp.order.approved',
            'erp.order.cancelled',

            // ORDER SHIPPING
            'erp.ordershipping.index',
            'erp.ordershipping.show',
            'erp.ordershipping.create',
            'erp.ordershipping.update',
            'erp.ordershipping.delete',

            // ORDER ITEM
            'erp.orderitem.index',
            'erp.orderitem.show',
            'erp.orderitem.create',
            'erp.orderitem.update',
            'erp.orderitem.delete',
            'erp.orderitem.completed',
            'erp.orderitem.summary',
            'erp.orderitem.cancelled',

            // PRODUCT
            'erp.product.index',
            'erp.product.show',
            'erp.product.create',
            'erp.product.update',
            'erp.product.delete',

            // CATEGORY PRODUCT
            'erp.categoryproduct.index',
            'erp.categoryproduct.show',
            'erp.categoryproduct.create',
            'erp.categoryproduct.update',
            'erp.categoryproduct.delete',

            // INVENTORY
            'erp.inventory.index',
            'erp.inventory.show',
            'erp.inventory.create',
            'erp.inventory.update',
            'erp.inventory.delete',

            // INVENTORY ADJUSTMENT
            'erp.inventoryadjustment.create',

            // INVOICE
            'erp.invoicein.index',
            'erp.invoicein.show',
            'erp.invoicein.create',
            'erp.invoicein.update',
            'erp.invoicein.delete',
            'erp.invoicein.cancelled',
            'erp.invoicein.approved',

            'erp.invoiceout.index',
            'erp.invoiceout.show',
            'erp.invoiceout.create',
            'erp.invoiceout.update',
            'erp.invoiceout.delete',
            'erp.invoiceout.unapproved',
            'erp.invoiceout.approved',

            'erp.custominvoiceout.create',
            'erp.custominvoiceout.update',
            'erp.custominvoiceout.delete',
            'erp.custominvoiceout.index',

            'erp.custominvoicein.create',
            'erp.custominvoicein.update',
            'erp.custominvoicein.delete',
            'erp.custominvoicein.index',

            // PURCHASE
            'erp.purchase.index',
            'erp.purchase.show',
            'erp.purchase.create',
            'erp.purchase.update',
            'erp.purchase.delete',
            'erp.purchase.cancelled',
            'erp.purchase.approved',
            'erp.purchase.requested',

            // PURCHASE ITEM
            'erp.purchaseitem.index',
            'erp.purchaseitem.show',
            'erp.purchaseitem.create',
            'erp.purchaseitem.update',
            'erp.purchaseitem.delete',

            // PURCHASE TAX
            'erp.purchasetax.create',

            // STOCK IN
            'erp.stockin.index',
            'erp.stockin.show',
            'erp.stockin.create',
            'erp.stockin.update',
            'erp.stockin.delete',
            'erp.stockin.cancelled',
            'erp.stockin.received',

            // STOCK OUT
            'erp.stockout.index',
            'erp.stockout.show',
            'erp.stockout.create',
            'erp.stockout.update',
            'erp.stockout.delete',
            'erp.stockout.completed',
            'erp.stockout.shipped',

            // STOCK MOVEMENT
            'erp.stockmovementin.index',
            'erp.stockmovementin.show',
            'erp.stockmovementin.create',
            'erp.stockmovementin.update',
            'erp.stockmovementin.delete',
            'erp.stockmovementin.completed',

            'erp.stockmovementout.index',
            'erp.stockmovementout.show',
            'erp.stockmovementout.create',
            'erp.stockmovementout.update',
            'erp.stockmovementout.delete',

            // WAREHOUSE
            'erp.warehouse.index',
            'erp.warehouse.show',
            'erp.warehouse.create',
            'erp.warehouse.update',
            'erp.warehouse.delete',

            // REPORT
            'erp.report.index',
            'erp.report.show',

            // BUSINESS SETTING
            'erp.business.update',

            // NOTIFICATION
            'erp.notification.index',
            'erp.notification.show',
            'erp.notification.create',
            'erp.notification.update',
            'erp.notification.delete',
            'erp.notification.many',

            // overview 
            'erp.overview.index',

            // sidebar 
            'erp.extension.index',
            'erp.extension.create',
            'erp.extension.update',
            'erp.extension.delete',
        ],


        // MANAGER
        'manager' => [

            // CUSTOMER
            'erp.customer.index',
            'erp.customer.show',
            'erp.customer.create',
            'erp.customer.update',
            'erp.customer.delete',

            // CUSTOMER GROUP 
            'erp.customergroup.index',
            'erp.customergroup.show',
            'erp.customergroup.create',
            'erp.customergroup.update',
            'erp.customergroup.delete',

            // SUPPLIER
            'erp.supplier.index',
            'erp.supplier.show',
            'erp.supplier.create',
            'erp.supplier.update',
            'erp.supplier.delete',

            // PRICE LIST
            'erp.pricelist.index',
            'erp.pricelist.show',
            'erp.pricelist.create',
            'erp.pricelist.update',
            'erp.pricelist.delete',

            // SHIPPING PROVIDER
            'erp.shipping.index',
            'erp.shipping.show',
            'erp.shipping.create',
            'erp.shipping.update',
            'erp.shipping.delete',

            // ORDER
            'erp.order.index',
            'erp.order.show',
            'erp.order.create',
            'erp.order.update',
            'erp.order.delete',
            'erp.order.approved',
            'erp.order.cancelled',

            // ORDER SHIPPING
            'erp.ordershipping.index',
            'erp.ordershipping.show',
            'erp.ordershipping.create',
            'erp.ordershipping.update',
            'erp.ordershipping.delete',

            // ORDER ITEM
            'erp.orderitem.index',
            'erp.orderitem.show',
            'erp.orderitem.create',
            'erp.orderitem.update',
            'erp.orderitem.delete',
            'erp.orderitem.completed',
            'erp.orderitem.summary',
            'erp.orderitem.cancelled',

            // PRODUCT
            'erp.product.index',
            'erp.product.show',
            'erp.product.create',
            'erp.product.update',
            'erp.product.delete',

            // CATEGORY PRODUCT
            'erp.categoryproduct.index',
            'erp.categoryproduct.show',
            'erp.categoryproduct.create',
            'erp.categoryproduct.update',
            'erp.categoryproduct.delete',

            // INVENTORY
            'erp.inventory.index',
            'erp.inventory.show',
            'erp.inventory.create',
            'erp.inventory.update',
            'erp.inventory.delete',

            // INVENTORY ADJUSTMENT
            'erp.inventoryadjustment.create',

            // INVOICE
            'erp.invoicein.index',
            'erp.invoicein.show',
            'erp.invoicein.create',
            'erp.invoicein.update',
            'erp.invoicein.delete',
            'erp.invoicein.cancelled',
            'erp.invoicein.approved',

            'erp.invoiceout.index',
            'erp.invoiceout.show',
            'erp.invoiceout.create',
            'erp.invoiceout.update',
            'erp.invoiceout.delete',
            'erp.invoiceout.unapproved',
            'erp.invoiceout.approved',

            'erp.custominvoiceout.create',
            'erp.custominvoiceout.update',
            'erp.custominvoiceout.delete',
            'erp.custominvoiceout.index',

            'erp.custominvoicein.create',
            'erp.custominvoicein.update',
            'erp.custominvoicein.delete',
            'erp.custominvoicein.index',

            // PURCHASE
            'erp.purchase.index',
            'erp.purchase.show',
            'erp.purchase.create',
            'erp.purchase.update',
            'erp.purchase.delete',
            'erp.purchase.cancelled',
            'erp.purchase.approved',
            'erp.purchase.requested',

            // PURCHASE ITEM
            'erp.purchaseitem.index',
            'erp.purchaseitem.show',
            'erp.purchaseitem.create',
            'erp.purchaseitem.update',
            'erp.purchaseitem.delete',

            // PURCHASE TAX
            'erp.purchasetax.create',

            // STOCK IN
            'erp.stockin.index',
            'erp.stockin.show',
            'erp.stockin.create',
            'erp.stockin.update',
            'erp.stockin.delete',
            'erp.stockin.cancelled',
            'erp.stockin.received',

            // STOCK OUT
            'erp.stockout.index',
            'erp.stockout.show',
            'erp.stockout.create',
            'erp.stockout.update',
            'erp.stockout.delete',
            'erp.stockout.completed',
            'erp.stockout.shipped',

            // STOCK MOVEMENT
            'erp.stockmovementin.index',
            'erp.stockmovementin.show',
            'erp.stockmovementin.create',
            'erp.stockmovementin.update',
            'erp.stockmovementin.delete',
            'erp.stockmovementin.completed',

            'erp.stockmovementout.index',
            'erp.stockmovementout.show',
            'erp.stockmovementout.create',
            'erp.stockmovementout.update',
            'erp.stockmovementout.delete',

            // WAREHOUSE
            'erp.warehouse.index',
            'erp.warehouse.show',
            'erp.warehouse.create',
            'erp.warehouse.update',
            'erp.warehouse.delete',

            // REPORT
            'erp.report.index',
            'erp.report.show',

            // NOTIFICATION
            'erp.notification.index',
            'erp.notification.show',
            'erp.notification.create',
            'erp.notification.update',
            'erp.notification.delete',
            'erp.notification.many',

            // overview 
            'erp.overview.index',
            // sidebar 
            'erp.extension.index',
            'erp.extension.create',
            'erp.extension.update',
            'erp.extension.delete',
        ],


        // SELLER
        'seller' => [
            // CUSTOMER
            'erp.customer.index',
            'erp.customer.show',
            'erp.customer.create',
            'erp.customer.update',
            'erp.customer.delete',

            // CUSTOMER GROUP 
            'erp.customergroup.index',
            'erp.customergroup.show',
            'erp.customergroup.create',
            'erp.customergroup.update',
            'erp.customergroup.delete',

            // PRICE LIST
            'erp.pricelist.index',
            'erp.pricelist.show',
            'erp.pricelist.create',
            'erp.pricelist.update',
            'erp.pricelist.delete',

            // ORDER
            'erp.order.index',
            'erp.order.show',
            'erp.order.create',
            'erp.order.update',

            // ORDER SHIPPING
            'erp.ordershipping.index',
            'erp.ordershipping.show',
            'erp.ordershipping.create',
            'erp.ordershipping.update',
            'erp.ordershipping.delete',

            // ORDER ITEM
            'erp.orderitem.index',
            'erp.orderitem.show',
            'erp.orderitem.create',
            'erp.orderitem.update',
            'erp.orderitem.delete',
            'erp.orderitem.completed',
            'erp.orderitem.summary',

            // PRODUCT
            'erp.product.index',

            // CATEGORY PRODUCT
            'erp.categoryproduct.index',
            'erp.categoryproduct.show',
            'erp.categoryproduct.create',
            'erp.categoryproduct.update',
            'erp.categoryproduct.delete',

            // INVENTORY
            'erp.inventory.update',

            // NOTIFICATION
            'erp.notification.index',
            'erp.notification.show',
            'erp.notification.create',
            'erp.notification.update',
            'erp.notification.delete',
            'erp.notification.many',
        ],


        // ACCOUNTANTER
        'accountanter' => [

            // CATEGORY PRODUCT
            'erp.categoryproduct.index',
            'erp.categoryproduct.show',
            'erp.categoryproduct.create',
            'erp.categoryproduct.update',
            'erp.categoryproduct.delete',

            // INVOICE
            'erp.invoicein.create',
            'erp.invoiceout.create',

            // STOCK IN
            'erp.stockin.create',

            // STOCK OUT
            'erp.stockout.create',

            // REPORT
            'erp.report.index',
            'erp.report.show',
            // PRODUCT
            'erp.product.index',

            // NOTIFICATION
            'erp.notification.index',
            'erp.notification.show',
            'erp.notification.create',
            'erp.notification.update',
            'erp.notification.delete',
            'erp.notification.many',

            // Custom invoice out
            'erp.custominvoiceout.create',
            'erp.custominvoiceout.update',
            'erp.custominvoiceout.delete',
            'erp.custominvoiceout.index',
            // Custom invoice in
            'erp.custominvoicein.create',
            'erp.custominvoicein.update',
            'erp.custominvoicein.delete',
            'erp.custominvoicein.index',
        ],


        // WAREHOUSEMAN
        'warehouseman' => [

            // STOCK MOVEMENT
            'erp.stockmovementin.index',
            'erp.stockmovementin.show',
            'erp.stockmovementin.create',
            'erp.stockmovementin.update',
            'erp.stockmovementin.delete',
            'erp.stockmovementin.completed',

            'erp.stockmovementout.index',
            'erp.stockmovementout.show',
            'erp.stockmovementout.create',
            'erp.stockmovementout.update',
            'erp.stockmovementout.delete',

            // WAREHOUSE
            'erp.warehouse.index',
            'erp.warehouse.show',
            'erp.warehouse.create',
            'erp.warehouse.update',

            // INVENTORY
            'erp.inventory.index',
            'erp.inventory.show',
            'erp.inventory.create',
            'erp.inventory.update',

            // SHIPPING PROVIDER
            'erp.shipping.index',
            'erp.shipping.show',
            'erp.shipping.create',
            'erp.shipping.update',
            'erp.shipping.delete',
            // ORDER SHIPPING
            'erp.ordershipping.update',
            // STOCK OUT
            'erp.stockout.shipped',
            'erp.stockout.completed',
            // ORDER ITEM 
            'erp.orderitem.completed',
            // STOCK IN
            'erp.stockin.update',
            'erp.stockin.received',
            // PRODUCT
            'erp.product.index',

            // NOTIFICATION
            'erp.notification.index',
            'erp.notification.show',
            'erp.notification.create',
            'erp.notification.update',
            'erp.notification.delete',
            'erp.notification.many',
        ],


        // PURCHASER
        'purchaser' => [

            // SUPPLIER
            'erp.supplier.index',
            'erp.supplier.show',
            'erp.supplier.create',
            'erp.supplier.update',
            'erp.supplier.delete',

            // PURCHASE
            'erp.purchase.index',
            'erp.purchase.show',
            'erp.purchase.create',
            'erp.purchase.update',
            'erp.purchase.delete',
            'erp.purchase.requested',

            // PURCHASE ITEM
            'erp.purchaseitem.index',
            'erp.purchaseitem.show',
            'erp.purchaseitem.create',
            'erp.purchaseitem.update',
            'erp.purchaseitem.delete',

            // PURCHASE TAX
            'erp.purchasetax.create',

            // PRODUCT
            'erp.product.index',

            // NOTIFICATION
            'erp.notification.index',
            'erp.notification.show',
            'erp.notification.create',
            'erp.notification.update',
            'erp.notification.delete',
            'erp.notification.many',
        ],
    ],
    'nav' => [

        [
            'to'        => '/',
            'link'      => null,
            'icon'      => 'bi bi-bar-chart',
            'label'     => 'Overview',
            'ability'   => 'erp.overview.index',
        ],

        [
            'to'        => '/purchases',
            'link'      => null,
            'icon'      => 'bi bi-currency-dollar',
            'label'     => 'Purchases',
            'ability'   => 'erp.purchase.index',
        ],

        [
            'to'        => '/orders',
            'link'      => null,
            'icon'      => 'bi bi-cart',
            'label'     => 'Orders',
            'ability'   => 'erp.order.index',
        ],

        [
            'to'        => '/products',
            'link'      => null,
            'icon'      => 'bi bi-box',
            'label'     => 'Products',
            'ability'   => 'erp.product.index',
        ],

        [
            'to'        => '/suppliers',
            'link'      => null,
            'icon'      => 'bi bi-people',
            'label'     => 'Suppliers',
            'ability'   => 'erp.supplier.index',
        ],

        [
            'to'        => '/customers',
            'link'      => null,
            'icon'      => 'bi bi-people',
            'label'     => 'Customers',
            'ability'   => 'erp.customer.index',
        ],

        [
            'to'        => '/warehouses',
            'link'      => null,
            'icon'      => 'bi bi-building',
            'label'     => 'Warehouses',
            'ability'   => 'erp.warehouse.index',
        ],

        [
            'to'        => '/stocks',
            'link'      => null,
            'icon'      => 'bi bi-file-earmark-text',
            'label'     => 'Stocks',
            'ability'   => 'erp.stockin.index', 
        ],

        [
            'to'        => '/inventories',
            'link'      => null,
            'icon'      => 'bi bi-clipboard-data',
            'label'     => 'Inventories',
            'ability'   => 'erp.inventory.index',
        ],

        [
            'to'        => '/invoices',
            'link'      => null,
            'icon'      => 'bi bi-receipt',
            'label'     => 'Invoices',
            'ability'   => 'erp.invoicein.index', 
        ],

        [
            'to'        => '/shippings',
            'link'      => null,
            'icon'      => 'bi bi-truck',
            'label'     => 'Shipping Providers',
            'ability'   => 'erp.shipping.index',
        ],

        [
            'to'        => '/settings',
            'link'      => null,
            'icon'      => 'bi bi-gear',
            'label'     => 'Settings',
            'ability'   => 'erp.business.update',
        ],

        [
            'to'        => '/users',
            'link'      => null,
            'icon'      => 'bi bi-people-fill',
            'label'     => 'Employees',
            'ability'   => 'erp.user.index',
        ],

        [
            'to'        => '/activity-logs',
            'link'      => null,
            'icon'      => 'bi bi-diagram-3',
            'label'     => 'Logs',
            'ability'   => 'erp.notification.index',
        ],

        [
            'to'        => '/extensions',
            'link'      => null,
            'icon'      => 'bi bi-diagram-3',
            'label'     => 'Extension',
            'ability'   => 'erp.extension.index',
        ],
    ],

];
