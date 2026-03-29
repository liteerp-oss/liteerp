<?php

namespace Core\PurchaseItem\Http\Controllers;

use Core\PurchaseItem\Application\UseCases\ViewPurchase;
use Illuminate\Http\Request;

class ViewPurchaseItemController
{
    public function index(Request $request, ViewPurchase $useCase){
        $entity = $useCase->handle([]);
        return response()->json(['message' => $entity]);
    }
}