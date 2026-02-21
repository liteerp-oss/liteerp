<?php 
namespace Extensions\FastMode\Http\Controllers;

use App\Contracts\Events\ExtensionEvent;
use Core\Permission\Infrastructure\Helpers\PermissionNode;
use Extensions\FastMode\Models\FastModeModel;
use Illuminate\Http\Request;

class FastModeController {
    function __construct(
            private PermissionNode $permissionNode,
            private ExtensionEvent $extensionEvent
      ) {
           $this->permissionNode->setNode('fastmode');
      }
    public function index(Request $request) {
        $this->extensionEvent->dispatch($this->permissionNode->getPermission("index"),$request->all());
        return response()->json([
            'message' => FastModeModel::first()
        ]);
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'status' => 'required|in:paid,partial_payment,pending'
        ]);
        $this->extensionEvent->dispatch($this->permissionNode->getPermission("create"),$request->all());
        $validated['business_id'] = $request->get('business_id');
        $update = FastModeModel::updateOrCreate([
            'business_id' => $validated['business_id']
        ],$validated);
        return response()->json(['message'=> $update]);
    }
}