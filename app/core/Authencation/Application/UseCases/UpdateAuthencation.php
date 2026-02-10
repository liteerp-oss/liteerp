<?php

namespace Core\Authencation\Application\UseCases;

use App\Exceptions\BadException;
use App\Exceptions\UnauthorizedException;
use Core\Authencation\Application\DTOs\UpdateAuthencationRequest;
use Core\Authencation\Domain\Services\AuthencationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdateAuthencation
{
    public function __construct(private AuthencationService $service) {}

    public function handle(UpdateAuthencationRequest $dto)
    {
        $user = Auth::guard('sanctum')->user();
        if(!$user) {
            throw new UnauthorizedException(__("authencation::messages.not_logged"));
        }
        if($dto->password && !$dto->new_password) {
            throw new BadException(__("authencation::messages.new_password_is_required"));
        }
        if(!$dto->password && $dto->new_password) {
            throw new BadException(__("authencation::messages.password_is_required"));
        }
        if($dto->password && !Hash::check($dto->password,$user->password)) {
            throw new BadException(__("authencation::messages.password_is_not_correctly"));
        }
        if($dto->new_password) {
            $dto->password = Hash::make($dto->new_password);
        }
        $dto->id = $user->id;
        return $this->service->update($dto->toArray());
    }
}