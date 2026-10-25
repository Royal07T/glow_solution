<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackService
{
    public function isConfigured(): bool
    {
        return filled(config('services.paystack.secret_key'));
    }

    public function initializeTransaction(array $payload): array
    {
        $response = $this->client()->post('/transaction/initialize', $payload);
        $data = $this->validatedData($response, 'Unable to initialize the Paystack payment.');

        if (empty($data['authorization_url'])) {
            throw new RuntimeException('Paystack did not return a checkout URL.');
        }

        return $data;
    }

    public function verifyTransaction(string $reference): array
    {
        $response = $this->client()->get('/transaction/verify/'.rawurlencode($reference));

        return $this->validatedData($response, 'Unable to verify the Paystack payment.');
    }

    public function hasValidWebhookSignature(string $payload, ?string $signature): bool
    {
        $secret = $this->secretKey();

        if (! $secret || ! $signature) {
            return false;
        }

        return hash_equals(hash_hmac('sha512', $payload, $secret), $signature);
    }

    private function client()
    {
        $secret = $this->secretKey();

        if (! $secret) {
            throw new RuntimeException('Paystack is not configured.');
        }

        return Http::baseUrl(rtrim((string) config('services.paystack.payment_url', 'https://api.paystack.co'), '/'))
            ->withToken($secret)
            ->acceptJson()
            ->timeout(20);
    }

    private function validatedData(Response $response, string $fallbackMessage): array
    {
        $body = $response->json();

        if (! $response->successful() || ! is_array($body) || ! ($body['status'] ?? false)) {
            $message = is_array($body) ? ($body['message'] ?? $fallbackMessage) : $fallbackMessage;

            throw new RuntimeException((string) $message);
        }

        if (! is_array($body['data'] ?? null)) {
            throw new RuntimeException($fallbackMessage);
        }

        return $body['data'];
    }

    private function secretKey(): ?string
    {
        $secret = config('services.paystack.secret_key');

        return filled($secret) ? (string) $secret : null;
    }
}
