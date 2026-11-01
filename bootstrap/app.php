<?php

use App\Http\Middleware\CheckEmployerVerified;
use App\Http\Middleware\CheckTwoFactor;
use App\Http\Middleware\EnforceSessionTimeout;
use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\EnsureUserRole;
use App\Http\Middleware\RateLimitSensitiveActions;
use App\Http\Middleware\SanitizeInput;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'payments/paystack/webhook',
        ]);
        $middleware->throttleApi();
        $middleware->append(SecurityHeaders::class);
        $middleware->web(append: [
            EnforceSessionTimeout::class,
            SanitizeInput::class,
        ]);
        $middleware->alias([
            'role' => EnsureUserRole::class,
            'subscription' => EnsureSubscriptionActive::class,
            'employer.verified' => CheckEmployerVerified::class,
            'rate.limit' => RateLimitSensitiveActions::class,
            '2fa' => CheckTwoFactor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (InvalidSignatureException $e, Request $request) {
            if (! $request->routeIs('verification.verify')) {
                return null;
            }

            if ($request->user()) {
                return redirect()->route('verification.notice')
                    ->with('error', 'This verification link has expired or is invalid. Please request a new one.');
            }

            return redirect()->route('login')
                ->with('error', 'This verification link has expired or is invalid. Please sign in and request a new one.');
        });
    })->create();
