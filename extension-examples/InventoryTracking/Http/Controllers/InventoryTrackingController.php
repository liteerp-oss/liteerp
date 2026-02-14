<?php

namespace Extensions\InventoryTracking\Http\Controllers;

use App\Http\Controllers\Controller;
use Extensions\InventoryTracking\Models\InventoryTrackingModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryTrackingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'message' => InventoryTrackingModel::query()->first(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'min' => 'required|numeric|min:0',
        ]);
        $validated['business_id'] = $request->get('business_id');
        $tracking = InventoryTrackingModel::updateOrCreate([],$validated);

        return response()->json([
            'message' => $tracking,
        ]);
    }
}
