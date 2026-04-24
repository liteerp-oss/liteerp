<?php

namespace App\Supports\Permissions\Enums;

enum Permission: string
{
    case AUTHENCATION_CREATE_ADMIN = "erp.authencation.create_admin";

    case USER_INDEX = 'erp.user.index';
    case USER_SHOW = 'erp.user.show';
    case USER_CREATE = 'erp.user.create';
    case USER_UPDATE = 'erp.user.update';
    case USER_DELETE = 'erp.user.delete';

    case CUSTOMER_INDEX = 'erp.customer.index';
    case CUSTOMER_SHOW = 'erp.customer.show';
    case CUSTOMER_CREATE = 'erp.customer.create';
    case CUSTOMER_UPDATE = 'erp.customer.update';
    case CUSTOMER_DELETE = 'erp.customer.delete';
    case CUSTOMER_CREATORDEALERSHIPPING = 'erp.customer.creatordershipping';

    case CUSTOMERGROUP_INDEX = 'erp.customergroup.index';
    case CUSTOMERGROUP_SHOW = 'erp.customergroup.show';
    case CUSTOMERGROUP_CREATE = 'erp.customergroup.create';
    case CUSTOMERGROUP_UPDATE = 'erp.customergroup.update';
    case CUSTOMERGROUP_DELETE = 'erp.customergroup.delete';

    case SUPPLIER_INDEX = 'erp.supplier.index';
    case SUPPLIER_SHOW = 'erp.supplier.show';
    case SUPPLIER_CREATE = 'erp.supplier.create';
    case SUPPLIER_UPDATE = 'erp.supplier.update';
    case SUPPLIER_DELETE = 'erp.supplier.delete';

    case PRICELIST_INDEX = 'erp.pricelist.index';
    case PRICELIST_SHOW = 'erp.pricelist.show';
    case PRICELIST_CREATE = 'erp.pricelist.create';
    case PRICELIST_UPDATE = 'erp.pricelist.update';
    case PRICELIST_DELETE = 'erp.pricelist.delete';

    case SHIPPING_INDEX = 'erp.shipping.index';
    case SHIPPING_SHOW = 'erp.shipping.show';
    case SHIPPING_CREATE = 'erp.shipping.create';
    case SHIPPING_UPDATE = 'erp.shipping.update';
    case SHIPPING_DELETE = 'erp.shipping.delete';

    case ORDER_INDEX = 'erp.order.index';
    case ORDER_SHOW = 'erp.order.show';
    case ORDER_CREATE = 'erp.order.create';
    case ORDER_UPDATE = 'erp.order.update';
    case ORDER_DELETE = 'erp.order.delete';
    case ORDER_APPROVED = 'erp.order.approved';
    case ORDER_CANCELLED = 'erp.order.cancelled';

    case ORDERSHIPPING_INDEX = 'erp.ordershipping.index';
    case ORDERSHIPPING_SHOW = 'erp.ordershipping.show';
    case ORDERSHIPPING_CREATE = 'erp.ordershipping.create';
    case ORDERSHIPPING_UPDATE = 'erp.ordershipping.update';
    case ORDERSHIPPING_DELETE = 'erp.ordershipping.delete';

    case ORDERITEM_INDEX = 'erp.orderitem.index';
    case ORDERITEM_SHOW = 'erp.orderitem.show';
    case ORDERITEM_CREATE = 'erp.orderitem.create';
    case ORDERITEM_UPDATE = 'erp.orderitem.update';
    case ORDERITEM_DELETE = 'erp.orderitem.delete';
    case ORDERITEM_COMPLETED = 'erp.orderitem.completed';
    case ORDERITEM_SUMMARY = 'erp.orderitem.summary';
    case ORDERITEM_CANCELLED = 'erp.orderitem.cancelled';

    case PRODUCT_INDEX = 'erp.product.index';
    case PRODUCT_SHOW = 'erp.product.show';
    case PRODUCT_CREATE = 'erp.product.create';
    case PRODUCT_UPDATE = 'erp.product.update';
    case PRODUCT_DELETE = 'erp.product.delete';

    case CATEGORYPRODUCT_INDEX = 'erp.categoryproduct.index';
    case CATEGORYPRODUCT_SHOW = 'erp.categoryproduct.show';
    case CATEGORYPRODUCT_CREATE = 'erp.categoryproduct.create';
    case CATEGORYPRODUCT_UPDATE = 'erp.categoryproduct.update';
    case CATEGORYPRODUCT_DELETE = 'erp.categoryproduct.delete';

    case INVENTORY_INDEX = 'erp.inventory.index';
    case INVENTORY_SHOW = 'erp.inventory.show';

    case INVENTORYADJUSTMENT_CREATE = 'erp.inventoryadjustment.create';
    case INVENTORYADJUSTMENT_INDEX = 'erp.inventoryadjustment.index';

    case INVOICEIN_INDEX = 'erp.invoicein.index';
    case INVOICEIN_SHOW = 'erp.invoicein.show';
    case INVOICEIN_CREATE = 'erp.invoicein.create';
    case INVOICEIN_UPDATE = 'erp.invoicein.update';
    case INVOICEIN_DELETE = 'erp.invoicein.delete';
    case INVOICEIN_CANCELLED = 'erp.invoicein.cancelled';
    case INVOICEIN_APPROVED = 'erp.invoicein.approved';

    case INVOICEOUT_INDEX = 'erp.invoiceout.index';
    case INVOICEOUT_SHOW = 'erp.invoiceout.show';
    case INVOICEOUT_CREATE = 'erp.invoiceout.create';
    case INVOICEOUT_UPDATE = 'erp.invoiceout.update';
    case INVOICEOUT_DELETE = 'erp.invoiceout.delete';
    case INVOICEOUT_UNAPPROVED = 'erp.invoiceout.unapproved';
    case INVOICEOUT_APPROVED = 'erp.invoiceout.approved';

    case CUSTOMINVOICEOUT_CREATE = 'erp.custominvoiceout.create';
    case CUSTOMINVOICEOUT_UPDATE = 'erp.custominvoiceout.update';
    case CUSTOMINVOICEOUT_DELETE = 'erp.custominvoiceout.delete';
    case CUSTOMINVOICEOUT_INDEX = 'erp.custominvoiceout.index';

    case CUSTOMINVOICEIN_CREATE = 'erp.custominvoicein.create';
    case CUSTOMINVOICEIN_UPDATE = 'erp.custominvoicein.update';
    case CUSTOMINVOICEIN_DELETE = 'erp.custominvoicein.delete';
    case CUSTOMINVOICEIN_INDEX = 'erp.custominvoicein.index';

    case PURCHASE_INDEX = 'erp.purchase.index';
    case PURCHASE_SHOW = 'erp.purchase.show';
    case PURCHASE_CREATE = 'erp.purchase.create';
    case PURCHASE_UPDATE = 'erp.purchase.update';
    case PURCHASE_DELETE = 'erp.purchase.delete';
    case PURCHASE_CANCELLED = 'erp.purchase.cancelled';
    case PURCHASE_APPROVED = 'erp.purchase.approved';
    case PURCHASE_REQUESTED = 'erp.purchase.requested';

    case PURCHASEITEM_INDEX = 'erp.purchaseitem.index';
    case PURCHASEITEM_SHOW = 'erp.purchaseitem.show';
    case PURCHASEITEM_CREATE = 'erp.purchaseitem.create';
    case PURCHASEITEM_UPDATE = 'erp.purchaseitem.update';
    case PURCHASEITEM_DELETE = 'erp.purchaseitem.delete';

    case PURCHASETAX_CREATE = 'erp.purchasetax.create';

    case STOCKIN_INDEX = 'erp.stockin.index';
    case STOCKIN_SHOW = 'erp.stockin.show';
    case STOCKIN_CREATE = 'erp.stockin.create';
    case STOCKIN_UPDATE = 'erp.stockin.update';
    case STOCKIN_DELETE = 'erp.stockin.delete';
    case STOCKIN_CANCELLED = 'erp.stockin.cancelled';
    case STOCKIN_RECEIVED = 'erp.stockin.received';

    case STOCKOUT_INDEX = 'erp.stockout.index';
    case STOCKOUT_SHOW = 'erp.stockout.show';
    case STOCKOUT_CREATE = 'erp.stockout.create';
    case STOCKOUT_UPDATE = 'erp.stockout.update';
    case STOCKOUT_DELETE = 'erp.stockout.delete';
    case STOCKOUT_COMPLETED = 'erp.stockout.completed';
    case STOCKOUT_SHIPPED = 'erp.stockout.shipped';
    case STOCKOUT_CANCELLED = "erp.stockout.cancelled";

    case STOCKMOVEMENTIN_INDEX = 'erp.stockmovementin.index';
    case STOCKMOVEMENTIN_SHOW = 'erp.stockmovementin.show';
    case STOCKMOVEMENTIN_CREATE = 'erp.stockmovementin.create';
    case STOCKMOVEMENTIN_UPDATE = 'erp.stockmovementin.update';
    case STOCKMOVEMENTIN_DELETE = 'erp.stockmovementin.delete';
    case STOCKMOVEMENTIN_COMPLETED = 'erp.stockmovementin.completed';

    case STOCKMOVEMENTOUT_INDEX = 'erp.stockmovementout.index';
    case STOCKMOVEMENTOUT_SHOW = 'erp.stockmovementout.show';
    case STOCKMOVEMENTOUT_CREATE = 'erp.stockmovementout.create';
    case STOCKMOVEMENTOUT_UPDATE = 'erp.stockmovementout.update';
    case STOCKMOVEMENTOUT_DELETE = 'erp.stockmovementout.delete';

    case WAREHOUSE_INDEX = 'erp.warehouse.index';
    case WAREHOUSE_SHOW = 'erp.warehouse.show';
    case WAREHOUSE_CREATE = 'erp.warehouse.create';
    case WAREHOUSE_UPDATE = 'erp.warehouse.update';
    case WAREHOUSE_DELETE = 'erp.warehouse.delete';

    case BUSINESS_UPDATE = 'erp.business.update';
    case BUSINESS_CREATE = 'erp.business.create';

    case OVERVIEW_INDEX = 'erp.overview.index';

    case EXTENSION_INDEX = 'erp.extension.index';
    case EXTENSION_CREATE = 'erp.extension.create';
    case EXTENSION_UPDATE = 'erp.extension.update';
    case EXTENSION_DELETE = 'erp.extension.delete';

    case PERMISSIONGROUP_INDEX = 'erp.permissiongroup.index';
    case PERMISSIONGROUP_SHOW = 'erp.permissiongroup.show';
    case PERMISSIONGROUP_CREATE = 'erp.permissiongroup.create';
    case PERMISSIONGROUP_UPDATE = 'erp.permissiongroup.update';
    case PERMISSIONGROUP_DELETE = 'erp.permissiongroup.delete';
    case PERMISSIONGROUP_CREATE_ADMIN = 'erp.permissiongroup.create_admin';

    case PERMISSION_INDEX = 'erp.permission.index';
    case PERMISSION_SHOW = 'erp.permission.show';
    case PERMISSION_CREATE = 'erp.permission.create';
    case PERMISSION_UPDATE = 'erp.permission.update';

    case PERMISSIONGROUPUSER_CREATE = 'erp.permissiongroupuser.create';
    case PERMISSIONGROUPUSER_DELETE = 'erp.permissiongroupuser.delete';
    case PERMISSIONGROUPUSER_SHOW = 'erp.permissiongroupuser.show';

    case NOTIFICATION_WORKFLOW = 'erp.notification.workflow';
    case NOTIFICATION_UPDATE = 'erp.notification.update';
    case NOTIFICATION_DELETE = 'erp.notification.delete';
    case NOTIFICATION_CREATE = 'erp.notification.create';
    case NOTIFICATION_CREATE_MANY = 'erp.notification.many';
    case ACTIVITYLOG_INDEX = 'erp.activitylog.index';
    case ACTIVITYLOG_CREATE = 'erp.activitylog.create';
    case ACTIVITYLOG_UPDATE = 'erp.activitylog.update';
    case ACTIVITYLOG_DELETE = 'erp.activitylog.delete';
    case ACTIVITYLOG_CANCEL = 'erp.activitylog.cancel';
}
