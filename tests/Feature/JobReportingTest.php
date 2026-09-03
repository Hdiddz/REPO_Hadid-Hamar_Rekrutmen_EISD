<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_report_suspicious_job(): void
    {
        $jobseeker = User::factory()->jobseeker()->create();
        $job = Job::factory()->create(['status' => 'open']);

        $response = $this->actingAs($jobseeker)->post(route('jobs.report', $job), [
            'reason' => 'Indikasi Percaloan / Pungutan Biaya Administrasi',
            'details' => 'Mitra meminta uang muka sebesar 100 ribu sebelum interview.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('job_reports', [
            'job_id' => $job->id,
            'reporter_id' => $jobseeker->id,
            'reason' => 'Indikasi Percaloan / Pungutan Biaya Administrasi',
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_review_and_take_action_on_report(): void
    {
        $admin = User::factory()->admin()->create();
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->for($employer, 'employer')->create(['status' => 'open']);
        $reporter = User::factory()->jobseeker()->create();

        $report = JobReport::create([
            'job_id' => $job->id,
            'reporter_id' => $reporter->id,
            'reason' => 'Upah Tidak Transparan / Di Bawah Standar',
            'details' => 'Upah yang dijanjikan jauh di bawah upah minimum wilayah.',
            'status' => 'pending',
        ]);

        // Admin views report details (marks as reviewed)
        $this->actingAs($admin)->get(route('admin.reports.show', $report))->assertOk();
        $report->refresh();
        $this->assertSame('reviewed', $report->status);

        // Admin takes action: close job
        $actionResponse = $this->actingAs($admin)->post(route('admin.reports.action', $report), [
            'action_type' => 'close_job',
            'close_duration' => '7_days',
            'admin_notes' => 'Tutup lowongan selama 7 hari sampai mitra merevisi standar upah.',
        ]);

        $actionResponse->assertRedirect(route('admin.reports.index'))
            ->assertSessionHas('success');

        $report->refresh();
        $job->refresh();

        $this->assertSame('action_taken', $report->status);
        $this->assertSame('closed', $job->status);
        $this->assertTrue($job->closed_by_admin);
    }
}
