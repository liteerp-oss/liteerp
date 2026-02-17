<?php

namespace Core\PurchaseItem\Http\Controllers;

use Core\PurchaseItem\Application\UseCases\CreatePurchaseItem;
use Core\PurchaseItem\Application\DTOs\CreatePurchaseItemRequest;
use Core\PurchaseItem\Application\DTOs\DeletePurchaseItemRequest as DTOsDeletePurchaseItemRequest;
use Core\PurchaseItem\Application\Queries\IndexQuery;
use Core\PurchaseItem\Application\UseCases\DeletePurchaseItem;
use Core\PurchaseItem\Application\UseCases\UpdatePurchaseItem;
use Core\PurchaseItem\Http\Requests\CreatePurchaseItemRequest as FormRequest;
use Core\PurchaseItem\Http\Requests\DeletePurchaseItemRequest;
use Core\PurchaseItem\Http\Requests\IndexPurchaseItemRequest;
use Core\PurchaseItem\Http\Requests\UpdatePurchaseItemRequest;

class PurchaseItemController
{
    public function store(FormRequest $request, CreatePurchaseItem $useCase)
    {
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function index(IndexPurchaseItemRequest $request, IndexQuery $useCase)
    {
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function update(
        string $id,
        UpdatePurchaseItemRequest $request,
        UpdatePurchaseItem $useCase
    ) {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function destroy(
        string $id,
        DeletePurchaseItemRequest $request,
        DeletePurchaseItem $useCase
    ) {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
}
