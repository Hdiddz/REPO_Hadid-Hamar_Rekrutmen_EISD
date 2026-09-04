<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_admin_and_ten_demo_employers_with_jobs(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'Hadid@adm.id')->first();
        $this->assertNotNull($admin);
        $this->assertSame('Hadid', $admin->username);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('REMOVED_CREDENTIAL', $admin->password));

        $usernames = collect(range(1, 10))->map(fn (int $i): string => 'johan'.$i);
        $employers = User::whereIn('username', $usernames)->get();
        $this->assertCount(10, $employers);
        $this->assertSame(10, Job::where('status', 'open')->count());
    }
}
