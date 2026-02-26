<?php

namespace Core\CustomerGroup\Http\Controllers;

use Core\CustomerGroup\Application\UseCases\ViewCustomerGroup;
use Illuminate\Http\Request;

class ViewController
{
    public function index(Request $request, ViewCustomerGroup $useCase) {
        $entity = $useCase->handle([]);
        return response()->json(['message' => $entity]);
    }
}