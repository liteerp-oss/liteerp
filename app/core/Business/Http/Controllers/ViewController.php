<?php

namespace Core\Business\Http\Controllers;

use Core\Business\Application\UseCases\ViewRender;
use Illuminate\Http\Request;

class ViewController
{
    public function index(Request $request, ViewRender $useCase) {
        $entity = $useCase->handle([]);
        return response()->json(['message' => $entity]);
    }
}