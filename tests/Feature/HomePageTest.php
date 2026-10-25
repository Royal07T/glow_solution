<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_home_page_shows_google_map_for_company_address(): void
    {
        config([
            'company.address' => '12 Test Street, Abuja',
            'company.phone' => '08036227769',
            'company.map' => '9.130070,7.492158',
            'company.map_link' => 'https://maps.app.goo.gl/vmrFrJAoCesdbujr8',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Contact Us')
            ->assertSee('https://www.google.com/maps?q=9.130070%2C7.492158', false)
            ->assertSee('z=17', false)
            ->assertSee('output=embed', false)
            ->assertSee('https://maps.app.goo.gl/vmrFrJAoCesdbujr8', false)
            ->assertSee('title="Map showing the Glow Recruitment Agency office location"', false);
    }

    public function test_home_page_has_floating_whatsapp_link(): void
    {
        config(['company.phone' => '08036227769']);

        $this->get('/')
            ->assertOk()
            ->assertSee('https://wa.me/2348036227769', false)
            ->assertSee('Chat with Glow Recruitment on WhatsApp')
            ->assertSee('Back to top')
            ->assertSee('data-back-to-top', false);
    }

    public function test_whatsapp_link_normalises_international_phone_numbers(): void
    {
        config(['company.phone' => '+234 803 622 7769']);

        $this->get('/')
            ->assertOk()
            ->assertSee('https://wa.me/2348036227769', false);
    }

    public function test_admin_approved_job_appears_in_featured_opportunities(): void
    {
        $job = $this->job($this->employer(), [
            'title' => 'Live-in Nanny Needed',
            'status' => 'pending',
            'published_at' => null,
        ]);

        $this->get('/')->assertDontSee('Live-in Nanny Needed');

        $this->actingAs($this->admin())
            ->post(route('admin.jobs.approve', $job), ['admin_notes' => 'Looks good'])
            ->assertRedirect();

        $this->assertSame('approved', $job->fresh()->status);
        $this->assertNotNull($job->fresh()->published_at);

        $this->get('/')
            ->assertOk()
            ->assertSee('Featured Opportunities')
            ->assertSee('Live-in Nanny Needed');
    }

    public function test_expired_job_is_not_featured(): void
    {
        $this->job($this->employer(), [
            'title' => 'Expired Role',
            'status' => 'approved',
            'published_at' => now()->subMonth(),
            'expires_at' => now()->subDay(),
        ]);

        $this->get('/')->assertDontSee('Expired Role');
    }
}
