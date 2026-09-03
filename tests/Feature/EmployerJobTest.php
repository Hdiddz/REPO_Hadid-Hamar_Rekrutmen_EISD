<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Job;
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
}
