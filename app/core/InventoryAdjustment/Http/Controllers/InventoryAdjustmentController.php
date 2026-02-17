<?php

namespace Core\InventoryAdjustment\Http\Controllers;

use Core\InventoryAdjustment\Application\Queries\IndexQuery;
use Core\InventoryAdjustment\Application\UseCases\CreateInventoryAdjustment;
use Core\InventoryAdjustment\Http\Requests\CreateInventoryAdjustmentRequest as FormRequest;
use Core\InventoryAdjustment\Http\Requests\IndexInventoryAdjustmentRequest;

class InventoryAdjustmentController
{
    public function store(FormRequest $request, CreateInventoryAdjustment $useCase)
    {
        $data = $request->all();
        $entity = $useCase->handle($data);
        return response()->json(['message' => $entity]);
    }
    public function index(IndexInventoryAdjustmentRequest $request, 
    IndexQuery $useCase)
    {
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
}