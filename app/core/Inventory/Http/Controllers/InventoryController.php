<?php

namespace Core\Inventory\Http\Controllers;

use Core\Inventory\Application\UseCases\CreateInventory;
use Core\Inventory\Application\Queries\IndexQuery;
use Core\Inventory\Http\Requests\CreateInventoryRequest as FormRequest;
use Core\Inventory\Http\Requests\IndexInventoryRequest;

class InventoryController
{
    public function store(FormRequest $request, CreateInventory $useCase)
    {
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function index(
        IndexQuery $indexQuery,
        IndexInventoryRequest $request
    ) {
        $entity = $indexQuery->handle($request->all());
        return response()->json(['message' => $entity]);
    }
}
