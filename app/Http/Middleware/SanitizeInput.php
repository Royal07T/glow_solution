<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Inputs that must be preserved exactly as supplied.
     *
     * @var list<string>
     */
    private const PRESERVED_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        '_token',
        '_method',
        'entitlements',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('payments/paystack/webhook')) {
            return $next($request);
        }

        $request->query->replace($this->sanitize($request->query->all()));
        $request->request->replace($this->sanitize($request->request->all()));

        if ($request->isJson()) {
            $request->json()->replace($this->sanitize($request->json()->all()));
        }

        return $next($request);
    }

    /**
     * @param  array<array-key, mixed>  $input
     * @return array<array-key, mixed>
     */
    private function sanitize(array $input): array
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $input[$key] = $this->sanitize($value);

                continue;
            }

            if (! is_string($value) || in_array($key, self::PRESERVED_KEYS, true)) {
                continue;
            }

            $input[$key] = $this->sanitizeString($value);
        }

        return $input;
    }

    private function sanitizeString(string $value): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? $value;
        $value = preg_replace('/<\s*(script|style)\b[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $value) ?? $value;
        $value = preg_replace('/<!--.*?-->/s', '', $value) ?? $value;
        $value = preg_replace('/<\s*\/?\s*[a-zA-Z][^>]*>/', '', $value) ?? $value;

        return trim($value);
    }
}
