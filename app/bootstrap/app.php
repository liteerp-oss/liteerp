<?php

use App\Exceptions\BadException;
use App\Http\Middleware\AppLanguage;
use App\Http\Middleware\BusinessAdmin;
use App\Http\Middleware\BusinessToken;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsLogged;
use App\Http\Middleware\RequestLockMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Auth\Middleware\Authenticate;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias([
            'app.isLogged' => IsLogged::class,
            'business.token' => BusinessToken::class,
            'app.isAdmin'   => IsAdmin::class,
            'auth:sanctum' => EnsureFrontendRequestsAreStateful::class,
            'auth' => Authenticate::class,
            'app.language' => AppLanguage::class,
            'app.lock' => RequestLockMiddleware::class
        ]);
        $middleware->group('business', [
            'business.token',
            'app.language',
            'app.lock'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
