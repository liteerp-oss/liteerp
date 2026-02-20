<?php

namespace Core\User\Http\Controllers;

use Core\User\Application\Queries\IndexQuery;
use Core\User\Application\UseCases\CreateUser;
use Core\User\Application\UseCases\DeleteUser;
use Core\User\Application\UseCases\UpdateUser;
use Core\User\Http\Requests\CreateUserRequest as CreateFormRequest;
use Core\User\Http\Requests\DeleteUserRequest;
use Core\User\Http\Requests\IndexUserRequest;
use Core\User\Http\Requests\UpdateUserRequest;

class UserController
{
    public function index(IndexQuery $useCase,IndexUserRequest $request)
    {
        return response(['message' => $useCase->handle($request->all())]);
    }
    public function store(CreateFormRequest $request, CreateUser $useCase)
    {
        return response(['message' => $useCase->handle($request->all())]);
    }
    // public function update(UpdateUserRequest $request, 
    //     UpdateUser $useCase, string $id)
    // {
    //     $request->merge(['id' => $id]);
    //     return response(['message' => $useCase->handle($request->all())]);
    // }
    public function destroy(DeleteUserRequest $request, 
        DeleteUser $useCase, string $id)
    {
        $request->merge(['id' => $id]);
        return response(['message' => $useCase->handle($request->all())]);
    }
}
