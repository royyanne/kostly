<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsOwner;
use App\Http\Middleware\EnsureUserIsSuperAdmin;
use App\Http\Middleware\EnsureUserIsTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'super_admin' => EnsureUserIsSuperAdmin::class,
            'admin' => EnsureUserIsAdmin::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'owner' => EnsureUserIsOwner::class,
            'tenant' => EnsureUserIsTenant::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
