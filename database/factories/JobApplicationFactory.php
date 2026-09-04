<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_id' => Job::factory(),
            'user_id' => User::factory()->jobseeker(),
            'resume_file' => 'resumes/testing-resume.pdf',
            'note' => fake()->sentence(),
            'status' => 'pending',
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (): array => ['status' => 'accepted']);
    }

    public function interview(): static
    {
        return $this->state(fn (): array => [
            'status' => 'interview',
            'interview_date' => now()->addDays(2)->toDateString(),
            'interview_time' => '10:00',
            'interview_type' => 'Wawancara Langsung',
            'interview_location' => 'Kantor Mitra',
            'interview_notes' => 'Silakan hadir tepat waktu.',
            'interview_status' => 'pending',
        ]);
    }
}
