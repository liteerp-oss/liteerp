<?php

namespace Core\Product\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Product\Application\DTOs\CreateProductRequest;
use Core\Product\Application\DTOs\DeleteProductRequest;
use Core\Product\Domain\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class DeleteProduct
{
    public function __construct(private ProductService $service,
    private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = DeleteProductRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Product'
            )
        );
        $delete = $this->service->delete($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$delete->toArray()
                ],
                module: 'Product'
            )
        );
        Event::dispatch(Permission::PRODUCT_DELETE->value, [
            ...$data
        ]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.delete', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'product',
            'id' => $delete->id,
            'data' => $delete->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}
