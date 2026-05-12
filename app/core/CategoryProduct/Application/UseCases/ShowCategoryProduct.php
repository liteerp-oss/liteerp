<?php

namespace Core\CategoryProduct\Application\UseCases;

use Core\CategoryProduct\Domain\Services\CategoryProductService;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\CategoryProduct\Application\DTOs\ShowCategoryProductRequest;
use Illuminate\Support\Facades\Event;

class ShowCategoryProduct
{
    public function __construct(private CategoryProductService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        $dto = ShowCategoryProductRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'CategoryProduct'
            )
        );
        $result = $this->service->show($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$result->toArray()
                ],
                module: 'CategoryProduct'
            )
        );
        // Event::dispatch(Permission::CATEGORYPRODUCT_SHOW->value,[
        //     ...$data
        // ]);
        return $data;
    }
}