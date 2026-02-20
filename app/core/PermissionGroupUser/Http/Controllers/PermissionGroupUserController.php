<?php

namespace Core\PermissionGroupUser\Http\Controllers;

use Core\PermissionGroupUser\Application\Queries\IndexQuery;
use Core\PermissionGroupUser\Application\UseCases\CreatePermissionGroupUser;
use Core\PermissionGroupUser\Application\UseCases\DeletePermissionGroupUser;
use Core\PermissionGroupUser\Application\UseCases\ShowPermissionGroupUser;
use Core\PermissionGroupUser\Application\UseCases\UpdatePermissionGroupUser;
use Core\PermissionGroupUser\Http\Requests\CreatePermissionGroupUserRequest;
use Core\PermissionGroupUser\Http\Requests\DeletePermissionGroupUserRequest;
use Core\PermissionGroupUser\Http\Requests\IndexPermissionGroupUserRequest;
use Core\PermissionGroupUser\Http\Requests\ShowPermissionGroupUserRequest;
use Core\PermissionGroupUser\Http\Requests\UpdatePermissionGroupUserRequest;

class PermissionGroupUserController
{
    // public function store(CreatePermissionGroupUserRequest $request, CreatePermissionGroupUser $useCase)
    // {
    //     $entity = $useCase->handle($request->all());
    //     return response()->json(['message' => $entity]);
    // }

    // public function index(IndexPermissionGroupUserRequest $request, IndexQuery $query)
    // {
    //     $entity = $query->handle($request->all());
    //     return response()->json(['message' => $entity]);
    // }

    // public function show(ShowPermissionGroupUserRequest $request, ShowPermissionGroupUser $useCase, string $id)
    // {
    //     $request->merge(['id' => $id]);
    //     $entity = $useCase->handle($request->all());
    //     return response()->json(['message' => $entity]);
    // }

    // public function update(UpdatePermissionGroupUserRequest $request, UpdatePermissionGroupUser $useCase, string $id)
    // {
    //     $request->merge(['id' => $id]);
    //     $entity = $useCase->handle($request->all());
    //     return response()->json(['message' => $entity]);
    // }

    // public function destroy(DeletePermissionGroupUserRequest $request, DeletePermissionGroupUser $useCase, string $id)
    // {
    //     $request->merge(['id' => $id]);
    //     $entity = $useCase->handle($request->all());
    //     return response()->json(['message' => $entity]);
    // }
}
