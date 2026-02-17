<?php

namespace Core\StockMovementOut\Http\Controllers;

use Core\StockMovementOut\Application\UseCases\CreateStockMovementOut;
use Core\StockMovementOut\Application\DTOs\CreateStockMovementOutRequest;
use Core\StockMovementOut\Application\Queries\IndexQuery;
use Core\StockMovementOut\Application\UseCases\UpdateStockMovementOut;
use Core\StockMovementOut\Http\Requests\CreateStockMovementOutRequest as FormRequest;
use Core\StockMovementOut\Http\Requests\IndexStockMovementOutRequest;
use Core\StockMovementOut\Http\Requests\UpdateStockMovementOutRequest;

class StockMovementOutController
{
    public function store(FormRequest $request, 
        CreateStockMovementOut $useCase)
    {
        $dto = CreateStockMovementOutRequest::fromArray($request->all());
        $entity = $useCase->handle($dto);
        return response()->json(['message' => $entity]);
    }
    public function update(UpdateStockMovementOutRequest $request,
        UpdateStockMovementOut $useCase,string $id)
    {
        $request->merge(['id' => $id]);
        $dto = UpdateStockMovementOutRequest::fromArray($request->all());
        $entity = $useCase->handle($dto);
        return response()->json(['message' => $entity]);
    }
    public function index(IndexStockMovementOutRequest $request,
        IndexQuery $useCase) {
            $entity = $useCase->handle($request->all());
            return response()->json(['message' => $entity]);
        }
}