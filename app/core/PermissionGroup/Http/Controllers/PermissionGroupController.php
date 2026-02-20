<?php

namespace Core\PermissionGroup\Http\Controllers;

use Core\PermissionGroup\Application\Queries\IndexQuery;
use Core\PermissionGroup\Application\UseCases\CreatePermissionGroup;
use Core\PermissionGroup\Application\UseCases\DeletePermissionGroup;
use Core\PermissionGroup\Application\UseCases\ShowPermissionGroup;
use Core\PermissionGroup\Application\UseCases\UpdatePermissionGroup;
use Core\PermissionGroup\Http\Requests\CreatePermissionGroupRequest;
use Core\PermissionGroup\Http\Requests\DeletePermissionGroupRequest;
use Core\PermissionGroup\Http\Requests\IndexPermissionGroupRequest;
use Core\PermissionGroup\Http\Requests\ShowPermissionGroupRequest;
use Core\PermissionGroup\Http\Requests\UpdatePermissionGroupRequest;

class PermissionGroupController
{
    public function store(CreatePermissionGroupRequest $request, CreatePermissionGroup $useCase)
    {
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }

    public function index(IndexPermissionGroupRequest $request, IndexQuery $query)
    {
        $entity = $query->handle($request->all());
        return response()->json(['message' => $entity]);
    }

    public function show(ShowPermissionGroupRequest $request, ShowPermissionGroup $useCase, string $id)
    {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }

    public function update(UpdatePermissionGroupRequest $request, UpdatePermissionGroup $useCase, string $id)
    {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }

    public function destroy(DeletePermissionGroupRequest $request, DeletePermissionGroup $useCase, string $id)
    {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
}
