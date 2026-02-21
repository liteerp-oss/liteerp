<?php

namespace Extensions\Hrm\Http\Controllers\Api;

use App\Contracts\Events\ExtensionEvent;
use App\Exceptions\UnauthorizedException;
use App\Http\Controllers\Controller;
use Core\Permission\Application\UseCases\GetPermission;
use Core\Permission\Infrastructure\Helpers\PermissionNode;
use Extensions\Hrm\Services\LeaveService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeaveRequestController extends Controller
{
    public function __construct(
        private LeaveService $leaveService,
        private GetPermission $getPermission,
        private PermissionNode $permissionNode
    ) {
        $this->permissionNode->setNode('hrm');
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([]);
        $validated['user_id'] = $request->get('user_id');
        $validated['business_id'] = $request->get('business_id');
        if (
            $this->getPermission->handle([
                ...$validated,
                'permission' => $this->permissionNode->getPermission("index-leave")
            ])
        ) {
            return response()->json([
                'message' => [
                    'list' => $this->leaveService->getLeaveRequestsAll($validated),
                    'permission' => true,
                ],
            ]);
        }
        $leaveRequests = $this->leaveService->getLeaveRequestsForUser($validated);
        return response()->json([
            'message' => [
                'list' => $leaveRequests,
                'permission' => false,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|in:annual,sick,unpaid',
            'reason' => 'nullable|string',
        ]);
        $all = $request->all();
        $validated['user_id'] = $all['user_id'];
        $validated['business_id'] = $all['business_id'];

        $leaveRequest = $this->leaveService->createLeaveRequest($validated);

        return response()->json($leaveRequest);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);
        $validated['user_id'] = $request->all('user_id');
        $validated['business_id'] = $request->all('business_id');
        if (
            $this->getPermission->handle([
                ...$validated,
                'permission' => $this->permissionNode->getPermission("approve-leave")
            ])
        ) {
            throw new UnauthorizedException(__("extension.hrm::not_permission"));
        }
        return response()->json(['message' => $this->leaveService->updateLeaveRequest($id, $validated)]);
    }
}
