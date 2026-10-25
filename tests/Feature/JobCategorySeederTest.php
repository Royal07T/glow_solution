<?php

namespace Tests\Feature;

use App\Models\JobCategory;
use Database\Seeders\JobCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobCategorySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_nigerian_job_categories_idempotently(): void
    {
        $this->seed(JobCategorySeeder::class);
        $this->seed(JobCategorySeeder::class);

        foreach (['Drivers', 'Marketing & Advertising', 'Sales Representatives', 'Housekeeping', 'Oil, Gas & Energy'] as $name) {
            $this->assertDatabaseHas('job_categories', [
                'name' => $name,
                'is_active' => true,
            ]);
        }

        $this->assertGreaterThanOrEqual(60, JobCategory::count());
        $this->assertSame(JobCategory::count(), JobCategory::distinct()->count('slug'));
    }
}
