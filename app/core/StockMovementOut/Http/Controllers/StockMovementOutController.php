<?php

namespace Core\StockMovementOut\Http\Controllers;

use Core\StockMovementOut\Application\Queries\IndexQuery;
use Core\StockMovementOut\Http\Requests\IndexStockMovementOutRequest;

class StockMovementOutController
{
    public function index(IndexStockMovementOutRequest $request,
        IndexQuery $useCase) {
            $entity = $useCase->handle($request->all());
            return response()->json(['message' => $entity]);
        }
}