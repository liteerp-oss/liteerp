<?php

namespace Core\Business\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Hooks\HookDispatcher;

class ViewRender
{
    public function __construct(
     private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        $form = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::UI,
                timing: HookTiming::ON,
                payload: $data,
                module: 'Business'
            )
        );
        $index = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::UI,
                timing: HookTiming::ON,
                payload: $data,
                module: 'Business'
            )
        );
        return [
            'form' => [
                ...$form
            ],
            'index' => [
                ...$index
            ]
        ];
    }
}