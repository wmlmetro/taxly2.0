<?php

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth-or-apikey' => \App\Http\Middleware\ApiKeyOrTokenMiddleware::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'httponly.xsrf' => \App\Http\Middleware\HttpOnlyXsrfToken::class,
        ]);

        // Trust all proxies for HTTPS behind ingress
        $middleware->trustProxies(
            at: '*',
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_PREFIX
        );

        // Apply security middleware globally
        $middleware->append([
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\HttpOnlyXsrfToken::class,
        ]);

        // $middleware->group('api', [
        //     ForceJsonResponse::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
