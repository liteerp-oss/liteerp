<?php

namespace Extensions\PusherSetting\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Extensions\PusherSetting\Services\PusherSettingService;
use Extensions\PusherSetting\Http\Requests\SavePusherSettingRequest;

class PusherSettingController extends Controller
{
    public function show(PusherSettingService $svc)
    {
        $setting = $svc->show();
        return response()->json([
            'message' => [
                'config' => $setting
            ]
        ]);
    }

    public function save(SavePusherSettingRequest $request, PusherSettingService $svc)
    {
        $data = $request->validated();
        $saved = $svc->save($data);
        return response()->json([
            'message' => [
                'config' => $saved
            ]
        ]);
    }
}
