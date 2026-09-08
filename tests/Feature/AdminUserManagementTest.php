<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private const string TEST_PASSWORD = 'test-only-'.'123!';

    public function test_admin_can_view_users_list_and_filter(): void
    {
        $admin = User::factory()->admin()->create();
        $employer = User::factory()->employer()->create(['name' => 'Mitra Usaha Jaya']);
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Pencari Kerja Hebat']);

        $response = $this->actingAs($admin)->get(route('admin.users.index', ['q' => 'Mitra Usaha']));

        $response->assertOk()
            ->assertSee('Mitra Usaha Jaya')
            ->assertDontSee('Pencari Kerja Hebat');
    }

    public function test_admin_can_view_user_detail_and_activities(): void
    {
        $admin = User::factory()->admin()->create();
        $employer = User::factory()->employer()->create(['business_name' => 'Toko Barokah']);
        Job::factory()->for($employer, 'employer')->create(['title' => 'Kasir Toko']);

        $response = $this->actingAs($admin)->get(route('admin.users.show', $employer));

        $response->assertOk()
            ->assertSee('Toko Barokah')
            ->assertSee('Kasir Toko');
    }

    public function test_admin_can_update_user_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->jobseeker()->create();

        $response = $this->actingAs($admin)->put(route('admin.users.password', $user), [
            'password' => 'PasswordBaru123!',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertTrue(Hash::check('PasswordBaru123!', $user->password));

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $admin->id,
            'receiver_id' => $user->id,
            'is_read' => false,
        ]);
    }

    public function test_admin_can_ban_and_unban_user(): void
    {
        $admin = User::factory()->admin()->create();
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->for($employer, 'employer')->create(['status' => 'open']);

        // Ban user with 7 days and close their jobs
        $response = $this->actingAs($admin)->post(route('admin.users.ban', $employer), [
            'ban_reason' => 'Melakukan pungutan liar kepada pelamar.',
            'duration_type' => '7_days',
            'close_jobs' => 1,
        ]);

        $response->assertSessionHas('success');
        $employer->refresh();
        $job->refresh();

        $this->assertTrue($employer->isBanned());
        $this->assertSame('Melakukan pungutan liar kepada pelamar.', $employer->ban_reason);
        $this->assertSame('closed', $job->status);
        $this->assertTrue($job->closed_by_admin);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $admin->id,
            'receiver_id' => $employer->id,
        ]);

        // Banned user cannot login
        auth()->logout();
        $this->post(route('login'), [
            'email' => $employer->email,
            'password' => 'password',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('error');

        // Admin unbans user
        $unbanResponse = $this->actingAs($admin)->post(route('admin.users.unban', $employer));
        $unbanResponse->assertSessionHas('success');
        $employer->refresh();

        $this->assertFalse($employer->isBanned());
        $this->assertNull($employer->banned_at);
        $this->assertNull($employer->ban_reason);
    }

    public function test_admin_can_update_user_account_profile_and_username(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->jobseeker()->create(['username' => 'budi_lama']);

        $response = $this->actingAs($admin)->put(route('admin.users.account', $user), [
            'username' => 'budi_baru',
            'name' => 'Budi Santoso Baru',
            'email' => 'budi_baru@example.com',
            'phone' => '081234567890',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertSame('budi_baru', $user->username);
        $this->assertSame('Budi Santoso Baru', $user->name);
        $this->assertSame('budi_baru@example.com', $user->email);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $admin->id,
            'receiver_id' => $user->id,
            'is_read' => false,
        ]);
    }

    public function test_admin_cannot_update_user_phone_exceeding_13_characters(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->jobseeker()->create(['phone' => '081234567890']);

        $response = $this->actingAs($admin)->put(route('admin.users.account', $user), [
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '08123456789012', // 14 characters
        ]);

        $response->assertSessionHasErrors(['phone']);
        $user->refresh();
        $this->assertSame('081234567890', $user->phone);
    }

    public function test_admin_cannot_assign_taken_username(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['username' => 'user_pertama']);
        $user2 = User::factory()->create(['username' => 'user_kedua']);

        $response = $this->actingAs($admin)->put(route('admin.users.account', $user2), [
            'username' => 'user_pertama',
            'name' => $user2->name,
            'email' => $user2->email,
        ]);

        $response->assertSessionHasErrors('username');
        $user2->refresh();
        $this->assertSame('user_kedua', $user2->username);
    }

    public function test_user_can_update_own_username_in_settings(): void
    {
        $user = User::factory()->jobseeker()->create(['username' => 'pencari_awal']);

        $response = $this->actingAs($user)->put(route('settings.username.update'), [
            'username' => 'pencari_sukses',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertSame('pencari_sukses', $user->username);
    }

    public function test_user_cannot_take_already_used_username_in_settings(): void
    {
        User::factory()->create(['username' => 'super_admin_sdg']);
        $user = User::factory()->jobseeker()->create(['username' => 'pencari_biasa']);

        $response = $this->actingAs($user)->put(route('settings.username.update'), [
            'username' => 'super_admin_sdg',
        ]);

        $response->assertSessionHasErrors('username');
        $user->refresh();
        $this->assertSame('pencari_biasa', $user->username);
    }

    public function test_user_can_login_using_username(): void
    {
        $user = User::factory()->jobseeker()->create([
            'username' => 'hadid_creative',
            'password' => Hash::make(self::TEST_PASSWORD),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'hadid_creative',
            'password' => self::TEST_PASSWORD,
        ]);

        $response->assertRedirect(route('jobs.index'));
        $this->assertAuthenticatedAs($user);
    }
}
