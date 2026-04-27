<?php

namespace App\Http\Controllers\FE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;

class DashboardController extends Controller
{
    //
    public function index(HookDispatcher $hooks){
        $data = $hooks->dispatch(new HookContext(
            action: HookAction::INDEX,
            phase: HookPhase::UI,
            timing: HookTiming::ON,
            module: 'Dashboard',
            payload: [
                'childViews' => []
            ]
        ));
        return view('admin.dashboard.index',$data);
    }
}
