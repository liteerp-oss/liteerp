<?php

namespace Core\Purchase\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\PurchaseModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Purchase\Application\DTOs\IndexPurchaseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    function __construct(private HookDispatcher $hooks) {}
    function handle(array $data): array
    {
        $dto = IndexPurchaseRequest::fromArray($data);
        $list = PurchaseModel::select(
            "purchases.*",
            "suppliers.unit_name as supplier_name",
            "created_users.name as created_name",
            "approved_users.name as approved_name",
            DB::raw("SUM(purchase_items.buy_quantity) as buy_quantity"),
            DB::raw("SUM(purchase_items.gift_quantity) as gift_quantity"),
            DB::raw("SUM(purchase_items.compensation_quantity) as compensation_quantity"),
            DB::raw("SUM(purchase_items.conversion_quantity) as conversion_quantity"),
            DB::raw("ROUND(SUM(
            purchase_items.unit_cost * purchase_items.tax / 100
            ),2) as tax"),
             DB::raw("ROUND(SUM(
            purchase_items.unit_cost
            ),2) as subtotal"),
             DB::raw("ROUND(SUM(
            purchase_items.unit_cost + (purchase_items.unit_cost * purchase_items.tax / 100)
            ) + purchases.shipping_fee,2) as total"),
            DB::raw("ROUND(SUM(purchase_items.unit_cost),2) as unit_cost")
        )
            ->join("suppliers", "suppliers.id", "=", "purchases.supplier_id")
            ->join("users as created_users", "created_users.id", "=", "purchases.created_by")
            ->leftJoin("users as approved_users", "approved_users.id", "=", "purchases.approved_by")
            ->leftJoin("purchase_items", "purchase_items.purchase_id", "=", "purchases.id")
            ->where('purchases.business_id', $dto->business_id)
            ->orderBy("purchases.id", $dto->order_by)
            ->groupBy("purchases.id");
        /**
         * Search for inventory adjustment 
         * Only get for purchase completed
         */
        if($dto->isCompleted) {
            $list = $list->leftJoin("invoice_ins",
                "invoice_ins.purchase_id","=","purchases.id")
                ->leftJoin("stock_ins","stock_ins.invoice_in_id","=","invoice_ins.id")
                ->where('stock_ins.status','received');
        }
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'query' => $list,
                    'data' => $data
                ],
                module: 'Purchase'
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        if ($dto->keywords) {
            $list->whereAny(['suppliers.unit_name','created_users.name','purchases.id'], 'like', '%' . $dto->keywords . '%');
        }
        // Event::dispatch(Permission::PURCHASE_INDEX->value, [
        //     ...$data,
        //     ...$dto->toArray(),
        // ]);
        return $list->paginate(15)->toArray();
    }
}
