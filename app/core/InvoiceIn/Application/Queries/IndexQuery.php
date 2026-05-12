<?php

namespace Core\InvoiceIn\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\InvoiceInModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\InvoiceIn\Application\DTOs\IndexInvoiceInRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    function __construct(private HookDispatcher $hooks) {}
    function handle(array $data): array
    {
        $dto = IndexInvoiceInRequest::fromArray($data);
        $list = InvoiceInModel::select(
            "invoice_ins.*",
            "suppliers.unit_name as unit_name",
            "suppliers.email as email",
            "suppliers.phone as phone",
            "suppliers.address as address",
            "suppliers.tax_code as tax_code",
            "suppliers.bank_name as bank_name",
            "suppliers.bank_account as bank_account",
            "suppliers.website as website",
            "suppliers.note as note",
            "purchases.status as purchase_status"
        )
            ->join("purchases", "purchases.id", "=", "invoice_ins.purchase_id")
            ->join("suppliers", "suppliers.id", "=", "purchases.supplier_id")
            ->where('invoice_ins.business_id', $dto->business_id);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'query' => $list,
                    'data' => $data
                ],
                module: 'InvoiceIn'
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        if ($dto->keywords) {
            $list = $list->whereAny(['invoice_ins.document_no',
            'suppliers.unit_name',
            'suppliers.email'], 'like', '%' . $dto->keywords . '%');
        }
        // Event::dispatch(Permission::INVOICEIN_INDEX->value, [
        //     ...$data
        // ]);
        return $list->orderBy("invoice_ins.id", $dto->order_by)->paginate(15)->toArray();
    }
}
