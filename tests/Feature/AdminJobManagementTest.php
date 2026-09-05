<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJobManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_jobs_index_page_and_filter_by_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Kreatif']);
        $job = Job::factory()->create(['category_id' => $category->id, 'title' => 'Video Editor']);

        $response = $this->actingAs($admin)->get(route('admin.jobs.index'));

        $response->assertOk()
            ->assertSee('Semua Lowongan')
            ->assertSee('Video Editor')
            ->assertSee('Kreatif');
    }

    public function test_admin_can_issue_compliance_warning_to_job_and_employer_receives_chat_and_notification(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->create(['title' => 'Staff Cuci Piring']);

        $response = $this->actingAs($admin)->post(route('admin.jobs.warn', $job), [
            'warning_category' => 'salary_not_standard',
            'warning_message' => 'Upah yang dicantumkan berada di bawah batas minimum kelayakan regional.',
            'also_close_job' => 0,
        ]);

        $response->assertRedirect(route('admin.jobs.show', $job))
            ->assertSessionHas('success');

        $job->refresh();
        $this->assertSame('salary_not_standard', $job->admin_warning_category);
        $this->assertSame('Upah yang dicantumkan berada di bawah batas minimum kelayakan regional.', $job->admin_warning_message);
        $this->assertNotNull($job->admin_warned_at);
        $this->assertTrue($job->hasAdminWarning());
        $this->assertSame('open', $job->status);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $admin->id,
            'receiver_id' => $job->employer_id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $job->employer_id,
            'notifiable_type' => User::class,
        ]);
    }

    public function test_admin_can_issue_warning_and_simultaneously_close_job(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->create(['status' => 'open']);

        $response = $this->actingAs($admin)->post(route('admin.jobs.warn', $job), [
            'warning_category' => 'excessive_hours',
            'warning_message' => 'Jam kerja tertera melebihi standar etis 8 jam per hari.',
            'also_close_job' => 1,
        ]);

        $response->assertRedirect(route('admin.jobs.show', $job))
            ->assertSessionHas('success');

        $job->refresh();
        $this->assertSame('excessive_hours', $job->admin_warning_category);
        $this->assertSame('closed', $job->status);
        $this->assertTrue($job->closed_by_admin);
        $this->assertStringContainsString('Peringatan Kepatuhan', (string) $job->closed_reason);
    }

    public function test_admin_can_dismiss_warning(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->create([
            'admin_warning_category' => 'misleading_info',
            'admin_warning_message' => 'Deskripsi tidak jelas.',
            'admin_warned_at' => now(),
        ]);

        $this->assertTrue($job->hasAdminWarning());

        $response = $this->actingAs($admin)->delete(route('admin.jobs.dismissWarning', $job));

        $response->assertRedirect(route('admin.jobs.show', $job))
            ->assertSessionHas('success');

        $job->refresh();
        $this->assertNull($job->admin_warning_category);
        $this->assertNull($job->admin_warning_message);
        $this->assertNull($job->admin_warned_at);
        $this->assertFalse($job->hasAdminWarning());
    }

    public function test_admin_can_close_and_reopen_job_with_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->create(['status' => 'open']);

        // Close job
        $closeResponse = $this->actingAs($admin)->post(route('admin.jobs.close', $job), [
            'close_reason' => 'Perlu peninjauan standar jam kerja.',
            'duration_type' => '14_days',
        ]);

        $closeResponse->assertSessionHas('success');
        $job->refresh();

        $this->assertSame('closed', $job->status);
        $this->assertTrue($job->closed_by_admin);
        $this->assertSame('Perlu peninjauan standar jam kerja.', $job->closed_reason);
        $this->assertNotNull($job->closed_until);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $admin->id,
            'receiver_id' => $job->employer_id,
        ]);

        // Reopen job
        $reopenResponse = $this->actingAs($admin)->post(route('admin.jobs.reopen', $job));
        $reopenResponse->assertSessionHas('success');
        $job->refresh();

        $this->assertSame('open', $job->status);
        $this->assertFalse($job->closed_by_admin);
        $this->assertNull($job->closed_reason);
        $this->assertNull($job->closed_until);
    }

    public function test_admin_can_update_applicant_approval_status(): void
    {
        $admin = User::factory()->admin()->create();
        $application = JobApplication::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)->patch(route('admin.applications.status', $application), [
            'status' => 'accepted',
        ]);

        $response->assertSessionHas('success');
        $application->refresh();
        $this->assertSame('accepted', $application->status);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $admin->id,
            'receiver_id' => $application->user_id,
        ]);
    }
}
