<?php

namespace Core\InventoryAdjustment\Http\Controllers;

use Core\InventoryAdjustment\Application\UseCases\ViewRender;
use Illuminate\Http\Request;

class ViewInventoryAdjustmentController
{
    public function index(Request $request, ViewRender $useCase) {
        $entity = $useCase->handle([]);
        return response()->json(['message' => $entity]);
    }
}