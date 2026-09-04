<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_registration_requires_business_name_and_shows_field_error(): void
    {
        $response = $this->followingRedirects()->from(route('register'))->post(route('register'), [
            'name' => 'Hendra Wijaya',
            'email' => 'hendra@example.test',
            'role' => 'employer',
            'phone' => '081234567890',
            'password' => 'REMOVED_CREDENTIAL',
            'password_confirmation' => 'REMOVED_CREDENTIAL',
        ]);

        $response->assertOk()
            ->assertSee('Nama usaha wajib diisi untuk mitra UMKM.');
    }

    public function test_registration_assigns_employer_role_and_redirects_to_its_dashboard(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Hendra Wijaya',
            'email' => 'hendra@example.test',
            'role' => 'employer',
            'phone' => '081234567890',
            'business_name' => 'Kedai Uji',
            'password' => 'REMOVED_CREDENTIAL',
            'password_confirmation' => 'REMOVED_CREDENTIAL',
        ]);

        $response->assertRedirect(route('employer.dashboard'))
            ->assertSessionHas('success');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'hendra@example.test',
            'role' => 'employer',
            'business_name' => 'Kedai Uji',
        ]);
    }

    public function test_each_role_is_blocked_from_other_role_portals(): void
    {
        $jobseeker = User::factory()->jobseeker()->create();
        $employer = User::factory()->employer()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($jobseeker)->get(route('employer.dashboard'))->assertForbidden();
        $this->actingAs($jobseeker)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($employer)->get(route('applications.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('employer.dashboard'))->assertForbidden();
    }

    public function test_authenticated_layout_contains_bootstrap_logout_modal_and_flash_below_navbar(): void
    {
        $user = User::factory()->jobseeker()->create();

        $response = $this->actingAs($user)
            ->withSession(['success' => 'Operasi berhasil.'])
            ->get(route('jobs.index'));

        $response->assertOk()
            ->assertSee('data-bs-target="#logoutModal"', false)
            ->assertSee('class="modal fade" id="logoutModal"', false)
            ->assertSee('Operasi berhasil.');

        $content = $response->getContent();
        $this->assertLessThan(strpos($content, 'Operasi berhasil.'), strpos($content, '</header>'));
    }

    public function test_user_can_update_password_from_settings_page(): void
    {
        $user = User::factory()->employer()->create([
            'password' => Hash::make('REMOVED_CREDENTIAL'),
        ]);

        $response = $this->actingAs($user)->put(route('settings.password.update'), [
            'current_password' => 'REMOVED_CREDENTIAL',
            'password' => 'newREMOVED_CREDENTIAL',
            'password_confirmation' => 'newREMOVED_CREDENTIAL',
        ]);

        $response->assertRedirect()->assertSessionHas('success');
        $this->assertTrue(Hash::check('newREMOVED_CREDENTIAL', $user->fresh()->password));
    }

    public function test_banned_user_attempting_login_receives_banned_notice_and_session(): void
    {
        $bannedUser = User::factory()->jobseeker()->create([
            'email' => 'banned@example.test',
            'username' => 'banned_user',
            'banned_at' => now()->subDay(),
            'banned_until' => now()->addDays(3),
            'ban_reason' => 'Melanggar aturan komunitas.',
            'password' => Hash::make('REMOVED_CREDENTIAL'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'banned@example.test',
            'password' => 'REMOVED_CREDENTIAL',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('account_banned');
        $this->assertGuest();
    }

    public function test_deleted_or_non_existent_account_shows_inline_warning_without_popup(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'deleted_account@example.test',
            'password' => 'REMOVED_CREDENTIAL',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email' => 'Akun tidak ditemukan. Akun ini belum terdaftar atau telah dihapus dari sistem.']);
        $response->assertSessionMissing('account_not_found');
        $this->assertGuest();
    }

    public function test_wrong_password_shows_generic_credential_warning(): void
    {
        $user = User::factory()->jobseeker()->create([
            'email' => 'user@example.test',
            'password' => Hash::make('correct_password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'user@example.test',
            'password' => 'wrong_password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'password' => 'Email, nama pengguna, atau kata sandi yang Anda masukkan salah.',
        ]);
        $this->assertGuest();
    }

    public function test_display_name_cannot_be_used_as_non_unique_login_identifier(): void
    {
        User::factory()->jobseeker()->create([
            'name' => 'Nama Sama',
            'username' => 'akun_pertama',
            'password' => Hash::make('REMOVED_CREDENTIAL'),
        ]);
        User::factory()->jobseeker()->create([
            'name' => 'Nama Sama',
            'username' => 'akun_kedua',
            'password' => Hash::make('password456'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'Nama Sama',
            'password' => 'REMOVED_CREDENTIAL',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_jobseeker_is_redirected_to_jobs_page_from_home(): void
    {
        $jobseeker = User::factory()->jobseeker()->create();

        $response = $this->actingAs($jobseeker)->get(route('home'));

        $response->assertRedirect(route('jobs.index'));
    }

    public function test_authenticated_employer_is_redirected_to_dashboard_from_home(): void
    {
        $employer = User::factory()->employer()->create();

        $response = $this->actingAs($employer)->get(route('home'));

        $response->assertRedirect(route('employer.dashboard'));
    }

    public function test_registration_rejects_phone_number_exceeding_13_characters(): void
    {
        $response = $this->from(route('register'))->post(route('register'), [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.test',
            'role' => 'jobseeker',
            'phone' => '08123456789012', // 14 digits
            'password' => 'REMOVED_CREDENTIAL',
            'password_confirmation' => 'REMOVED_CREDENTIAL',
        ]);

        $response->assertRedirect(route('register'))
            ->assertSessionHasErrors(['phone']);
        $this->assertGuest();
    }

    public function test_registration_accepts_phone_number_up_to_13_characters(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso13@example.test',
            'role' => 'jobseeker',
            'phone' => '0812345678901', // 13 characters
            'password' => 'REMOVED_CREDENTIAL',
            'password_confirmation' => 'REMOVED_CREDENTIAL',
        ]);

        $response->assertRedirect(route('jobs.index'))
            ->assertSessionHas('success');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'budi.santoso13@example.test',
            'phone' => '0812345678901',
        ]);
    }

    public function test_login_page_does_not_mention_hadids_in_placeholder(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertDontSee('atau hadids');
        $response->assertDontSee('hadids');
        $response->assertSee('placeholder="nama@email.com"', false);
    }
}
