<?php

namespace Core\PermissionGroup\Http\Controllers;

use Core\PermissionGroup\Application\UseCases\ViewRender;
use Illuminate\Http\Request;

class ViewPermissionGroupController
{
    public function index(Request $request, ViewRender $useCase) {
        $entity = $useCase->handle([]);
        return response()->json(['message' => $entity]);
    }
}