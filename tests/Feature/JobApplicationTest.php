<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_jobseeker_can_apply_with_pdf_and_pivot_data_is_saved(): void
    {
        Storage::fake('local');
        $jobseeker = User::factory()->jobseeker()->create();
        $job = Job::factory()->create();

        $response = $this->actingAs($jobseeker)->post(route('applications.store', $job), [
            'resume' => UploadedFile::fake()->create('resume.pdf', 120, 'application/pdf'),
            'note' => 'Saya memiliki pengalaman pelayanan pelanggan selama satu tahun.',
        ]);

        $response->assertRedirect(route('applications.index'))
            ->assertSessionHas('success');
        $application = JobApplication::query()->sole();
        $this->assertSame($job->id, $application->job_id);
        $this->assertSame($jobseeker->id, $application->user_id);
        $this->assertSame('pending', $application->status);
        Storage::disk('local')->assertExists($application->resume_file);
        $this->assertTrue($job->fresh()->applicants()->whereKey($jobseeker->id)->exists());
    }

    public function test_resume_validation_is_rendered_below_the_field(): void
    {
        $jobseeker = User::factory()->jobseeker()->create();
        $job = Job::factory()->create();

        $response = $this->actingAs($jobseeker)->followingRedirects()
            ->from(route('jobs.show', $job))
            ->post(route('applications.store', $job), ['note' => 'Tanpa resume']);

        $response->assertOk()
            ->assertSee('Resume PDF wajib diunggah.');
    }

    public function test_guest_cannot_see_message_or_application_controls(): void
    {
        $job = Job::factory()->create();

        $this->get(route('jobs.show', $job))
            ->assertOk()
            ->assertSee('Masuk untuk melamar')
            ->assertDontSee('Pesan mitra')
            ->assertDontSee('Kirim lamaran');
    }

    public function test_employer_owner_and_admin_can_download_private_resume_but_other_employer_cannot(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('resumes/candidate.pdf', '%PDF-1.4 test');
        $owner = User::factory()->employer()->create();
        $otherEmployer = User::factory()->employer()->create();
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->for($owner, 'employer')->create();
        $application = JobApplication::factory()->for($job)->create([
            'resume_file' => 'resumes/candidate.pdf',
        ]);

        $this->actingAs($owner)->get(route('employer.applications.resume', $application))->assertDownload();
        $this->actingAs($admin)->get(route('admin.applications.resume', $application))->assertDownload();
        $this->actingAs($otherEmployer)->get(route('employer.applications.resume', $application))->assertForbidden();
    }

    public function test_employer_and_admin_can_preview_private_resume_inline_but_other_employer_cannot(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('resumes/candidate.pdf', '%PDF-1.4 test preview');
        $owner = User::factory()->employer()->create();
        $otherEmployer = User::factory()->employer()->create();
        $admin = User::factory()->admin()->create();
        $applicant = User::factory()->jobseeker()->create();
        $job = Job::factory()->for($owner, 'employer')->create();
        $application = JobApplication::factory()->for($job)->for($applicant, 'user')->create([
            'resume_file' => 'resumes/candidate.pdf',
        ]);

        $ownerResponse = $this->actingAs($owner)->get(route('employer.applications.resume.preview', $application));
        $ownerResponse->assertOk();
        $this->assertSame('application/pdf', $ownerResponse->headers->get('content-type'));
        $this->assertStringContainsString('inline', $ownerResponse->headers->get('content-disposition'));

        $adminResponse = $this->actingAs($admin)->get(route('admin.applications.resume.preview', $application));
        $adminResponse->assertOk();
        $this->assertSame('application/pdf', $adminResponse->headers->get('content-type'));

        $applicantResponse = $this->actingAs($applicant)->get(route('applications.resume.preview', $application));
        $applicantResponse->assertOk();
        $this->assertSame('application/pdf', $applicantResponse->headers->get('content-type'));

        $this->actingAs($otherEmployer)->get(route('employer.applications.resume.preview', $application))->assertForbidden();
    }

    public function test_duplicate_application_is_rejected_without_second_pivot_row(): void
    {
        Storage::fake('local');
        $jobseeker = User::factory()->jobseeker()->create();
        $job = Job::factory()->create();
        JobApplication::factory()->for($job)->for($jobseeker, 'user')->create();

        $response = $this->actingAs($jobseeker)->post(route('applications.store', $job), [
            'resume' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHas('warning');
        $this->assertDatabaseCount('job_applications', 1);
    }

    public function test_application_status_flows_from_jobseeker_to_employer_and_back_to_jobseeker(): void
    {
        Storage::fake('local');
        $employer = User::factory()->employer()->create();
        $jobseeker = User::factory()->jobseeker()->create();
        $job = Job::factory()->for($employer, 'employer')->create();

        $this->actingAs($jobseeker)->post(route('applications.store', $job), [
            'resume' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
            'note' => 'Siap mengikuti proses seleksi.',
        ])->assertRedirect(route('applications.index'));

        $application = JobApplication::query()->sole();
        $this->actingAs($employer)->patch(route('employer.applications.update', $application), [
            'status' => 'accepted',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => 'accepted',
        ]);
        $this->actingAs($jobseeker)->get(route('applications.index'))
            ->assertOk()
            ->assertSee('Diterima');
    }

    public function test_applied_jobseeker_can_view_job_detail_even_when_under_review_or_closed(): void
    {
        $employer = User::factory()->employer()->create();
        $jobseeker = User::factory()->jobseeker()->create();
        $job = Job::factory()->for($employer, 'employer')->create([
            'title' => 'Video Editor Kreatif',
            'status' => 'open',
        ]);

        $application = JobApplication::factory()->for($job)->for($jobseeker, 'user')->create([
            'status' => 'pending',
        ]);

        // Jobseeker can see "Lihat Detail Lowongan" in applications history
        $this->actingAs($jobseeker)->get(route('applications.index'))
            ->assertOk()
            ->assertSee('Lihat Detail Lowongan')
            ->assertSee('Video Editor Kreatif');

        // Jobseeker can view job detail page when under review
        $this->actingAs($jobseeker)->get(route('jobs.show', $job))
            ->assertOk()
            ->assertSee('Video Editor Kreatif')
            ->assertSee('Lamaran Sudah Terkirim')
            ->assertSee('Menunggu Tinjauan');

        // Even if job is closed by employer, applied jobseeker can still view job details
        $job->update(['status' => 'closed']);

        $this->actingAs($jobseeker)->get(route('jobs.show', $job))
            ->assertOk()
            ->assertSee('Video Editor Kreatif')
            ->assertSee('Lamaran Sudah Terkirim');
    }

    public function test_jobseeker_can_cancel_their_own_application_and_resume_file_is_removed(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('resumes/test_cancel.pdf', '%PDF-1.4 test');

        $jobseeker = User::factory()->jobseeker()->create();
        $job = Job::factory()->create(['title' => 'Graphic Designer']);
        $application = JobApplication::factory()->for($job)->for($jobseeker, 'user')->create([
            'resume_file' => 'resumes/test_cancel.pdf',
        ]);

        $response = $this->actingAs($jobseeker)->delete(route('applications.destroy', $application));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('job_applications', [
            'id' => $application->id,
        ]);
        Storage::disk('local')->assertMissing('resumes/test_cancel.pdf');
    }

    public function test_other_user_cannot_cancel_another_users_application(): void
    {
        Storage::fake('local');
        $owner = User::factory()->jobseeker()->create();
        $otherUser = User::factory()->jobseeker()->create();
        $job = Job::factory()->create();
        $application = JobApplication::factory()->for($job)->for($owner, 'user')->create();

        $this->actingAs($otherUser)
            ->delete(route('applications.destroy', $application))
            ->assertForbidden();

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
        ]);
    }
}
