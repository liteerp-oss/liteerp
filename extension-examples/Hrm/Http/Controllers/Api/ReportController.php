<?php

namespace Extensions\Hrm\Http\Controllers\Api;

use App\Exceptions\UnauthorizedException;
use App\Http\Controllers\Controller;
use Core\BusinessRole\Domain\Services\BusinessRoleService;
use Core\Permission\Application\UseCases\GetPermission;
use Core\Permission\Infrastructure\Helpers\PermissionNode;
use Extensions\Hrm\Services\LeaveService;
use Extensions\Hrm\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private GetPermission $getPermission,
        private PermissionNode $permissionNode
    ) {
        $this->permissionNode->setNode('hrm');
    }
    public function index(Request $request): JsonResponse{
        $validated = $request->validate([
            "keywords"=> "nullable|max:150|string",
        ]);
        $validated['user_id'] = $request->get('user_id');
        $validated['business_id'] = $request->get('business_id');
        if (
            $this->getPermission->handle([
                ...$validated,
                'permission' => $this->permissionNode->getPermission("index-report")
            ])
        ) {
            $validated['permission'] = true;
        }
        return response()->json([
            "message" => $this->reportService->index($validated)
        ]);
    }
    public function store(Request $request): JsonResponse {
        $validated = $request->validate([
            'summary'=> 'required|max:10000',
            'issues'=> 'nullable|max:10000',
            'tasks_done'=> 'nullable|max:10000',
        ]);
        $validated['user_id'] = $request->get('user_id');
        $validated['business_id'] = $request->get('business_id');
        if (
            $this->getPermission->handle([
                ...$validated,
                'permission' => $this->permissionNode->getPermission("create-report")
            ])
        ) {
            throw new UnauthorizedException(__("extension.hrm::not_permission"));
        }
        return response()->json([
            'message' => $this->reportService->store($validated),
        ]);
    }
}
