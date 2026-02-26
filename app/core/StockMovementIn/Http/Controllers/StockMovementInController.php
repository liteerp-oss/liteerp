<?php

namespace Core\StockMovementIn\Http\Controllers;

use Core\StockMovementIn\Application\UseCases\CreateStockMovementIn;
use Core\StockMovementIn\Application\Queries\IndexQuery;
use Core\StockMovementIn\Application\UseCases\UpdateStockMovementIn;
use Core\StockMovementIn\Http\Requests\CreateStockMovementInRequest as FormRequest;
use Core\StockMovementIn\Http\Requests\IndexStockMovementInRequest;

class StockMovementInController
{
    public function store(FormRequest $request, CreateStockMovementIn $useCase)
    {
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function index(IndexStockMovementInRequest $request, 
        IndexQuery $useCase)
    {
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function update(FormRequest $request, UpdateStockMovementIn $useCase, string $id)
    {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
}
