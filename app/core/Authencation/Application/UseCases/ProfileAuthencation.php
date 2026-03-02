<?php

namespace Core\Authencation\Application\UseCases;

use App\Exceptions\UnauthorizedException;
use Core\Authencation\Domain\Services\AuthencationService;
use Illuminate\Support\Facades\Auth;

class ProfileAuthencation
{
    public function __construct(private AuthencationService $service) {}

    public function handle()
    {
        $user = Auth::guard('sanctum')->user();
        if(!$user) {
            throw new UnauthorizedException(__("authencation::messages.not_logged"));
        }
        return $this->service->profile([
            'email'=> $user->email,
            'password'=> $user->password,
            'name'=> $user->name,
            'id'=> $user->id,
            'email_verified_at'=> $user->email_verified_at,
            'bio'=> $user->bio,
            'avatar'=> $user->avatar,
            'phone'=> $user->phone,
            'last_seen'=> $user->last_seen,
            'role' => $user->role,
            'lang' => $user->lang
        ]);
    }
}