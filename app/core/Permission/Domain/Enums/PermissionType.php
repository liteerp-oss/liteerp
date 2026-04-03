<?php 
namespace Core\Permission\Domain\Enums;
enum PermissionType : string {
    case EMPLOYEE = 'Employee';
    case CUSTOMER = 'Customer';
    case CUSTOMER_GROUP = 'Customer group';
    case SUPPLIER = 'Supplier';
    case PRICE_LIST = 'Price list';
    case SHIPPING = 'Shipping';
    case ORDER = 'Order';
    case ORDER_SHIPPING = 'Order shipping';
    case ORDER_ITEM = 'Order item';
    case PRODUCT = 'Product';
    case PRODUCT_CATEGORIES = 'Product categories';
    case INVENTORY = 'Inventory';
    case INVENTORY_ADJUSTMENT = 'Inventory adjustment';
    case INVOICE_IN = 'Invoice in';
    case INVOICE_OUT = 'Invoice out';
    case CUSTOM_INVOICE_OUT = 'Custom invoice out';
    case CUSTOM_INVOICE_IN = 'Custom invoice in';
    case PURCHASE = 'Purchase';
    case PURCHASE_ITEM = 'Purchase item';
    case PURCHASE_TAX = 'Purchase tax';
    case STOCK_IN = 'Stock in';
    case STOCK_OUT = 'Stock out';
    case STOCK_MOVEMENT_IN = 'Stock movement in';
    case STOCK_MOVEMENT_OUT = 'Stock movement out';
    case WAREHOUSE = 'Warehouse';
    case BUSINESS = 'Business';
    case OVERVIEW = 'Overview';
    case EXTENSION = 'Extension';
    case PERMISSION_GROUP = 'Permission group';
    case PERMISSION = 'Permission';
    case PERMISSION_GROUP_USER = 'Permission group user';
    case NOTIFICATION = 'Notification'; 
    case ACTIVITYLOG = 'Activity log'; 
}
