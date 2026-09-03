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

    public function test_admin_can_edit_job_details(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $job = Job::factory()->create(['title' => 'Judul Awal']);

        $response = $this->actingAs($admin)->put(route('admin.jobs.update', $job), [
            'category_id' => $category->id,
            'title' => 'Judul Diperbarui Admin',
            'description' => 'Deskripsi pekerjaan yang telah diperbarui admin untuk memenuhi standar.',
            'location' => 'Kota Bandung',
            'salary_type' => 'monthly',
            'salary_amount' => 4500000,
            'work_hours_per_day' => 8,
            'status' => 'open',
        ]);

        $response->assertRedirect(route('admin.jobs.show', $job))
            ->assertSessionHas('success');

        $job->refresh();
        $this->assertSame('Judul Diperbarui Admin', $job->title);
        $this->assertSame('Kota Bandung', $job->location);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $admin->id,
            'receiver_id' => $job->employer_id,
        ]);
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
