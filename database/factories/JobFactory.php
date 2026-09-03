<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employer_id' => User::factory()->employer(),
            'category_id' => Category::factory(),
            'title' => fake()->randomElement(['Barista', 'Kasir Toko', 'Staf Gudang', 'Admin UMKM']),
            'description' => fake()->paragraphs(2, true),
            'location' => fake()->city().', Jawa Barat',
            'salary_type' => 'monthly',
            'salary_amount' => fake()->numberBetween(2500000, 5000000),
            'work_hours_per_day' => fake()->numberBetween(6, 8),
            'status' => 'open',
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (): array => ['status' => 'closed']);
    }
}
