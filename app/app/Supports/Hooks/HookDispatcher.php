<?php

namespace App\Supports\Hooks;

use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookResult;
use Illuminate\Contracts\Container\Container;

class HookDispatcher
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Dispatch hook theo context
     */
    public function dispatch(HookContext $context): mixed
    {
        foreach ($this->container->tagged('liteerp.hooks') as $hook) {
            if (! $hook instanceof HookInterface) {
                continue;
            }
            if (! $hook::supports($context)) {
                continue;
            }
            $result = $hook->handle($context);
            if ($result->stop) {
                return $result->payload ?? [];
            }
            if ($result->payload !== null) {
                $context->payload = $result->payload;
            }
        }

        return $context->payload;
    }
}
