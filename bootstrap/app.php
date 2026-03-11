<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Confiar en proxies (ngrok, load balancers, etc.)
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            \Illuminate\Http\Exceptions\ThrottleRequestsException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($request->isMethod('POST') && !$request->expectsJson()) {
                return back()->withErrors([
                    'throttle' => 'Realizaste demasiados intentos. Esperá unos minutos antes de intentarlo de nuevo.',
                ])->withInput();
            }
        });
    })->create();
