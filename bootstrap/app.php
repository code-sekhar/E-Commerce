<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'auth:sanctum' => EnsureFrontendRequestsAreStateful::class
        ]);
        $middleware->validateCsrfTokens(except: [
            '/register','/login','/category'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
