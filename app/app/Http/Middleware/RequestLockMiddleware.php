<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RequestLockMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $data = $request->all();
        $this->sortRecursive($data);
        $hash = hash('sha256', json_encode($data));
        $lockKey = sprintf(
            'lock:%s:%s:%s',
            $request->method(),
            $request->path(),
            $hash
        );
        if ($request->user()) {
            $lockKey .= ':user:' . $request->user()->id;
        }
        return Cache::lock($lockKey, 5)->block(1, function () use ($next, $request) {
            return $next($request);
        });
    }

    private function sortRecursive(&$array)
    {
        if (!is_array($array)) return;

        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->sortRecursive($value);
            }
        }

        ksort($array);
    }
}