<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_public_responses_send_modern_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeaderMissing('X-XSS-Protection');

        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("script-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-src 'self' https://www.google.com https://maps.google.com", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        $this->assertStringContainsString("base-uri 'self'", $csp);
        $this->assertStringContainsString("form-action 'self'", $csp);
        $this->assertStringNotContainsString("'unsafe-inline'", $csp);
        $this->assertStringNotContainsString("'unsafe-eval'", $csp);
        $this->assertStringNotContainsString('cdn.jsdelivr.net', $csp);
    }

    public function test_public_pages_do_not_rely_on_inline_scripts_or_styles(): void
    {
        foreach ([route('home'), route('login'), route('register')] as $url) {
            $response = $this->get($url)->assertOk();
            $html = $response->getContent();

            $this->assertStringNotContainsString('x-data', $html);
            $this->assertStringNotContainsString('style=', $html);
            $this->assertStringNotContainsString('onerror=', $html);
            $this->assertStringNotContainsString('cdn.jsdelivr.net', $html);
            $this->assertDoesNotMatchRegularExpression('/<script(?![^>]*\bsrc=)[^>]*>/i', $html);
        }
    }

    public function test_authenticated_pages_are_not_cacheable(): void
    {
        $response = $this->actingAs($this->jobSeeker())->get(route('jobseeker.dashboard'));

        $cacheControl = $response->headers->get('Cache-Control');

        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('private', $cacheControl);
    }

    public function test_untrusted_forwarded_headers_are_ignored(): void
    {
        $response = $this
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->withHeaders([
                'X-Forwarded-Proto' => 'https',
                'X-Forwarded-Host' => 'evil.test',
            ])
            ->get('/');

        $response->assertOk()
            ->assertDontSee('https://evil.test', false);
    }

    public function test_forwarded_https_requests_generate_secure_urls(): void
    {
        $response = $this
            ->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->withHeaders([
                'X-Forwarded-Proto' => 'https',
                'X-Forwarded-Host' => 'glow.test',
            ])
            ->get('/');

        $response->assertOk()
            ->assertSee('https://glow.test/logo.png', false)
            ->assertSee('https://glow.test/build/assets/', false);
    }
}
