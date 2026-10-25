<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_admin_can_view_audit_log_details(): void
    {
        $admin = $this->admin(['name' => 'Platform Admin']);
        $job = $this->job($this->employer());

        $this->actingAs($admin);
        AuditLog::record('job.updated', $job, ['status' => 'pending'], ['status' => 'approved']);
        $log = AuditLog::latest('id')->firstOrFail();

        $this->get(route('admin.audit-logs.show', $log))
            ->assertOk()
            ->assertSee('job.updated')
            ->assertSee('Platform Admin')
            ->assertSee('Previous values')
            ->assertSee('pending')
            ->assertSee('New values')
            ->assertSee('approved')
            ->assertSee(route('admin.jobs.show', $job), false);
    }

    public function test_audit_log_index_links_to_details(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin);
        AuditLog::record('user.suspended', $admin, ['is_suspended' => false], ['is_suspended' => true]);
        $log = AuditLog::latest('id')->firstOrFail();

        $this->get(route('admin.audit-logs.index'))
            ->assertOk()
            ->assertSee(route('admin.audit-logs.show', $log), false);
    }

    public function test_non_admin_users_cannot_view_audit_log_details(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin);
        AuditLog::record('user.suspended', $admin, ['is_suspended' => false], ['is_suspended' => true]);
        $log = AuditLog::latest('id')->firstOrFail();

        $this->actingAs($this->employerWithPlan())
            ->get(route('admin.audit-logs.show', $log))
            ->assertForbidden();
    }
}
