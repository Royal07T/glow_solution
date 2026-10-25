<?php

namespace Tests\Feature;

use App\Models\JobCategory;
use App\Models\Lga;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_lga_lookup_returns_lgas_for_the_selected_state(): void
    {
        $lagos = State::create(['name' => 'Lagos State']);
        $ogun = State::create(['name' => 'Ogun State']);
        Lga::create(['state_id' => $lagos->id, 'name' => 'Ikeja']);
        Lga::create(['state_id' => $lagos->id, 'name' => 'Eti-Osa']);
        Lga::create(['state_id' => $ogun->id, 'name' => 'Abeokuta North']);

        $this->getJson(route('locations.lgas', ['state' => $lagos->id]))
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'Eti-Osa'])
            ->assertJsonFragment(['name' => 'Ikeja'])
            ->assertJsonMissing(['name' => 'Abeokuta North']);
    }

    public function test_lga_lookup_requires_a_valid_state(): void
    {
        $this->getJson(route('locations.lgas'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('state');

        $this->getJson(route('locations.lgas', ['state' => 999]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('state');
    }

    public function test_profile_forms_use_database_states_and_lgas(): void
    {
        $state = State::create(['name' => 'Lagos State']);
        Lga::create(['state_id' => $state->id, 'name' => 'Ikeja']);

        $seeker = $this->jobSeeker();
        $seeker->jobSeekerProfile->update(['state' => 'Lagos State', 'city' => 'Ikeja']);

        $this->actingAs($seeker)
            ->get(route('jobseeker.profile.edit'))
            ->assertOk()
            ->assertSee('Lagos State')
            ->assertSee('Ikeja');

        $employer = $this->employerWithPlan();

        $this->actingAs($employer)
            ->get(route('employer.jobs.create'))
            ->assertOk()
            ->assertSee('Lagos State');
    }

    public function test_job_submission_stores_the_selected_state_and_lga(): void
    {
        $state = State::create(['name' => 'Lagos State']);
        Lga::create(['state_id' => $state->id, 'name' => 'Ikeja']);
        $employer = $this->employerWithPlan();
        $category = JobCategory::factory()->create();

        $this->actingAs($employer)
            ->post(route('employer.jobs.store'), [
                'title' => 'House Manager',
                'job_category_id' => $category->id,
                'type' => 'domestic',
                'employment_type' => 'live_in',
                'description' => 'Manage a private residence.',
                'location' => 'Ikeja GRA',
                'city' => 'Ikeja',
                'state' => 'Lagos State',
            ])
            ->assertRedirect(route('employer.jobs.index'));

        $this->assertDatabaseHas('jobs', [
            'employer_profile_id' => $employer->employerProfile->id,
            'city' => 'Ikeja',
            'state' => 'Lagos State',
        ]);
    }
}
