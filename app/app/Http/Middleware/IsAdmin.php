<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('sanctum');
        if(!$user->check()) {
            
            return response(['message' => __("Not logged")],401);
        }
        $user = $user->user();
        if($user->system_role !== 'admin') {
            if(env('ONLY_ADMIN_CREATE_BUSINESS')
                && env('ONLY_ADMIN_CREATE_BUSINESS') === true) {
                return response(['message' => __("You have not permission")],403);
            }
        }
        return $next($request);
    }
}
