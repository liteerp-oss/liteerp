<?php 
namespace Core\StockMovementIn\Application\Queries;

use App\Contracts\Queries\QueryInterface;
use App\Models\StockMovementInModel;
use Core\StockMovementIn\Application\DTOs\IndexStockMovementInRequest;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface {
    public function __construct(private HookDispatcher $hooks){}
    public function handle(array $data): array {
        $dto = IndexStockMovementInRequest::fromArray($data);
        $rows = StockMovementInModel::select("stock_movements_in.*",
            "suppliers.unit_name as unit_name",
            "products.name as name",
            "products.unit as unit",
            "products.sku as sku",
            "category_product.name as category",
            "warehouses.name as warehouse")
        ->join("stock_ins","stock_ins.id"
            ,"=","stock_movements_in.stock_in_id")
        ->join("invoice_ins","invoice_ins.id"
            ,"=","stock_ins.invoice_in_id")
        ->join("products","products.id"
            ,"=","stock_movements_in.product_id")
        ->join("warehouses","warehouses.id"
            ,"=","stock_movements_in.warehouse_id")
        ->join("purchases","purchases.id"
            ,"=","invoice_ins.purchase_id")
        ->join("suppliers","suppliers.id"
            ,"=","purchases.supplier_id")
        ->join("category_product","category_product.id"
            ,"=","products.category_id")
        ->where('invoice_ins.business_id',$dto->business_id)
        ->where('stock_movements_in.stock_in_id',$dto->stock_in_id);
        if (!empty($dto->keywords)) {
            $rows->whereAny(['products.name','products.sku','category_product.name'], 
                'like', '%' . $dto->keywords . '%');
        } 
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
                    'query' => $rows
                ],
                module: 'StockMovementIn'
            )
        );
        $rows = $data['query'];
        $data = $data['data'];
        Event::dispatch("erp.stockmovementin.index", [
            ...$data
        ]);
        return $rows->orderBy('stock_movements_in.id', $dto->order_by)->paginate(15)->toArray();
    }
}