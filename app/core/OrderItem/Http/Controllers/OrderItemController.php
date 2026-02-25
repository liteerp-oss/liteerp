<?php

namespace Core\OrderItem\Http\Controllers;

use Core\OrderItem\Application\Queries\IndexQuery;
use Core\OrderItem\Application\UseCases\CreateOrderItem;
use Core\OrderItem\Application\UseCases\DeleteOrderItem;
use Core\OrderItem\Application\UseCases\GetSummaryOrderItemForDisplay;
use Core\OrderItem\Application\UseCases\UpdateOrderItem;
use Core\OrderItem\Http\Requests\CreateOrderItemRequest as FormRequest;
use Core\OrderItem\Http\Requests\DeleteOrderItemRequest;
use Core\OrderItem\Http\Requests\IndexOrderItemRequest;
use Core\OrderItem\Http\Requests\UpdateOrderItemRequest;

class OrderItemController
{
    public function store(FormRequest $request, CreateOrderItem $useCase)
    {
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function index(
        IndexOrderItemRequest $request,
        IndexQuery $useCase,
        GetSummaryOrderItemForDisplay $summary
    ) {
        if($request->input('summary')) {
            $entity = $summary->handle($request->toArray());
        } else {
            $entity = $useCase->handle($request->toArray());
        }
        
        return response()->json(['message' => $entity]);
    }
    public function update(
        UpdateOrderItemRequest $request,
        UpdateOrderItem $useCase,
        string $id
    ) {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function destroy(
        DeleteOrderItemRequest $request,
        DeleteOrderItem $useCase,
        string $id
    ) {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
}
