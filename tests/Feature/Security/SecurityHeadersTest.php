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
    }

    public function test_authenticated_pages_are_not_cacheable(): void
    {
        $response = $this->actingAs($this->jobSeeker())->get(route('jobseeker.dashboard'));

        $cacheControl = $response->headers->get('Cache-Control');

        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('private', $cacheControl);
    }
}
