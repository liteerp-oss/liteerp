<?php 
namespace Core\Order\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\OrderModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Order\Application\DTOs\IndexOrderRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface {
    function __construct(
        private HookDispatcher $hooks)
    {
        
    }
    function handle(array $data): array
    {
        $dto = IndexOrderRequest::fromArray($data);
        $list = OrderModel::select(
            "orders.*",
            "customers.name as customer_name",
            "customers.address as customer_address",
            "created_user.name as created_name",
            "approved_user.name as approved_name",
            DB::raw("SUM(order_items.buy_quantity) as total_buy"),
            DB::raw("SUM(order_items.gift_quantity) as total_gift"),
            DB::raw("SUM(order_items.compensation_quantity) as total_comp"),
            DB::raw("SUM(order_items.conversion_quantity) as total_convert"),
            DB::raw("SUM(order_items.discount) as total_discount"),
            DB::raw("COUNT(order_items.id) as total_product")
        )->join("customers", "customers.id", "=", "orders.customer_id")
            ->join("users as created_user", "created_user.id", "=", "orders.created_by")
            ->leftJoin("users as approved_user", "approved_user.id", "=", "orders.approved_by")
            ->leftJoin("order_items", "order_items.order_id", "=", "orders.id")
            ->groupBy("orders.id")
            ->where('orders.business_id',$dto->business_id)
            ->where('order_items.deleted_at',NULL);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'data' => [
                        ...$data,
                        ...$dto->toArray()
                    ],
                    'query' => $list
                ],
                module: 'Order'
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        if ($dto->keywords) {
            $list = $list->whereAny(['orders.order_no',
            'customers.name',
            'customers.email'], 'like', '%' . $dto->keywords . '%');        
        }
        // Event::dispatch(Permission::ORDER_INDEX->value, [
        //     ...$data
        // ]);
        return $list->orderBy("orders.id", $dto->order_by)
            ->paginate($dto->paginate)->toArray();
    }
}