<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Notifications\ResignationDecisionNotification;
use App\Notifications\ResignationSubmittedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
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

    public function test_jobseeker_cannot_cancel_accepted_application(): void
    {
        Storage::fake('local');
        $jobseeker = User::factory()->jobseeker()->create();
        $job = Job::factory()->create(['title' => 'Graphic Designer']);
        $application = JobApplication::factory()->for($job)->for($jobseeker, 'user')->create([
            'status' => 'accepted',
            'start_date' => now()->addDays(3)->toDateString(),
        ]);

        $this->actingAs($jobseeker)
            ->delete(route('applications.destroy', $application))
            ->assertForbidden();

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => 'accepted',
        ]);
    }

    public function test_jobseeker_views_accepted_job_details_with_start_date_and_no_cancel_button(): void
    {
        $jobseeker = User::factory()->jobseeker()->create();
        $employer = User::factory()->employer()->create(['business_name' => 'Studio Kreatif Bandung']);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Video Editor']);
        $startDate = now()->addDays(5)->startOfDay();

        $application = JobApplication::factory()->for($job)->for($jobseeker, 'user')->create([
            'status' => 'accepted',
            'start_date' => $startDate->toDateString(),
            'acceptance_notes' => 'Harap bawa laptop dan identitas diri pada hari pertama.',
        ]);

        $response = $this->actingAs($jobseeker)->get(route('jobs.show', $job));

        $response->assertOk()
            ->assertSee('Selamat, Anda Diterima!')
            ->assertSee('Resmi Diterima')
            ->assertSee($startDate->translatedFormat('l, d F Y'))
            ->assertSee('Harap bawa laptop dan identitas diri pada hari pertama.')
            ->assertDontSee('id="cancelApplicationForm-'.$application->id.'"', false);
    }

    public function test_jobseeker_views_accepted_application_in_history_with_no_cancel_button(): void
    {
        $jobseeker = User::factory()->jobseeker()->create();
        $job = Job::factory()->create(['title' => 'Barista Senior']);
        $startDate = now()->addDays(4)->startOfDay();

        $application = JobApplication::factory()->for($job)->for($jobseeker, 'user')->create([
            'status' => 'accepted',
            'start_date' => $startDate->toDateString(),
        ]);

        $response = $this->actingAs($jobseeker)->get(route('applications.index'));

        $response->assertOk()
            ->assertSee('Selamat! Anda Resmi Diterima Bekerja 🎉')
            ->assertSee($startDate->translatedFormat('l, d F Y'))
            ->assertDontSee('id="cancelAppForm-'.$application->id.'"', false);
    }

    public function test_jobseeker_can_filter_applications_by_status(): void
    {
        $jobseeker = User::factory()->jobseeker()->create();

        $jobInterview = Job::factory()->create(['title' => 'Videographer Wawancara']);
        JobApplication::factory()->for($jobInterview)->for($jobseeker, 'user')->create([
            'status' => 'interview',
            'interview_date' => now()->addDays(2)->toDateString(),
        ]);

        $jobAccepted = Job::factory()->create(['title' => 'Akuntan Diterima']);
        JobApplication::factory()->for($jobAccepted)->for($jobseeker, 'user')->create([
            'status' => 'accepted',
            'start_date' => now()->addDays(5)->toDateString(),
        ]);

        // 1. Direct to interview filter
        $responseInterview = $this->actingAs($jobseeker)->get(route('applications.index', ['status' => 'interview']));
        $responseInterview->assertOk()
            ->assertSee('Undangan Sesi Wawancara Aktif')
            ->assertSee('Videographer Wawancara')
            ->assertDontSee('Akuntan Diterima');

        // 2. Direct to accepted filter
        $responseAccepted = $this->actingAs($jobseeker)->get(route('applications.index', ['status' => 'accepted']));
        $responseAccepted->assertOk()
            ->assertSee('Daftar Lamaran Resmi Diterima Bekerja')
            ->assertSee('Akuntan Diterima')
            ->assertDontSee('Videographer Wawancara');
    }

    public function test_accepted_candidate_can_submit_resignation_request(): void
    {
        Notification::fake();

        $employer = User::factory()->employer()->create(['business_name' => 'Kedai Kopi Santai']);
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Rian Pratama']);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Head Barista']);

        $application = JobApplication::factory()->for($job)->for($jobseeker, 'user')->create([
            'status' => 'accepted',
        ]);

        $resignationDate = now()->addDays(14)->toDateString();

        $response = $this->actingAs($jobseeker)->post(route('applications.resign', $application), [
            'resignation_date' => $resignationDate,
            'resignation_reason' => 'Melanjutkan Studi / Pendidikan',
            'resignation_notes' => 'Terima kasih atas bimbingan selama bekerja.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'resignation_status' => 'pending',
            'resignation_reason' => 'Melanjutkan Studi / Pendidikan',
            'resignation_notes' => 'Terima kasih atas bimbingan selama bekerja.',
        ]);

        Notification::assertSentTo($employer, ResignationSubmittedNotification::class);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $jobseeker->id,
            'receiver_id' => $employer->id,
        ]);
    }

    public function test_non_accepted_candidate_cannot_submit_resignation_request(): void
    {
        $jobseeker = User::factory()->jobseeker()->create();
        $job = Job::factory()->create();

        $application = JobApplication::factory()->for($job)->for($jobseeker, 'user')->create([
            'status' => 'pending',
        ]);

        $response = $this->actingAs($jobseeker)->post(route('applications.resign', $application), [
            'resignation_date' => now()->addDays(7)->toDateString(),
            'resignation_reason' => 'Alasan lain',
        ]);

        $response->assertForbidden();
    }

    public function test_job_show_displays_ajukan_resign_for_accepted_candidate(): void
    {
        $employer = User::factory()->employer()->create(['business_name' => 'Studio Foto']);
        $jobseeker = User::factory()->jobseeker()->create();
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Fotografer']);

        $application = JobApplication::factory()->for($job)->for($jobseeker, 'user')->create([
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($jobseeker)->get(route('jobs.show', $job));
        $response->assertOk()
            ->assertSee('Laporkan Lowongan Ini')
            ->assertSee('Ajukan Resign');

        // Saat status resign sudah diajukan
        $application->update(['resignation_status' => 'pending']);

        $responsePending = $this->actingAs($jobseeker)->get(route('jobs.show', $job));
        $responsePending->assertOk()
            ->assertSee('Pengajuan resign sedang ditinjau');
    }

    public function test_employer_can_approve_resignation_request_with_custom_message(): void
    {
        Notification::fake();

        $employer = User::factory()->employer()->create(['business_name' => 'Kedai Kopi Bandung']);
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Budi Pratama']);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Barista']);

        $application = JobApplication::factory()->for($job)->for($jobseeker, 'user')->create([
            'status' => 'accepted',
            'resignation_status' => 'pending',
            'resignation_date' => now()->addDays(7)->toDateString(),
            'resignation_reason' => 'Pindah domisili',
        ]);

        $customMessage = 'Terima kasih atas dedikasi dan kerja sama yang sangat baik selama ini.';

        $response = $this->actingAs($employer)->patch(route('employer.applications.resignDecision', $application), [
            'decision' => 'approved',
            'response_message' => $customMessage,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => 'resigned',
            'resignation_status' => 'approved',
        ]);

        Notification::assertSentTo($jobseeker, ResignationDecisionNotification::class, function ($notification) {
            return $notification->decision === 'approved';
        });

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $employer->id,
            'receiver_id' => $jobseeker->id,
        ]);

        // Pastikan pekerja sudah terhapus dari daftar peserta diterima di dashboard
        $responseDashboard = $this->actingAs($employer)->get(route('employer.dashboard'));
        $responseDashboard->assertOk()
            ->assertSee('Belum ada peserta yang diterima')
            ->assertViewHas('acceptedWorkers', fn ($workers) => $workers->isEmpty())
            ->assertViewHas('metrics', fn ($metrics) => $metrics['accepted'] === 0);
    }

    public function test_employer_can_reject_resignation_request_with_custom_message(): void
    {
        Notification::fake();

        $employer = User::factory()->employer()->create(['business_name' => 'Studio Animasi']);
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Siti Aminah']);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Animator 3D']);

        $application = JobApplication::factory()->for($job)->for($jobseeker, 'user')->create([
            'status' => 'accepted',
            'resignation_status' => 'pending',
            'resignation_date' => now()->addDays(5)->toDateString(),
            'resignation_reason' => 'Melanjutkan studi',
        ]);

        $customMessage = 'Mohon selesaikan proyek episode 2 sebelum mengakhiri kerja sama.';

        $response = $this->actingAs($employer)->patch(route('employer.applications.resignDecision', $application), [
            'decision' => 'rejected',
            'response_message' => $customMessage,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'resignation_status' => 'rejected',
        ]);

        Notification::assertSentTo($jobseeker, ResignationDecisionNotification::class, function ($notification) {
            return $notification->decision === 'rejected';
        });
    }

    public function test_employer_sees_resignation_controls_on_dashboard_and_applicants_page(): void
    {
        $employer = User::factory()->employer()->create(['business_name' => 'Hadids Creative Studio']);
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Budi']);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Video Editor']);

        JobApplication::factory()->for($job)->for($jobseeker, 'user')->create([
            'status' => 'accepted',
            'resignation_status' => 'pending',
            'resignation_date' => now()->addDays(7)->toDateString(),
            'resignation_reason' => 'Pindah Tempat Tinggal / Domisili',
        ]);

        // Dashboard
        $responseDashboard = $this->actingAs($employer)->get(route('employer.dashboard'));
        $responseDashboard->assertOk()
            ->assertSee('Pengajuan Resign Masuk')
            ->assertSee('Setujui Resign');

        // Halaman Pelamar
        $responseApplicants = $this->actingAs($employer)->get(route('employer.applications.index'));
        $responseApplicants->assertOk()
            ->assertSee('Pengajuan Pengunduran Diri (Resign) Masuk')
            ->assertSee('Setujui Resign')
            ->assertSee('Tolak Pengajuan');
    }

    public function test_chat_messages_endpoint_returns_pending_resignation_for_employer(): void
    {
        $employer = User::factory()->employer()->create();
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Kandidat Resign']);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Admin Gudang']);

        JobApplication::factory()->for($job)->for($jobseeker, 'user')->create([
            'status' => 'accepted',
            'resignation_status' => 'pending',
            'resignation_date' => now()->addDays(10)->toDateString(),
            'resignation_reason' => 'Alasan Pribadi',
        ]);

        $response = $this->actingAs($employer)->getJson(route('chat.messages', $jobseeker));
        $response->assertOk()
            ->assertJsonPath('pending_resignation.job_title', 'Admin Gudang')
            ->assertJsonPath('pending_resignation.resignation_reason', 'Alasan Pribadi');
    }
}
