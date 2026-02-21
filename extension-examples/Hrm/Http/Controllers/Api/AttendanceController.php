<?php

namespace Extensions\Hrm\Http\Controllers\Api;

use App\Contracts\Events\ExtensionEvent;
use App\Exceptions\UnauthorizedException;
use App\Http\Controllers\Controller;
use Core\Permission\Application\UseCases\GetPermission;
use Core\Permission\Infrastructure\Helpers\PermissionNode;
use Extensions\Hrm\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
        private GetPermission $getPermission,
        private PermissionNode $permissionNode,
    ) {
        $this->permissionNode->setNode('hrm');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'note' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->get('user_id');
        $validated['business_id'] = $request->get('business_id');
        $validated['user_agent'] = $request->userAgent();
        $validated['ip'] = $request->ip();
        $attendance = $this->attendanceService->store($validated);
        return response()->json([
            'message' => $attendance
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'note' => 'nullable|string'
        ]);
        $validated['user_id'] = $request->get('user_id');
        $validated['business_id'] = $request->get('business_id');
        $validated['id'] = $id;
        if (
            $this->getPermission->handle([
                ...$validated,
                'permission' => $this->permissionNode->getPermission("approve-attendance")
            ])
        ) {
            $validated['approved'] = $request->get('approved');
        } else if ($this->getPermission->handle([
                ...$validated,
                'permission' => $this->permissionNode->getPermission("update-attendance")
            ])) {
            $validated['note'] = $request->get('note') ?? null;
        } else {
            throw new UnauthorizedException(__("extension.hrm::not_permission"));
        }
        
        return response()->json([
            'message' => $this->attendanceService->update($validated)
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:' . (date('Y', time()) + 1),
        ]);
        $validated['user_id'] = $request->get('user_id');
        $validated['business_id'] = $request->get('business_id');
        if (
            $this->getPermission->handle([
                ...$validated,
                'permission' => $this->permissionNode->getPermission("index-attendance")
            ])
        ) {
            $validated['permission'] = true;
        }
        return response()->json([
            'message' => [
                'list' => $this->attendanceService->index($validated),
                'permission' => $validated['permission'] ?? false
            ]
        ]);
    }
}
