<?php

namespace Core\Authencation\Infrastructure\Services;

use App\Exceptions\BadException;
use App\Exceptions\UnauthorizedException;
use Core\Authencation\Domain\Services\AuthencationService;
use Core\Authencation\Domain\Repositories\AuthencationRepositoryInterface;
use Core\Authencation\Domain\Entities\Authencation;
use Illuminate\Support\Facades\Hash;

class AuthencationServiceImpl implements AuthencationService
{
    public function __construct(private AuthencationRepositoryInterface $repo) {}

    public function create(array $entity): Authencation
    {
        $entity = Authencation::fromArray($entity);
        $entity->notVerify();
        return $this->repo->create($entity);
    }
    public function createAdmin(array $entity): Authencation
    {
        $entity = Authencation::fromArray($entity);
        $entity->verifyAt();
        $entity->lastSeen();
        $entity->markAdmin();
        return $this->repo->create($entity);
    }
    public function profile(array $data): Authencation
    {
        $entity = Authencation::fromArray($data);
        $entity->lastSeen();
        return $this->repo->update($entity);
    }
    public function login(array $entity): Authencation| UnauthorizedException {
        $entity = Authencation::fromArray($entity);
        $user = $this->repo->findByEmail($entity->email);
        if(!$user) {
            throw new UnauthorizedException(__("authencation::messages.not_found"));
        }
        if(!Hash::check($entity->password,$user->password)) {
            throw new UnauthorizedException(__("authencation::messages.not_found"));
        }
        $user->token = $this->repo->token($entity);
        return $user;
    }
    public function verify(array $data): Authencation|UnauthorizedException
    {
        $user = $this->repo->findById($data['id']);
        $user->verifyAt();
        return $this->repo->update($user);
    }
    public function resetPassword(array $data): Authencation|UnauthorizedException
    {
        $user = $this->repo->findById($data['id']);
        $user->password = $data['password'];
        return $this->repo->update($user);
    }
    public function update(array $data): Authencation|UnauthorizedException|BadException
    {
        $entity = $this->repo->findByEmail($data['email']);
        if($entity) {
            if($entity->id !== $data['id']) {
                throw new BadException(__("authencation::messages.email_used"));
            } 
        } else {
            $entity = $this->repo->findById($data['id']);
            if(!$entity) {
                throw new UnauthorizedException(__("authencation::messages.not_found"));
            }
        }
        $entity->name = $data['name'] ?? $entity->name;
        $entity->email = $data['email'] ?? $entity->email;
        $entity->phone = $data['phone'] ?? $entity->phone;
        $entity->bio = $data['bio'] ?? $entity->bio;
        $entity->avatar = $data['avatar'] ?? $entity->avatar;
        $entity->password = $data['password'] ?? $entity->password;
        $entity->lang = $data['lang'] ?? $entity->lang;
        return $this->repo->update($entity);
    }
    public function findByEmail(array $data): Authencation|UnauthorizedException
    {
        return $this->repo->findByEmail($data['email']) ?? throw new BadException(__("authencation::messages.not_found"));
    }
    public function getByEmail(array $data): ?Authencation
    {
        return $this->repo->findByEmail($data['email']);
    }
    public function getPersonalTokenByEmail(array $data): ?array
    {
        $entity = $this->repo->findByEmail($data['email']);
        if(!$entity) {
            return null;
        }
        return [
            'token' => $this->repo->token($entity),
            'account' => $entity
        ];
    }
}