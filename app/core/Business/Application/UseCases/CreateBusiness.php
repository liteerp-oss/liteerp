<?php

namespace Core\Business\Application\UseCases;

use Core\Business\Application\DTOs\CreateBusinessRequest;
use Core\Business\Domain\Services\BusinessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateBusiness
{
    public function __construct(private BusinessService $service) {}

    public function handle(CreateBusinessRequest $dto)
    {
        DB::beginTransaction();
        $user = Auth::guard('sanctum')->user();
        $business = $this->service->create($dto->toArray());
        Event::dispatch('erp.business.create',[
            'id' => $business->id,
            'business_id' => $business->id,
            'user_id' => $user->id,
            'role_user_id' => $user->id
        ]);
        DB::commit();
        return $business;
    }
}