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

        // Membuka detail bersifat read-only.
        $this->actingAs($admin)->get(route('admin.reports.show', $report))->assertOk();
        $report->refresh();
        $this->assertSame('pending', $report->status);

        $this->actingAs($admin)
            ->patch(route('admin.reports.review', $report))
            ->assertSessionHas('success');
        $this->assertSame('reviewed', $report->fresh()->status);

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

    public function test_employer_cannot_report_own_job(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->for($employer, 'employer')->create();

        $response = $this->actingAs($employer)->post(route('jobs.report', $job), [
            'reason' => 'Laporan milik sendiri',
            'details' => 'Mencoba melaporkan lowongan milik akun sendiri.',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('job_reports', 0);
    }

    public function test_employer_can_report_other_job(): void
    {
        $employer = User::factory()->employer()->create();
        $otherEmployer = User::factory()->employer()->create();
        $job = Job::factory()->for($otherEmployer, 'employer')->create(['status' => 'open']);

        $response = $this->actingAs($employer)->post(route('jobs.report', $job), [
            'reason' => 'Indikasi Penipuan / Lowongan Palsu',
            'details' => 'Lowongan ini terindikasi mencatut nama perusahaan lain secara tidak sah.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('job_reports', [
            'job_id' => $job->id,
            'reporter_id' => $employer->id,
            'status' => 'pending',
        ]);
    }

    public function test_guest_cannot_access_report_history(): void
    {
        $response = $this->get(route('reports.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_is_redirected_to_admin_reports_when_accessing_user_reports(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('reports.index'));

        $response->assertRedirect(route('admin.reports.index'));
    }

    public function test_admin_does_not_see_riwayat_laporan_in_portal_menu(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('Riwayat Laporan');
    }

    public function test_authenticated_user_can_view_own_reports(): void
    {
        $user = User::factory()->jobseeker()->create();
        $otherUser = User::factory()->jobseeker()->create();
        $job = Job::factory()->create();

        $ownReport = JobReport::create([
            'job_id' => $job->id,
            'reporter_id' => $user->id,
            'reason' => 'Indikasi Percaloan / Pungutan Biaya Administrasi',
            'details' => 'Penjelasan laporan milik user yang sedang aktif login.',
            'status' => 'pending',
        ]);

        $otherReport = JobReport::create([
            'job_id' => $job->id,
            'reporter_id' => $otherUser->id,
            'reason' => 'Pelecehan / Diskriminasi SARA',
            'details' => 'Penjelasan laporan milik user lain yang tidak boleh terlihat.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertOk();
        $response->assertSee('Penjelasan laporan milik user yang sedang aktif login.');
        $response->assertDontSee('Penjelasan laporan milik user lain yang tidak boleh terlihat.');
    }

    public function test_user_can_filter_report_history_by_status(): void
    {
        $user = User::factory()->jobseeker()->create();
        $job = Job::factory()->create();

        JobReport::create([
            'job_id' => $job->id,
            'reporter_id' => $user->id,
            'reason' => 'Laporan Pending',
            'details' => 'Laporan yang masih berstatus pending.',
            'status' => 'pending',
        ]);

        JobReport::create([
            'job_id' => $job->id,
            'reporter_id' => $user->id,
            'reason' => 'Laporan Ditindak',
            'details' => 'Laporan yang sudah berstatus action taken.',
            'status' => 'action_taken',
        ]);

        $responsePending = $this->actingAs($user)->get(route('reports.index', ['status' => 'pending']));
        $responsePending->assertOk();
        $responsePending->assertSee('Laporan yang masih berstatus pending.');
        $responsePending->assertDontSee('Laporan yang sudah berstatus action taken.');

        $responseAction = $this->actingAs($user)->get(route('reports.index', ['status' => 'action_taken']));
        $responseAction->assertOk();
        $responseAction->assertSee('Laporan yang sudah berstatus action taken.');
        $responseAction->assertDontSee('Laporan yang masih berstatus pending.');
    }

    public function test_user_can_hide_report_from_history(): void
    {
        $user = User::factory()->jobseeker()->create();
        $otherUser = User::factory()->jobseeker()->create();
        $job = Job::factory()->create();

        $report = JobReport::create([
            'job_id' => $job->id,
            'reporter_id' => $user->id,
            'reason' => 'Laporan Rahasia Pengguna',
            'details' => 'Rincian laporan rahasia yang ingin dihapus dari riwayat.',
            'status' => 'pending',
        ]);

        // Other user cannot hide report
        $this->actingAs($otherUser)
            ->delete(route('reports.destroy', $report))
            ->assertForbidden();

        // Reporter can hide report
        $response = $this->actingAs($user)
            ->delete(route('reports.destroy', $report));

        $response->assertSessionHas('success');
        $this->assertNotNull($report->fresh()->reporter_hidden_at);

        // Report no longer shows in history index
        $this->flushSession();
        $this->actingAs($user)->get(route('reports.index'))
            ->assertOk()
            ->assertDontSee('Laporan Rahasia Pengguna');
    }

    public function test_admin_review_and_action_sends_both_chat_and_notifications_to_users(): void
    {
        $admin = User::factory()->admin()->create();
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->for($employer, 'employer')->create(['status' => 'open', 'title' => 'Staf Operasional']);
        $reporter = User::factory()->jobseeker()->create(['name' => 'Siti Nurhaliza']);

        $report = JobReport::create([
            'job_id' => $job->id,
            'reporter_id' => $reporter->id,
            'reason' => 'Indikasi Percaloan / Pungutan Biaya Administrasi',
            'details' => 'Diminta bayar seratus ribu untuk formulir pendaftaran.',
            'status' => 'pending',
        ]);

        // 1. Admin marks as reviewed
        $this->actingAs($admin)->patch(route('admin.reports.review', $report))
            ->assertSessionHas('success');

        // Check reporter received chat message and database notification for 'reviewed'
        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $admin->id,
            'receiver_id' => $reporter->id,
        ]);
        $this->assertSame(1, $reporter->fresh()->notifications()->count());
        $reviewNotif = $reporter->fresh()->notifications()->first();
        $this->assertSame('Laporan Sedang Ditinjau 🔍', $reviewNotif->data['title']);

        // 2. Admin dismisses the report
        $this->actingAs($admin)->post(route('admin.reports.action', $report), [
            'action_type' => 'dismiss',
            'admin_notes' => 'Tidak terbukti ada pungutan dari pihak mitra.',
        ])->assertRedirect(route('admin.reports.index'));

        // Check reporter received second notification for 'dismissed'
        $notifications = $reporter->fresh()->notifications()->get();
        $this->assertCount(2, $notifications);
        $dismissNotif = $notifications->firstWhere('data.type', 'dismissed');
        $this->assertNotNull($dismissNotif);
        $this->assertSame('Hasil Tinjauan Laporan ℹ️', $dismissNotif->data['title']);
    }
}
