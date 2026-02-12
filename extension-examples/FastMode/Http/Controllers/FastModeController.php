<?php 
namespace Extensions\FastMode\Http\Controllers;

use Extensions\FastMode\Models\FastModeModel;
use Illuminate\Http\Request;

class FastModeController {
    public function index(Request $request) {
        return response()->json([
            'message' => FastModeModel::first()
        ]);
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'status' => 'required|in:paid,partial_payment,pending'
        ]);
        $update = FastModeModel::updateOrCreate([],[
            'status'=> $validated['status'],
        ]);
        return response()->json(['message'=> $update]);
    }
}