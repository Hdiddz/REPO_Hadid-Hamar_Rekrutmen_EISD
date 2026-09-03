<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_create_job_and_sync_skill_pivot(): void
    {
        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();
        $skills = Skill::factory()->count(2)->create();

        $response = $this->actingAs($employer)->post(route('employer.jobs.store'), [
            'category_id' => $category->id,
            'title' => 'Barista dan Kasir',
            'description' => 'Melayani pelanggan, meracik minuman, dan menjaga kebersihan area kerja setiap hari.',
            'location' => 'Coblong, Kota Bandung',
            'salary_type' => 'monthly',
            'salary_amount' => 3200000,
            'work_hours_per_day' => 7,
            'skills' => $skills->modelKeys(),
        ]);

        $response->assertRedirect(route('employer.dashboard'))
            ->assertSessionHas('success');
        $job = Job::query()->sole();
        $this->assertSame($employer->id, $job->employer_id);
        $this->assertEqualsCanonicalizing($skills->modelKeys(), $job->skills()->pluck('skills.id')->all());
    }

    public function test_job_hours_cannot_exceed_eight_and_error_is_visible_below_field(): void
    {
        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();
        $skill = Skill::factory()->create();

        $response = $this->actingAs($employer)->followingRedirects()->from(route('employer.jobs.create'))->post(route('employer.jobs.store'), [
            'category_id' => $category->id,
            'title' => 'Kasir',
            'description' => 'Melayani transaksi pelanggan dengan ramah dan membuat pencatatan penjualan harian.',
            'location' => 'Bandung',
            'salary_type' => 'daily',
            'salary_amount' => 150000,
            'work_hours_per_day' => 10,
            'skills' => [$skill->id],
        ]);

        $response->assertOk()
            ->assertSee('Jam kerja harus berada di antara 1 sampai 8 jam per hari.');
    }

    public function test_employer_cannot_edit_another_employers_job(): void
    {
        $owner = User::factory()->employer()->create();
        $intruder = User::factory()->employer()->create();
        $job = Job::factory()->for($owner, 'employer')->create();

        $this->actingAs($intruder)->get(route('employer.jobs.edit', $job))->assertForbidden();
    }

    public function test_employer_can_add_custom_new_skills_when_creating_job(): void
    {
        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($employer)->post(route('employer.jobs.store'), [
            'category_id' => $category->id,
            'title' => 'Motion Designer',
            'description' => 'Mendesain animasi grafis gerak untuk promosi media sosial UMKM secara kreatif dan terstruktur.',
            'location' => 'Bandung',
            'salary_type' => 'monthly',
            'salary_amount' => 4000000,
            'work_hours_per_day' => 7,
            'new_skills' => ['After Effects', 'Cinema 4D'],
        ]);

        $response->assertRedirect(route('employer.dashboard'))
            ->assertSessionHas('success');
        $job = Job::query()->where('title', 'Motion Designer')->firstOrFail();
        $this->assertCount(2, $job->skills);
        $this->assertTrue(Skill::where('name', 'After Effects')->exists());
        $this->assertTrue(Skill::where('name', 'Cinema 4D')->exists());
    }

    public function test_job_detail_page_shows_edit_button_for_owner_and_last_updated_time(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->for($employer, 'employer')->create();

        $response = $this->actingAs($employer)->get(route('jobs.show', $job));

        $response->assertOk()
            ->assertSee('Edit Lowongan Ini')
            ->assertSee(route('employer.jobs.edit', $job))
            ->assertSee('Status Pembaruan Mitra:');
    }

    public function test_employer_can_destroy_skill_via_api(): void
    {
        $employer = User::factory()->employer()->create();
        $skill = Skill::factory()->create(['name' => 'Keahlian Obsolet']);

        $response = $this->actingAs($employer)->deleteJson(route('employer.skills.destroy', $skill));

        $response->assertOk()
            ->assertJson(['success' => true]);
        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }

    public function test_employer_can_close_and_reopen_job_from_detail_page(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->for($employer, 'employer')->create(['status' => 'open']);

        // Shows Tutup Lowongan Ini when job is open
        $this->actingAs($employer)->get(route('jobs.show', $job))
            ->assertOk()
            ->assertSee('Tutup Lowongan Ini')
            ->assertSee('Sedang Dibuka');

        // Employer closes job
        $this->actingAs($employer)->patch(route('employer.jobs.status', $job), ['status' => 'closed'])
            ->assertSessionHas('success');

        $this->assertSame('closed', $job->fresh()->status);

        // Shows Buka Kembali Lowongan Ini when job is closed
        $this->actingAs($employer)->get(route('jobs.show', $job))
            ->assertOk()
            ->assertSee('Buka Kembali Lowongan Ini')
            ->assertSee('Sedang Ditutup');
    }

    public function test_accepted_applicants_are_recorded_and_displayed_on_employer_dashboard(): void
    {
        $employer = User::factory()->employer()->create(['business_name' => 'Kedai Kopi Nusantara']);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Barista Senior']);
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Budi Santoso']);
        $application = JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $jobseeker->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($employer)->patch(route('employer.applications.update', $application), [
            'status' => 'accepted',
        ]);

        $response->assertSessionHas('success');
        $this->assertSame('accepted', $application->fresh()->status);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $employer->id,
            'receiver_id' => $jobseeker->id,
        ]);

        $dashboardResponse = $this->actingAs($employer)->get(route('employer.dashboard'));
        $dashboardResponse->assertOk()
            ->assertSee('Peserta &amp; Tenaga Kerja Diterima', false)
            ->assertSee('Budi Santoso')
            ->assertSee('Barista Senior')
            ->assertSee('Diterima Bekerja')
            ->assertSee('Chat Peserta');
    }

    public function test_employer_can_schedule_interview_with_custom_modal_fields(): void
    {
        $employer = User::factory()->employer()->create(['business_name' => 'Studio Foto Kreatif']);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Videografer']);
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Ahmad Fauzi']);
        $application = JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $jobseeker->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($employer)->patch(route('employer.applications.update', $application), [
            'status' => 'interview',
            'interview_date' => '2026-09-10',
            'interview_time' => '14:00',
            'interview_type' => 'Online via Google Meet',
            'interview_location' => 'https://meet.google.com/abc-defg-hij',
            'interview_notes' => 'Mohon siapkan laptop dan contoh karya video terbaru Anda.',
        ]);

        $response->assertSessionHas('success');
        $this->assertSame('interview', $application->fresh()->status);

        // Verify candidate received chat invitation with schedule
        $chat = ChatMessage::where('receiver_id', $jobseeker->id)->latest('id')->first();
        $this->assertNotNull($chat);
        $this->assertStringContainsString('Undangan Wawancara Kerja', $chat->message);
        $this->assertStringContainsString('14:00 WIB', $chat->message);
        $this->assertStringContainsString('meet.google.com', $chat->message);

        // Verify notification was sent
        $notif = $jobseeker->notifications()->first();
        $this->assertNotNull($notif);
        $this->assertSame('interview', $notif->data['status']);
        $this->assertStringContainsString('wawancara', $notif->data['message']);
    }

    public function test_employer_applications_page_renders_specialized_status_modals(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->for($employer, 'employer')->create();
        $jobseeker = User::factory()->jobseeker()->create();
        JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $jobseeker->id,
        ]);

        $response = $this->actingAs($employer)->get(route('employer.applications.index'));

        $response->assertOk()
            ->assertSee('modalStatusInterview')
            ->assertSee('modalStatusAccepted')
            ->assertSee('modalStatusRejected')
            ->assertSee('modalStatusPending')
            ->assertSee('Atur & Perbarui Status', false);
    }
}
