<?php 
namespace Core\CustomInvoiceOut\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\CustomInvoiceOutModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\CustomInvoiceOut\Application\DTOs\IndexCustomInvoiceOutRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface {
    public function __construct(private HookDispatcher $hooks) {}
    function handle(array $data): array
    {
        $dto = IndexCustomInvoiceOutRequest::fromArray($data);
        $list = CustomInvoiceOutModel::select("custom_invoice_outs.*",
            "customers.name as customer_name")
        ->join("customers","customers.id","=","custom_invoice_outs.customer_id")
        ->where('custom_invoice_outs.business_id',$dto->business_id);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'data' => $data,
                    'query' => $list
                ],
                module: 'CustomInvoiceOut'
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        if($dto->keywords) {
            $list = $list->whereAny(['custom_invoice_outs.document_no', 
                'custom_invoice_outs.description'], 'like',
                '%'. $dto->keywords .'%');
        }
        // Event::dispatch(Permission::CUSTOMINVOICEOUT_INDEX->value, [
        //     ...$data
        // ]);
        return $list->orderBy('custom_invoice_outs.id', $dto->order_by)->paginate(15)->toArray();
    }
}