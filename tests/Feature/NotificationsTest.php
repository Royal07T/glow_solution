<?php

namespace Tests\Feature;

use App\Models\EmployerVerification;
use App\Notifications\SystemNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_job_approval_notifies_the_employer(): void
    {
        $employer = $this->employer();
        $job = $this->job($employer, [
            'title' => 'Live-out Chef',
            'status' => 'pending',
            'published_at' => null,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.jobs.approve', $job), ['admin_notes' => 'Looks good'])
            ->assertRedirect();

        $notification = $employer->fresh()->notifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame('Job approved', $notification->data['title']);
        $this->assertSame(route('employer.jobs.show', $job, false), $notification->data['url']);
    }

    public function test_job_rejection_notifies_the_employer(): void
    {
        $employer = $this->employer();
        $job = $this->job($employer, ['status' => 'pending', 'published_at' => null]);

        $this->actingAs($this->admin())
            ->post(route('admin.jobs.reject', $job), ['admin_notes' => 'Missing requirements'])
            ->assertRedirect();

        $notification = $employer->fresh()->notifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame('Job needs attention', $notification->data['title']);
    }

    public function test_application_status_change_notifies_the_job_seeker(): void
    {
        $employer = $this->employer();
        $seeker = $this->jobSeeker();
        $job = $this->job($employer, ['title' => 'House Manager']);
        $application = $this->application($seeker, $job);

        $this->actingAs($employer)
            ->patch(route('employer.applications.update-status', $application), ['status' => 'interview'])
            ->assertRedirect();

        $notification = $seeker->fresh()->notifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame('Application update', $notification->data['title']);
        $this->assertStringContainsString('House Manager', $notification->data['message']);
        $this->assertStringContainsString('Interview', $notification->data['message']);
    }

    public function test_new_application_notifies_the_employer(): void
    {
        $employer = $this->employer();
        $seeker = $this->jobSeeker();
        $job = $this->job($employer, ['title' => 'Family Driver']);

        $this->actingAs($seeker)
            ->post(route('applications.store', $job))
            ->assertRedirect(route('applications.index'));

        $notification = $employer->fresh()->notifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame('New application received', $notification->data['title']);
        $this->assertStringContainsString('Family Driver', $notification->data['message']);
    }

    public function test_verification_decision_notifies_the_employer(): void
    {
        $employer = $this->employer(false);

        $verification = EmployerVerification::create([
            'employer_profile_id' => $employer->employerProfile->id,
            'document_type' => 'Business registration',
            'document_path' => 'verifications/test.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.verifications.approve', $verification))
            ->assertRedirect();

        $notification = $employer->fresh()->notifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame('Verification approved', $notification->data['title']);
    }

    public function test_notification_center_lists_and_marks_notifications_as_read(): void
    {
        $seeker = $this->jobSeeker();

        $seeker->notify(new SystemNotification('First update', 'First message', '/notifications', 'info'));
        $seeker->notify(new SystemNotification('Second update', 'Second message', '/notifications', 'success'));

        $this->actingAs($seeker)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('First update')
            ->assertSee('Second update');

        $notification = $seeker->notifications()->oldest()->first();

        $this->actingAs($seeker)
            ->get(route('notifications.read', $notification->id))
            ->assertRedirect('/notifications');

        $this->assertNotNull($notification->fresh()->read_at);

        $this->actingAs($seeker)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $seeker->fresh()->unreadNotifications()->count());
    }

    public function test_free_plan_activation_notifies_the_user(): void
    {
        $seeker = $this->jobSeeker();
        $plan = $this->plan('free-jobseeker', 'job_seeker', true);

        $this->actingAs($seeker)
            ->post(route('subscriptions.activate', $plan))
            ->assertRedirect(route('jobseeker.dashboard'));

        $notification = $seeker->fresh()->notifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame('Subscription activated', $notification->data['title']);
    }
}
