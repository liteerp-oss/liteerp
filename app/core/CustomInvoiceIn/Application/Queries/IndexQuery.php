<?php 
namespace Core\CustomInvoiceIn\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\CustomInvoiceInModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\CustomInvoiceIn\Application\DTOs\IndexCustomInvoiceInRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface {
    function __construct(private HookDispatcher $hooks)
    {
        
    }
    function handle(array $data): array
    {
        $dto = IndexCustomInvoiceInRequest::fromArray($data);
        $index = CustomInvoiceInModel::select("custom_invoice_ins.*",
            "suppliers.unit_name as unit_name")
        ->join("suppliers","suppliers.id","=","custom_invoice_ins.supplier_id")
        ->where('custom_invoice_ins.business_id',$dto->business_id);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'data' => $data,
                    'query' => $index
                ],
                module: 'CustomInvoiceIn'
            )
        );
        $index = $data['query'];
        $data = $data['data'];
        if($dto->keywords) {
            $index = $index->whereAny(['custom_invoice_ins.document_no', 
                'custom_invoice_ins.description'], 'like',
                '%'. $dto->keywords .'%');
        }
        // Event::dispatch(Permission::CUSTOMINVOICEIN_INDEX->value, [
        //     ...$data
        // ]);
        return $index->orderBy("custom_invoice_ins.id",$dto->order_by)->paginate(15)->toArray();
    }
}