<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicantProfileModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_applications_index_renders_applicant_profile_modal_and_clickable_avatar(): void
    {
        $employer = User::factory()->employer()->create();
        $jobseeker = User::factory()->jobseeker()->create([
            'name' => 'Siti Nurhaliza',
            'username' => 'siti_nur',
            'email' => 'siti@example.test',
            'phone' => '081234567890',
            'created_at' => now()->subMonths(3),
        ]);

        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Barista Senior',
            'status' => 'open',
        ]);

        JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $jobseeker->id,
            'status' => 'interview',
        ]);

        $response = $this->actingAs($employer)->get(route('employer.applications.index'));
        $response->assertOk();
        $response->assertSee('id="applicantProfileModal"', false);
        $response->assertSee('openApplicantProfileModal', false);
        $response->assertSee('Siti Nurhaliza');
        $response->assertSee('siti_nur');
        $response->assertSee('Terdaftar Sejak');
        $response->assertSee('Informasi Akun Platform');
    }

    public function test_employer_dashboard_renders_accepted_worker_clickable_avatar_for_profile_modal(): void
    {
        $employer = User::factory()->employer()->create();
        $worker = User::factory()->jobseeker()->create([
            'name' => 'Rian Pratama',
            'username' => 'rian_p',
            'created_at' => now()->subMonths(6),
        ]);

        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Kasir Kafe',
            'status' => 'open',
        ]);

        JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $worker->id,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($employer)->get(route('employer.dashboard'));
        $response->assertOk();
        $response->assertSee('id="applicantProfileModal"', false);
        $response->assertSee('openApplicantProfileModal', false);
        $response->assertSee('Rian Pratama');
        $response->assertSee('rian_p');
    }

    public function test_chat_messages_endpoint_includes_registered_at_in_jobseeker_profile_for_employer(): void
    {
        $employer = User::factory()->employer()->create();
        $jobseeker = User::factory()->jobseeker()->create([
            'name' => 'Ahmad Fauzi',
            'created_at' => now()->subDays(45),
        ]);

        $response = $this->actingAs($employer)->getJson(route('chat.messages', $jobseeker));
        $response->assertOk();
        $response->assertJsonPath('profile.type', 'jobseeker');
        $response->assertJsonPath('profile.name', 'Ahmad Fauzi');
        $this->assertNotNull($response->json('profile.registered_at'));
        $this->assertStringContainsString('yang lalu', $response->json('profile.registered_at'));
    }
}
