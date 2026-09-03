<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
