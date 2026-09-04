<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsernameCooldownTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_change_username_initially_and_records_username_changed_at(): void
    {
        $user = User::factory()->create([
            'username' => 'original_user',
            'username_changed_at' => null,
        ]);

        $response = $this->actingAs($user)->put(route('settings.username.update'), [
            'username' => 'updated_user',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();

        $this->assertSame('updated_user', $user->username);
        $this->assertNotNull($user->username_changed_at);
        $this->assertFalse($user->canChangeUsername());
        $this->assertGreaterThanOrEqual(6, $user->daysUntilUsernameChange());
    }

    public function test_user_cannot_change_username_during_7_day_cooldown(): void
    {
        $user = User::factory()->create([
            'username' => 'user_on_cooldown',
            'username_changed_at' => now()->subDays(2),
        ]);

        $response = $this->actingAs($user)->put(route('settings.username.update'), [
            'username' => 'another_change',
        ]);

        $response->assertSessionHasErrors('username');
        $user->refresh();

        $this->assertSame('user_on_cooldown', $user->username);
    }

    public function test_user_can_change_username_after_7_day_cooldown_expires(): void
    {
        $user = User::factory()->create([
            'username' => 'cooldown_expired_user',
            'username_changed_at' => now()->subDays(8),
        ]);

        $this->assertTrue($user->canChangeUsername());

        $response = $this->actingAs($user)->put(route('settings.username.update'), [
            'username' => 'new_fresh_username',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();

        $this->assertSame('new_fresh_username', $user->username);
        $this->assertTrue($user->username_changed_at->isToday());
        $this->assertFalse($user->canChangeUsername());
    }

    public function test_submitting_same_username_does_not_trigger_cooldown_or_error(): void
    {
        $user = User::factory()->create([
            'username' => 'same_user',
            'username_changed_at' => null,
        ]);

        $response = $this->actingAs($user)->put(route('settings.username.update'), [
            'username' => 'same_user',
        ]);

        $response->assertSessionHas('info');
        $user->refresh();

        $this->assertSame('same_user', $user->username);
        $this->assertNull($user->username_changed_at);
        $this->assertTrue($user->canChangeUsername());
    }

    public function test_settings_page_displays_cooldown_alert_when_locked(): void
    {
        $user = User::factory()->create([
            'username' => 'locked_user',
            'username_changed_at' => now()->subDays(3),
        ]);

        $response = $this->actingAs($user)->get(route('settings.index'));
        $response->assertOk();
        $response->assertSee('Masa Cooldown Ganti Username Aktif (7 Hari)');
        $response->assertSee('Cooldown 7 Hari (Terkunci)');
    }
}
