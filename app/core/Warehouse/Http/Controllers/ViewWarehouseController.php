<?php

namespace Core\Warehouse\Http\Controllers;

use App\Http\Controllers\Controller;
use Core\Warehouse\Application\UseCases\ViewWarehouse;
use Illuminate\Http\Request;

class ViewWarehouseController extends Controller {
    public function index(Request $request, ViewWarehouse $useCase) {
        $entity = $useCase->handle([]);
        return response()->json(['message' => $entity]);
    }
}