<?php

namespace Core\Extension\Http\Middlewares;

use App\Exceptions\BadException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class BlockExtension
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(env('BLOCK_EXTENSION') && env('BLOCK_EXTENSION') === true ) {
            throw new BadException(__('extension::messages.block_extension'));
        }
        return $next($request);
    }
}
