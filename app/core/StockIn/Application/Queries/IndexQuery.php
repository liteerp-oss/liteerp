<?php 
namespace Core\StockIn\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\StockInModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\StockIn\Application\DTOs\IndexStockInRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface {
    function __construct(private HookDispatcher $hooks)
    {
        
    }
    function handle(array $data): array
    {
        $dto = IndexStockInRequest::fromArray($data);
        $list = StockInModel::select(
            "stock_ins.*",
            DB::raw('count(stock_ins.id) as total_product'),
            "users.name as approved_name",
            "suppliers.unit_name as supplier_name",
            "invoice_ins.document_no as document_no",
            "purchases.id as purchase_id",
            "invoice_ins.due_date as due_date",
            "purchases.status as purchase_status"
        )
            ->join("invoice_ins", "invoice_ins.id", "=", "stock_ins.invoice_in_id")
            ->join("purchases", "purchases.id", "=", "invoice_ins.purchase_id")
            ->join("purchase_items", "purchase_items.purchase_id", "=", "purchases.id")
            ->leftJoin("users", "users.id", "=", "stock_ins.approved_by")
            ->join("products", "products.id", "=", "purchase_items.product_id")
            ->join("suppliers", "suppliers.id", "=", "purchases.supplier_id")
            ->groupBy("stock_ins.id")
            ->where('stock_ins.business_id', $dto->business_id);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'data' => $data,
                    'query' => $list
                ],
                module: 'StockIn'
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        if ($dto->keywords) {
            $list = $list->whereAny(['invoice_ins.document_no',
                'users.name',
                'suppliers.unit_name'], 'like', '%' . $dto->keywords . '%');
        }
        // Event::dispatch(Permission::STOCKIN_INDEX->value, [
        //     ...$data
        // ]);
        return $list->orderBy("stock_ins.id",$dto->order_by)->paginate(15)->toArray();
    }
}