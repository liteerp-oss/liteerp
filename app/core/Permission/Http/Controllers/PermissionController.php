<?php

namespace Core\Permission\Http\Controllers;

use Core\Permission\Application\UseCases\CreatePermission;
use Core\Permission\Application\UseCases\IndexPermission;
use Core\Permission\Application\UseCases\ShowPermission;
use Core\Permission\Http\Requests\CreatePermissionRequest;
use Core\Permission\Http\Requests\IndexPermissionRequest;
use Core\Permission\Http\Requests\ShowPermissionRequest;

class PermissionController
{
    public function store(CreatePermissionRequest $request, CreatePermission $useCase)
    {
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }

    public function show(ShowPermissionRequest $request, ShowPermission $useCase, string $id)
    {
        $request->merge(['group_id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }

    public function index(IndexPermissionRequest $request, IndexPermission $useCase)
    {
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
}
