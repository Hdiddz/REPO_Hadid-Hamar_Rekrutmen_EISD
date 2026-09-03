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
}
