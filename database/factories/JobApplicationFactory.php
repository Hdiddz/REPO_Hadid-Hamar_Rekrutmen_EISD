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
}
