<?php

namespace App\Http\Controllers;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;

class HomeController extends Controller {
    public function index(HookDispatcher $hooks) {
        $view = "welcome";
        $data = $hooks->dispatch(new HookContext(
            action: HookAction::INDEX,
            phase: HookPhase::UI,
            timing: HookTiming::ON,
            module: 'Home',
            payload: [
                'view' => $view 
            ]
        ));
        return view($data['view'],$data);
    }
}