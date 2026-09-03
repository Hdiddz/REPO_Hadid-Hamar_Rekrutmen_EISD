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
}
