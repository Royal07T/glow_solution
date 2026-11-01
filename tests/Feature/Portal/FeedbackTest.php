<?php

namespace Tests\Feature\Portal;

use App\Models\Feedback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_job_seeker_can_submit_feedback_about_their_application(): void
    {
        $seeker = $this->jobSeeker();
        $job = $this->job($this->employer());
        $application = $this->application($seeker, $job);

        $this->actingAs($seeker)
            ->post(route('feedback.store'), [
                'type' => 'application',
                'application_id' => $application->id,
                'rating' => 4,
                'subject' => 'Smooth process',
                'message' => 'The interview process was clear and timely.',
            ])
            ->assertRedirect(route('feedback.create'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('feedback', [
            'user_id' => $seeker->id,
            'job_id' => $job->id,
            'application_id' => $application->id,
            'type' => 'application',
            'rating' => 4,
        ]);
    }

    public function test_job_seeker_cannot_give_feedback_on_another_users_application(): void
    {
        $seeker = $this->jobSeeker();
        $otherSeeker = $this->jobSeeker();
        $application = $this->application($otherSeeker, $this->job($this->employer()));

        $this->actingAs($seeker)
            ->post(route('feedback.store'), [
                'type' => 'application',
                'application_id' => $application->id,
                'message' => 'This should not be allowed.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('feedback', 0);
    }

    public function test_user_can_submit_job_and_general_feedback(): void
    {
        $seeker = $this->jobSeeker();
        $job = $this->job($this->employer());

        $this->actingAs($seeker)
            ->post(route('feedback.store'), [
                'type' => 'job',
                'job_id' => $job->id,
                'rating' => 5,
                'message' => 'The job description was accurate.',
            ])
            ->assertSessionHasNoErrors();

        $this->actingAs($seeker)
            ->post(route('feedback.store'), [
                'type' => 'general',
                'message' => 'The site is easy to use.',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('feedback', [
            'user_id' => $seeker->id,
            'job_id' => $job->id,
            'type' => 'job',
            'rating' => 5,
        ]);

        $this->assertDatabaseHas('feedback', [
            'user_id' => $seeker->id,
            'job_id' => null,
            'application_id' => null,
            'type' => 'general',
            'rating' => null,
        ]);
    }

    public function test_feedback_requires_a_message_and_a_valid_type(): void
    {
        $this->actingAs($this->jobSeeker())
            ->post(route('feedback.store'), [
                'type' => 'not-a-type',
                'rating' => 9,
                'message' => '',
            ])
            ->assertSessionHasErrors(['type', 'rating', 'message']);
    }

    public function test_admin_can_review_submitted_feedback(): void
    {
        $seeker = $this->jobSeeker();
        Feedback::factory()->create([
            'user_id' => $seeker->id,
            'type' => 'general',
            'subject' => 'Search filters',
            'message' => 'Please add more location filters.',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.feedback.index'))
            ->assertOk()
            ->assertSee('Please add more location filters.')
            ->assertSee($seeker->email);
    }

    public function test_non_admin_cannot_review_feedback(): void
    {
        $this->actingAs($this->jobSeeker())
            ->get(route('admin.feedback.index'))
            ->assertForbidden();
    }

    public function test_feedback_submissions_are_rate_limited(): void
    {
        $seeker = $this->jobSeeker();

        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($seeker)
                ->post(route('feedback.store'), [
                    'type' => 'general',
                    'message' => 'Feedback number '.$i,
                ])
                ->assertSessionHasNoErrors();
        }

        $this->actingAs($seeker)
            ->post(route('feedback.store'), [
                'type' => 'general',
                'message' => 'This one should be rate limited.',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('feedback', 5);
    }
}
