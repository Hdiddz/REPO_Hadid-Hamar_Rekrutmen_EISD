<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllPagesRenderingSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_guest_pages_render_successfully(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->create(['employer_id' => $employer->id]);

        $homeResponse = $this->get(route('home'))->assertOk();
        $homeResponse->assertSee('Login');
        $homeResponse->assertSee('Register');
        $homeResponse->assertSee('home-search');
        $this->get(route('jobs.index'))->assertOk();
        $this->get(route('jobs.show', $job))->assertOk();
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }

    public function test_home_page_featured_jobs_carousel_renders_slides(): void
    {
        $employer = User::factory()->employer()->create(['business_name' => 'Mitra Usaha']);
        Job::factory()->create(['employer_id' => $employer->id, 'title' => 'Admin Penjualan']);
        Job::factory()->create(['employer_id' => $employer->id, 'title' => 'Operator Percetakan']);

        $response = $this->get(route('home'))->assertOk();
        $response->assertSee('featured-cover-slide');
        $response->assertSee('transition-opacity');
        $response->assertSee('Admin Penjualan');
        $response->assertSee('Operator Percetakan');
        $response->assertDontSee('featuredCarouselProgress');
        $response->assertDontSee('1 / 2');
    }

    public function test_jobseeker_pages_render_successfully(): void
    {
        $jobseeker = User::factory()->jobseeker()->create();
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->create(['employer_id' => $employer->id]);
        $application = JobApplication::factory()->create([
            'user_id' => $jobseeker->id,
            'job_id' => $job->id,
        ]);

        $this->actingAs($jobseeker)->get(route('profile.index'))->assertOk();
        $this->actingAs($jobseeker)->get(route('settings.index'))->assertOk();
        $this->actingAs($jobseeker)->get(route('applications.index'))->assertOk();
        $this->actingAs($jobseeker)->get(route('chat.index'))->assertOk();
        $this->actingAs($jobseeker)->get(route('jobs.show', $job))->assertOk();
    }

    public function test_employer_pages_render_successfully(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->create(['employer_id' => $employer->id]);
        $jobseeker = User::factory()->jobseeker()->create();
        JobApplication::factory()->create([
            'user_id' => $jobseeker->id,
            'job_id' => $job->id,
        ]);

        $this->actingAs($employer)->get(route('employer.dashboard'))->assertOk();
        $this->actingAs($employer)->get(route('employer.jobs.create'))->assertOk();
        $this->actingAs($employer)->get(route('employer.jobs.edit', $job))->assertOk();
        $this->actingAs($employer)->get(route('employer.applications.index'))->assertOk();
        $this->actingAs($employer)->get(route('settings.index'))->assertOk();
        $this->actingAs($employer)->get(route('chat.index'))->assertOk();
        $this->actingAs($employer)->get(route('jobs.show', $job))->assertOk();
    }

    public function test_admin_pages_render_successfully(): void
    {
        $admin = User::factory()->admin()->create();
        $employer = User::factory()->employer()->create();
        $jobseeker = User::factory()->jobseeker()->create();
        $category = Category::factory()->create();
        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'category_id' => $category->id,
        ]);
        $report = JobReport::create([
            'job_id' => $job->id,
            'reporter_id' => $jobseeker->id,
            'reason' => 'Indikasi penipuan',
            'details' => 'Lowongan meminta biaya pendaftaran yang tidak wajar.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Master data');
        $this->actingAs($admin)->get(route('admin.jobs.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.jobs.show', $job))->assertOk();
        $this->actingAs($admin)->get(route('admin.jobs.edit', $job))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.show', $employer))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.show', $jobseeker))->assertOk();
        $this->actingAs($admin)->get(route('admin.reports.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.reports.show', $report))->assertOk();
        $this->actingAs($admin)->get(route('admin.master'))->assertOk();
        $this->actingAs($admin)->get(route('settings.index'))->assertOk();
        $this->actingAs($admin)->get(route('chat.index'))->assertOk();
    }
}
