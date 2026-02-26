<?php

namespace Core\StockMovementIn\Http\Controllers;

use Core\StockMovementIn\Application\UseCases\CreateStockMovementIn;
use Core\StockMovementIn\Application\DTOs\CreateStockMovementInRequest;
use Core\StockMovementIn\Application\DTOs\IndexStockMovementInRequest as DTOsIndexStockMovementInRequest;
use Core\StockMovementIn\Application\Queries\IndexQuery;
use Core\StockMovementIn\Application\UseCases\UpdateStockMovementIn;
use Core\StockMovementIn\Http\Requests\CreateStockMovementInRequest as FormRequest;
use Core\StockMovementIn\Http\Requests\IndexStockMovementInRequest;

class StockMovementInController
{
    public function store(FormRequest $request, CreateStockMovementIn $useCase)
    {
        $dto = CreateStockMovementInRequest::fromArray($request->all());
        $entity = $useCase->handle($dto);
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
        $dto = CreateStockMovementInRequest::fromArray($request->all());
        $entity = $useCase->handle($dto);
        return response()->json(['message' => $entity]);
    }
}
