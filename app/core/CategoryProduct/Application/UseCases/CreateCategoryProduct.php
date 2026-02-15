<?php

namespace Core\CategoryProduct\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\CategoryProduct\Application\DTOs\CreateCategoryProductRequest;
use Core\CategoryProduct\Domain\Services\CategoryProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateCategoryProduct
{
    public function __construct(
        private CategoryProductService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: $data,
                module: 'CategoryProduct'
            )
        );
        $dto = CreateCategoryProductRequest::fromArray($data);
        
        $create = $this->service->create($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$create->toArray(),
                    ...$data
                ],
                module: 'CategoryProduct'
            )
        );
        Event::dispatch("erp.categoryproduct.create", [
            ...$create->toArray(),
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'category_id' => $create->id
        ]);
        DB::commit();
        return $create;
    }
}
