<?php

namespace Extensions\Hrm\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Permission\Application\UseCases\GetPermission;
use Core\Permission\Infrastructure\Helpers\PermissionNode;
use Extensions\Hrm\Services\DownloadCsvService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExportExcelController extends Controller
{
    public function __construct(
        private DownloadCsvService $downloadCsvService,
        private GetPermission $getPermission,
        private PermissionNode $permissionNode
    ) {
        $this->permissionNode->setNode('hrm');
    }
    public function index(Request $request): JsonResponse{
        $validated = $request->validate([]);
        $validated['user_id'] = $request->get('user_id');
        $validated['business_id'] = $request->get('business_id');
        if (
            $this->getPermission->handle([
                ...$validated,
                'permission' => $this->permissionNode->getPermission("index-monthly-summary")
            ])
        ) {
            $validated['permission'] = true;
        } 
        return response()->json([
            "message" => $this->downloadCsvService->index($validated),
            "permission" => $validated['permission'] ?? false
        ]);
    }
    public function show(Request $request,string $id): JsonResponse {
        $validated = $request->validate([]);
        $validated['id'] = $id;
        $validated['user_id'] = $request->get('user_id');
        $validated['business_id'] = $request->get('business_id');
        if (
            $this->getPermission->handle([
                ...$validated,
                'permission' => $this->permissionNode->getPermission("show-monthly-summary")
            ])
        ) {
            $validated['permission'] = true;
        } 
        return response()->json([
            'message' => $this->downloadCsvService->findOne($validated),
            "permission" => $validated['permission'] ?? false
        ]);
    }
}
