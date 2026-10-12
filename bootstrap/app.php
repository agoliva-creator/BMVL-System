<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add this alias array
        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdmin::class,
            'staff' => \App\Http\Middleware\StaffMiddleware::class, // NEW: Added Staff Middleware
            'secure.api' => \App\Http\Middleware\AccessMiddleware::class,
            'preventAccess' => \App\Http\Middleware\AccessMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
