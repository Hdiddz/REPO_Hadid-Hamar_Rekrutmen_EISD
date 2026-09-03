<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOversightTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_job_detail_employer_account_and_applicants(): void
    {
        $admin = User::factory()->admin()->create();
        $employer = User::factory()->employer()->create([
            'name' => 'Hendra Wijaya',
            'email' => 'hendra@example.test',
            'business_name' => 'Kedai Sudut Temu',
        ]);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Barista Lokal']);
        $applicant = User::factory()->jobseeker()->create(['name' => 'Budi Santoso']);
        JobApplication::factory()->for($job)->for($applicant, 'user')->create();

        $this->actingAs($admin)->get(route('admin.jobs.show', $job))
            ->assertOk()
            ->assertSee('Barista Lokal')
            ->assertSee('Kedai Sudut Temu')
            ->assertSee('hendra@example.test')
            ->assertSee('Budi Santoso');
    }

    public function test_admin_can_close_a_job(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->create(['status' => 'open']);

        $this->actingAs($admin)->patch(route('admin.jobs.status', $job), [
            'status' => 'closed',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('jobs', ['id' => $job->id, 'status' => 'closed']);
    }

    public function test_admin_dashboard_maintains_clean_metrics_overview(): void
    {
        $admin = User::factory()->admin()->create();
        $employer = User::factory()->employer()->create(['business_name' => 'Kopi Nusantara']);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Barista Senior']);
        $worker = User::factory()->jobseeker()->create(['name' => 'Fajar Nugraha']);

        JobApplication::factory()->for($job)->for($worker, 'user')->create([
            'status' => 'resigned',
            'resignation_status' => 'approved',
            'resignation_reason' => 'Melanjutkan studi S2 ke luar kota',
            'resigned_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Lowongan aktif')
            ->assertSee('Pekerja diterima')
            ->assertDontSee('Pemantauan Pengunduran Diri (Resign) Pekerja');
    }

    public function test_admin_can_see_resignation_details_on_job_and_user_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $employer = User::factory()->employer()->create(['business_name' => 'Bengkel Mobil Maju']);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Teknisi Mesin']);
        $worker = User::factory()->jobseeker()->create(['name' => 'Rian Hidayat']);

        $application = JobApplication::factory()->for($job)->for($worker, 'user')->create([
            'status' => 'resigned',
            'resignation_status' => 'approved',
            'resignation_reason' => 'Mendapatkan tawaran wirausaha keluarga',
            'resigned_at' => now(),
        ]);

        // Cek pada halaman detail lowongan admin (hanya melihat informasi & resume, tanpa kontrol ubah status)
        $responseJob = $this->actingAs($admin)->get(route('admin.jobs.show', $job));
        $responseJob->assertOk()
            ->assertSee('Rian Hidayat')
            ->assertSee('Telah Resign')
            ->assertSee('Mendapatkan tawaran wirausaha keluarga')
            ->assertDontSee('Ubah');

        // Cek pada halaman profil pengguna admin
        $responseUser = $this->actingAs($admin)->get(route('admin.users.show', $worker));
        $responseUser->assertOk()
            ->assertSee('Teknisi Mesin')
            ->assertSee('Telah Resign')
            ->assertSee('Mendapatkan tawaran wirausaha keluarga');

        // Admin juga dapat mengupdate status lamaran ke resigned
        $responseStatus = $this->actingAs($admin)->patch(route('admin.applications.status', $application), [
            'status' => 'resigned',
        ]);
        $responseStatus->assertSessionHas('success');

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => 'resigned',
            'resignation_status' => 'approved',
        ]);
    }
}
