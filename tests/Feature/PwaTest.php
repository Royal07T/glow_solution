<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class PwaTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public static function publicEntryPoints(): array
    {
        return [
            'home' => ['/'],
            'login' => ['/login'],
            'register' => ['/register'],
        ];
    }

    #[DataProvider('publicEntryPoints')]
    public function test_public_pages_link_the_installable_manifest(string $uri): void
    {
        $response = $this->get($uri);

        $response->assertOk()
            ->assertSee('rel="manifest"', false)
            ->assertSee('manifest.json', false)
            ->assertSee('<meta name="theme-color" content="#0B2345">', false)
            ->assertSee('rel="apple-touch-icon"', false);
    }

    public function test_authenticated_portal_pages_link_the_installable_manifest(): void
    {
        $response = $this->actingAs($this->jobSeeker())->get(route('jobseeker.dashboard'));

        $response->assertOk()
            ->assertSee('rel="manifest"', false)
            ->assertSee('manifest.json', false);
    }

    public function test_manifest_references_existing_icons_at_the_declared_sizes(): void
    {
        $manifestPath = public_path('manifest.json');

        $this->assertFileExists($manifestPath);

        $manifest = json_decode(file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('Glow Recruitment Agency Solution', $manifest['name']);
        $this->assertSame('Glow Recruitment', $manifest['short_name']);
        $this->assertSame('/', $manifest['start_url']);
        $this->assertSame('/', $manifest['scope']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('#0B2345', $manifest['theme_color']);
        $this->assertSame('#F5F7FA', $manifest['background_color']);

        $icons = collect($manifest['icons']);

        $this->assertTrue($icons->contains(
            fn (array $icon): bool => $icon['sizes'] === '192x192' && $icon['purpose'] === 'any'
        ));
        $this->assertTrue($icons->contains(
            fn (array $icon): bool => $icon['sizes'] === '512x512' && $icon['purpose'] === 'any'
        ));
        $this->assertTrue($icons->contains(
            fn (array $icon): bool => $icon['purpose'] === 'maskable'
        ));

        foreach ($manifest['icons'] as $icon) {
            $path = public_path(ltrim($icon['src'], '/'));
            $this->assertFileExists($path);
            $this->assertSame('image/png', $icon['type']);

            [$width, $height] = getimagesize($path);

            $this->assertSame($icon['sizes'], "{$width}x{$height}");
        }
    }

    public function test_service_worker_and_offline_fallback_are_published(): void
    {
        $this->assertFileExists(public_path('sw.js'));
        $this->assertFileExists(public_path('offline.html'));

        $serviceWorker = file_get_contents(public_path('sw.js'));

        $this->assertStringContainsString('/offline.html', $serviceWorker);
        $this->assertStringContainsString("addEventListener('fetch'", $serviceWorker);
    }
}
