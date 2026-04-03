<?php

namespace Core\OrderItem\Infrastructure\Repositories;

use App\Models\OrderItemModel;
use Core\OrderItem\Domain\Repositories\OrderItemRepositoryInterface;
use Core\OrderItem\Domain\Entities\OrderItem;
use Illuminate\Support\Facades\DB;

class EloquentOrderItemRepository implements OrderItemRepositoryInterface
{
    public function create(OrderItem $entity): OrderItem
    {
        $create = OrderItemModel::create($entity->toArray());
        $entity->id = $create['id'];
        return $entity;
    }
    public function show(array $data): array
    {
        return OrderItemModel::select(
            "order_items.*",
            "products.name",
            "products.unit",
            "products.retail_price",
            "products.wholesale_price",
            "products.image"
        )
            ->join(
                "stock_movements_in",
                "stock_movements_in.id",
                "=",
                "order_items.stock_movements_in_id"
            )
            ->join("products", "products.id", "=", "stock_movements_in.product_id")
            ->where('order_items.id', $data['id'])
            ->where('products.business_id', $data['business_id'])
            ->get()->toArray();
    }
    public function update(OrderItem $entity): OrderItem
    {
        OrderItemModel::where('id', $entity->id)
            ->update($entity->toArray());
        return $entity;
    }
    public function delete(OrderItem $entity): OrderItem
    {
        OrderItemModel::where('id', $entity->id)
            ->delete();
        return $entity;
    }
    public function findById(array $data): ?OrderItem
    {
        $row = OrderItemModel::select("order_items.*")
            ->join("orders", "orders.id", "=", "order_items.order_id")
            ->where('order_items.id', $data['id'])
            ->where('orders.business_id', $data['business_id'])
            ->first()?->toArray();
        if (!$row) {
            return null;
        }
        return OrderItem::fromArray($row);
    }
    public function findByProductId(array $data): ?OrderItem
    {
        $row = OrderItemModel::select("order_items.*")
            ->join("orders", "orders.id", "=", "order_items.order_id")
            ->where('order_items.stock_movements_in_id', $data['stock_movements_in_id'])
            ->where('orders.business_id', $data['business_id'])
            ->where('orders.id', $data['order_id'])
            ->first()?->toArray();
        if (!$row) {
            return null;
        }
        return OrderItem::fromArray($row);
    }
    public function index(array $data): array
    {
        return OrderItemModel::select(
            "order_items.*",
            "products.name",
            "products.sku",
            "products.unit",
            "products.image",
            "order_items.price as price",
            "warehouses.name as warehouse",
            "order_items.tax as tax",
            "purchases.id as purchase_id",
            DB::raw("
                ROUND(
                (order_items.price * order_items.buy_quantity) * (order_items.discount / 100)
                ,2)
                as total_discount 
            "),
            DB::raw("ROUND(
                (
                    (order_items.price * order_items.buy_quantity)
                    -
                    (
                        (order_items.price * order_items.buy_quantity) * (order_items.discount / 100)
                    )
                ) 
                * (order_items.tax / 100),2) as total_tax"),
            DB::raw("ROUND(order_items.price * order_items.buy_quantity,2) as subtotal"),
            DB::raw("ROUND(
                (order_items.price * order_items.buy_quantity)
                -
                (order_items.price * order_items.buy_quantity * order_items.discount / 100)
                + 
                (
                    (
                        (order_items.price * order_items.buy_quantity)
                        -
                        (
                            (order_items.price * order_items.buy_quantity
                                * order_items.discount / 100)
                        )
                    ) 
                    * (order_items.tax / 100)
                )
                ,2) as total")
        )
            ->join("stock_movements_in", "stock_movements_in.id", "=", "order_items.stock_movements_in_id")
            ->join("warehouses", "warehouses.id", "=", "stock_movements_in.warehouse_id")
            ->join("products", "products.id", "=", "stock_movements_in.product_id")
            ->join("stock_ins","stock_ins.id","=","stock_movements_in.stock_in_id")
            ->join("invoice_ins","invoice_ins.id","=","stock_ins.invoice_in_id")
            ->join("purchases","purchases.id","=","invoice_ins.purchase_id")
            ->where('products.business_id', $data['business_id'])
            ->where('order_items.order_id', $data['order_id'])
            ->paginate($data['paginate'] ?? 15)->toArray();
    }
    public function indexForStockMovementOut(array $data): array
    {
        return OrderItemModel::select(
            "order_items.*",
            "order_items.id as order_item_id",
            "stock_movements_in.product_id",
            "stock_movements_in.warehouse_id",
            DB::raw("ROUND((order_items.buy_quantity 
                + order_items.gift_quantity
                + order_items.compensation_quantity
                + order_items.conversion_quantity),2) as reserved_qty")
        )
            ->join("orders", "orders.id", "=", "order_items.order_id")
            ->join("stock_movements_in", "stock_movements_in.id", "=", "order_items.stock_movements_in_id")
            ->where('orders.business_id', $data['business_id'])
            ->where('order_items.order_id', $data['order_id'])
            ->limit(300)
            ->get()->toArray();
    }

    public function summary(array $data): ?array
    {
        return OrderItemModel::selectRaw("
            SUM(order_items.buy_quantity) AS total_quantity,
            ROUND(SUM(order_items.price * order_items.buy_quantity),2) AS subtotal,
            ROUND(SUM(
                (order_items.price * order_items.buy_quantity) * (order_items.discount / 100)
            ),2) AS discount,
            ROUND(SUM(
                (
                    (order_items.price * order_items.buy_quantity)
                    -
                    (
                        (order_items.price * order_items.buy_quantity) * (order_items.discount / 100)
                    )
                ) * (order_items.tax / 100)
            ),2) AS tax,
            ROUND(SUM(
                
                    (order_items.price * order_items.buy_quantity)
                    -
                    (
                        (order_items.price * order_items.buy_quantity) 
                            * order_items.discount / 100
                    )
                    +
                    (
                        (
                            (order_items.price * order_items.buy_quantity)
                            -
                            (
                                (order_items.price * order_items.buy_quantity) 
                                    * (order_items.discount / 100)
                            )
                        ) * (order_items.tax / 100)
                    )
                    +
                    (
                        CASE
                            WHEN shippings.shipping_fee_actual > 0
                                THEN shippings.shipping_fee_actual
                            ELSE shippings.shipping_fee_estimated
                        END
                    )
                
            ),2) AS total,
            ROUND( 
            MAX(
                CASE
                    WHEN shippings.shipping_fee_actual > 0
                        THEN shippings.shipping_fee_actual
                    ELSE shippings.shipping_fee_estimated
                END
            )
            ,2) AS shipping_fee
        ")
            ->join("orders", "orders.id", "=", "order_items.order_id")
            ->join("stock_movements_in", "stock_movements_in.id", "=", "order_items.stock_movements_in_id")
            ->join("products", "products.id", "=", "stock_movements_in.product_id")
            ->join("category_product", "category_product.id", "=", "products.category_id")
            ->join("customers", "customers.id", "=", "orders.customer_id")
            ->join("shippings", "shippings.order_id", "=", "orders.id")
            ->where('orders.business_id', $data['business_id'])
            ->where("order_items.order_id", $data['order_id'])
            ->where("order_items.deleted_at", NULL)
            //->groupBy("order_items.id")
            ->first()?->toArray();
    }
    public function cancelByOrderId(array $data): bool
    {
        return OrderItemModel::where('order_id',$data['order_id'])->update([
            'cancelled' => true 
        ]) ? true : false;
    }
}
