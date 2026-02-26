<?php

namespace Core\Inventory\Http\Controllers;

use Core\Inventory\Application\UseCases\ViewRender;
use Illuminate\Http\Request;

class ViewInventoryController
{
    public function index(Request $request, ViewRender $useCase) {
        $entity = $useCase->handle([]);
        return response()->json(['message' => $entity]);
    }
}