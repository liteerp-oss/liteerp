<?php

namespace Core\Authencation\Domain\Services;

use App\Exceptions\BadException;
use App\Exceptions\UnauthorizedException;
use Core\Authencation\Domain\Entities\Authencation;

interface AuthencationService
{
    public function create(array $data): Authencation | BadException;
    public function createAdmin(array $data): Authencation | BadException;
    public function update(array $data): Authencation | UnauthorizedException | BadException;
    public function profile(array $user): Authencation | UnauthorizedException;
    public function login(array $data) : Authencation | UnauthorizedException;
    public function verify(array $data) : Authencation | UnauthorizedException;
    public function resetPassword(array $data) : Authencation | UnauthorizedException;
    public function findByEmail(array $data) : Authencation | UnauthorizedException;
    public function getByEmail(array $data) : ?Authencation;
    public function getPersonalTokenByEmail(array $data) : ?array;
}