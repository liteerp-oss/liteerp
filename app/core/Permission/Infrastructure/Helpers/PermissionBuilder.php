<?php

namespace Core\Permission\Infrastructure\Helpers;

use App\Supports\Permissions\Enums\Permission;
use Core\Permission\Domain\Enums\PermissionType;

class PermissionBuilder
{
    private array $data = [];

    private function add(PermissionType $type, array $permissions): self
    {
        $this->data[$type->value] = array_map(
            fn(Permission $permission) => $permission->value,
            $permissions
        );;

        return $this;
    }

    public function addEmployee(): self
    {
        return $this->add(PermissionType::EMPLOYEE, [
            Permission::USER_INDEX,
            Permission::USER_SHOW,
            Permission::USER_CREATE,
            Permission::USER_UPDATE,
            Permission::USER_DELETE,
        ]);
    }

    public function addCustomer(): self
    {
        return $this->add(PermissionType::CUSTOMER, [
            Permission::CUSTOMER_INDEX,
            Permission::CUSTOMER_SHOW,
            Permission::CUSTOMER_CREATE,
            Permission::CUSTOMER_UPDATE,
            Permission::CUSTOMER_DELETE,
            Permission::CUSTOMER_CREATORDEALERSHIPPING,
        ]);
    }

    public function addCustomerGroup(): self
    {
        return $this->add(PermissionType::CUSTOMER_GROUP, [
            Permission::CUSTOMERGROUP_INDEX,
            Permission::CUSTOMERGROUP_SHOW,
            Permission::CUSTOMERGROUP_CREATE,
            Permission::CUSTOMERGROUP_UPDATE,
            Permission::CUSTOMERGROUP_DELETE,
        ]);
    }

    public function addSupplier(): self
    {
        return $this->add(PermissionType::SUPPLIER, [
            Permission::SUPPLIER_INDEX,
            Permission::SUPPLIER_SHOW,
            Permission::SUPPLIER_CREATE,
            Permission::SUPPLIER_UPDATE,
            Permission::SUPPLIER_DELETE,
        ]);
    }

    public function addPriceList(): self
    {
        return $this->add(PermissionType::PRICE_LIST, [
            Permission::PRICELIST_INDEX,
            Permission::PRICELIST_SHOW,
            Permission::PRICELIST_CREATE,
            Permission::PRICELIST_UPDATE,
            Permission::PRICELIST_DELETE,
        ]);
    }

    public function addShipping(): self
    {
        return $this->add(PermissionType::SHIPPING, [
            Permission::SHIPPING_INDEX,
            Permission::SHIPPING_SHOW,
            Permission::SHIPPING_CREATE,
            Permission::SHIPPING_UPDATE,
            Permission::SHIPPING_DELETE,
        ]);
    }

    public function addOrder(): self
    {
        return $this->add(PermissionType::ORDER, [
            Permission::ORDER_INDEX,
            Permission::ORDER_SHOW,
            Permission::ORDER_CREATE,
            Permission::ORDER_UPDATE,
            Permission::ORDER_DELETE,
            Permission::ORDER_APPROVED,
            Permission::ORDER_CANCELLED,
        ]);
    }

    public function addOrderShipping(): self
    {
        return $this->add(PermissionType::ORDER_SHIPPING, [
            Permission::ORDERSHIPPING_INDEX,
            Permission::ORDERSHIPPING_SHOW,
            Permission::ORDERSHIPPING_CREATE,
            Permission::ORDERSHIPPING_UPDATE,
            Permission::ORDERSHIPPING_DELETE,
        ]);
    }

    public function addOrderItem(): self
    {
        return $this->add(PermissionType::ORDER_ITEM, [
            Permission::ORDERITEM_INDEX,
            Permission::ORDERITEM_SHOW,
            Permission::ORDERITEM_CREATE,
            Permission::ORDERITEM_UPDATE,
            Permission::ORDERITEM_DELETE,
            Permission::ORDERITEM_COMPLETED,
            Permission::ORDERITEM_SUMMARY,
            Permission::ORDERITEM_CANCELLED,
        ]);
    }

    public function addProduct(): self
    {
        return $this->add(PermissionType::PRODUCT, [
            Permission::PRODUCT_INDEX,
            Permission::PRODUCT_SHOW,
            Permission::PRODUCT_CREATE,
            Permission::PRODUCT_UPDATE,
            Permission::PRODUCT_DELETE,
        ]);
    }

    public function addProductCategories(): self
    {
        return $this->add(PermissionType::PRODUCT_CATEGORIES, [
            Permission::CATEGORYPRODUCT_INDEX,
            Permission::CATEGORYPRODUCT_SHOW,
            Permission::CATEGORYPRODUCT_CREATE,
            Permission::CATEGORYPRODUCT_UPDATE,
            Permission::CATEGORYPRODUCT_DELETE,
        ]);
    }

    public function addInventory(): self
    {
        return $this->add(PermissionType::INVENTORY, [
            Permission::INVENTORY_INDEX,
            Permission::INVENTORY_SHOW
        ]);
    }

    public function addInventoryAdjustment(): self
    {
        return $this->add(PermissionType::INVENTORY_ADJUSTMENT, [
            Permission::INVENTORYADJUSTMENT_CREATE,
            Permission::INVENTORYADJUSTMENT_INDEX,
        ]);
    }

    public function addInvoiceIn(): self
    {
        return $this->add(PermissionType::INVOICE_IN, [
            Permission::INVOICEIN_INDEX,
            Permission::INVOICEIN_SHOW,
            Permission::INVOICEIN_CREATE,
            Permission::INVOICEIN_UPDATE,
            Permission::INVOICEIN_DELETE,
            Permission::INVOICEIN_CANCELLED,
            Permission::INVOICEIN_APPROVED,
        ]);
    }

    public function addInvoiceOut(): self
    {
        return $this->add(PermissionType::INVOICE_OUT, [
            Permission::INVOICEOUT_INDEX,
            Permission::INVOICEOUT_SHOW,
            Permission::INVOICEOUT_CREATE,
            Permission::INVOICEOUT_UPDATE,
            Permission::INVOICEOUT_DELETE,
            Permission::INVOICEOUT_UNAPPROVED,
            Permission::INVOICEOUT_APPROVED,
        ]);
    }

    public function addCustomInvoiceOut(): self
    {
        return $this->add(PermissionType::CUSTOM_INVOICE_OUT, [
            Permission::CUSTOMINVOICEOUT_CREATE,
            Permission::CUSTOMINVOICEOUT_UPDATE,
            Permission::CUSTOMINVOICEOUT_DELETE,
            Permission::CUSTOMINVOICEOUT_INDEX,
        ]);
    }

    public function addCustomInvoiceIn(): self
    {
        return $this->add(PermissionType::CUSTOM_INVOICE_IN, [
            Permission::CUSTOMINVOICEIN_CREATE,
            Permission::CUSTOMINVOICEIN_UPDATE,
            Permission::CUSTOMINVOICEIN_DELETE,
            Permission::CUSTOMINVOICEIN_INDEX,
        ]);
    }

    public function addPurchase(): self
    {
        return $this->add(PermissionType::PURCHASE, [
            Permission::PURCHASE_INDEX,
            Permission::PURCHASE_SHOW,
            Permission::PURCHASE_CREATE,
            Permission::PURCHASE_UPDATE,
            Permission::PURCHASE_DELETE,
            Permission::PURCHASE_CANCELLED,
            Permission::PURCHASE_APPROVED,
            Permission::PURCHASE_REQUESTED,
        ]);
    }

    public function addPurchaseItem(): self
    {
        return $this->add(PermissionType::PURCHASE_ITEM, [
            Permission::PURCHASEITEM_INDEX,
            Permission::PURCHASEITEM_SHOW,
            Permission::PURCHASEITEM_CREATE,
            Permission::PURCHASEITEM_UPDATE,
            Permission::PURCHASEITEM_DELETE,
        ]);
    }

    public function addPurchaseTax(): self
    {
        return $this->add(PermissionType::PURCHASE_TAX, [
            Permission::PURCHASETAX_CREATE,
        ]);
    }

    public function addStockIn(): self
    {
        return $this->add(PermissionType::STOCK_IN, [
            Permission::STOCKIN_INDEX,
            Permission::STOCKIN_SHOW,
            Permission::STOCKIN_CREATE,
            Permission::STOCKIN_UPDATE,
            Permission::STOCKIN_DELETE,
            Permission::STOCKIN_CANCELLED,
            Permission::STOCKIN_RECEIVED,
        ]);
    }

    public function addStockOut(): self
    {
        return $this->add(PermissionType::STOCK_OUT, [
            Permission::STOCKOUT_INDEX,
            Permission::STOCKOUT_SHOW,
            Permission::STOCKOUT_CREATE,
            Permission::STOCKOUT_UPDATE,
            Permission::STOCKOUT_DELETE,
            Permission::STOCKOUT_COMPLETED,
            Permission::STOCKOUT_SHIPPED,
            Permission::STOCKOUT_CANCELLED
        ]);
    }

    public function addStockMovementIn(): self
    {
        return $this->add(PermissionType::STOCK_MOVEMENT_IN, [
            Permission::STOCKMOVEMENTIN_INDEX,
            Permission::STOCKMOVEMENTIN_SHOW,
            Permission::STOCKMOVEMENTIN_CREATE,
            Permission::STOCKMOVEMENTIN_UPDATE,
            Permission::STOCKMOVEMENTIN_DELETE,
            Permission::STOCKMOVEMENTIN_COMPLETED,
        ]);
    }

    public function addStockMovementOut(): self
    {
        return $this->add(PermissionType::STOCK_MOVEMENT_OUT, [
            Permission::STOCKMOVEMENTOUT_INDEX,
            Permission::STOCKMOVEMENTOUT_SHOW,
            Permission::STOCKMOVEMENTOUT_CREATE,
            Permission::STOCKMOVEMENTOUT_UPDATE,
            Permission::STOCKMOVEMENTOUT_DELETE,
        ]);
    }

    public function addWarehouse(): self
    {
        return $this->add(PermissionType::WAREHOUSE, [
            Permission::WAREHOUSE_INDEX,
            Permission::WAREHOUSE_SHOW,
            Permission::WAREHOUSE_CREATE,
            Permission::WAREHOUSE_UPDATE,
            Permission::WAREHOUSE_DELETE,
        ]);
    }

    public function addBusiness(): self
    {
        return $this->add(PermissionType::BUSINESS, [
            Permission::BUSINESS_UPDATE,
        ]);
    }

    public function addOverview(): self
    {
        return $this->add(PermissionType::OVERVIEW, [
            Permission::OVERVIEW_INDEX,
        ]);
    }

    public function addExtension(): self
    {
        return $this->add(PermissionType::EXTENSION, [
            Permission::EXTENSION_INDEX,
            Permission::EXTENSION_CREATE,
            Permission::EXTENSION_UPDATE,
            Permission::EXTENSION_DELETE,
        ]);
    }

    public function addPermissionGroup(): self
    {
        return $this->add(PermissionType::PERMISSION_GROUP, [
            Permission::PERMISSIONGROUP_INDEX,
            Permission::PERMISSIONGROUP_SHOW,
            Permission::PERMISSIONGROUP_CREATE,
            Permission::PERMISSIONGROUP_UPDATE,
            Permission::PERMISSIONGROUP_DELETE,
        ]);
    }

    public function addPermission(): self
    {
        return $this->add(PermissionType::PERMISSION, [
            Permission::PERMISSION_INDEX,
            Permission::PERMISSION_SHOW,
            Permission::PERMISSION_CREATE,
            Permission::PERMISSION_UPDATE,
        ]);
    }

    public function addPermissionGroupUser(): self
    {
        return $this->add(PermissionType::PERMISSION_GROUP_USER, [
            Permission::PERMISSIONGROUPUSER_CREATE,
            Permission::PERMISSIONGROUPUSER_DELETE,
        ]);
    }

    public function addActivityLog(): self
    {
        return $this->add(PermissionType::ACTIVITYLOG, [
            Permission::ACTIVITYLOG_INDEX
        ]);
    }

    public function addCustom(string $type, array $permissions): self
    {
        $this->data[$type] = [
            ...$this->data[$type] ?? $permissions
        ];
        return $this;
    }
    public function buildByPass(): array
    {
        return [
            Permission::BUSINESS_CREATE->value,
            Permission::NOTIFICATION_CREATE->value,
            Permission::NOTIFICATION_CREATE_MANY->value,
            Permission::AUTHENCATION_CREATE_ADMIN->value,
            Permission::ACTIVITYLOG_CREATE->value,
            Permission::ACTIVITYLOG_UPDATE->value,
            Permission::ACTIVITYLOG_DELETE->value,
        ];
    }
    public function addFull(): self
    {
        $this->addBusiness()
            ->addOverview()
            ->addEmployee()
            ->addCustomer()
            ->addCustomerGroup()
            ->addPriceList()
            ->addProduct()
            ->addPurchase()
            ->addPurchaseItem()
            ->addShipping()
            ->addOrderShipping()
            ->addCustomInvoiceIn()
            ->addStockIn()
            ->addOrder()
            ->addOrderItem()
            ->addPurchaseTax()
            ->addStockOut()
            ->addInventory()
            ->addInventoryAdjustment()
            ->addCustomInvoiceOut()
            ->addPermission()
            ->addPermissionGroup()
            ->addPermissionGroupUser()
            ->addExtension()
            ->addWarehouse()
            ->addStockMovementIn()
            ->addStockMovementOut()
            ->addProductCategories()
            ->addSupplier()
            ->addInvoiceIn()
            ->addInvoiceOut()
            ->addActivityLog();
        return $this;
    }
    public function addBasic(): self
    {
        return $this;
    }
    public function build(): array
    {
        return $this->data;
    }
    public function buildListItem(): array
    {
        $listItem = [];
        foreach (array_keys($this->data) as $key => $value) {
            $listItem = [
                ...$listItem,
                ...$this->data[$value]
            ];
        }
        return $listItem;
    }
}
