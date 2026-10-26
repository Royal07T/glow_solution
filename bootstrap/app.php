<?php

use App\Http\Middleware\CheckEmployerVerified;
use App\Http\Middleware\CheckTwoFactor;
use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\EnsureUserRole;
use App\Http\Middleware\RateLimitSensitiveActions;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $trustedProxies = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('TRUSTED_PROXIES', ''))
        )));

        $middleware->trustProxies(at: $trustedProxies);
        $middleware->validateCsrfTokens(except: [
            'payments/paystack/webhook',
        ]);
        $middleware->append(SecurityHeaders::class);
        $middleware->alias([
            'role' => EnsureUserRole::class,
            'subscription' => EnsureSubscriptionActive::class,
            'employer.verified' => CheckEmployerVerified::class,
            'rate.limit' => RateLimitSensitiveActions::class,
            '2fa' => CheckTwoFactor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
