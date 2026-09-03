<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarAndRememberMeTest extends TestCase
{
    use RefreshDatabase;

    private function createFakeImage(string $filename = 'profile.png'): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($filename, $png);
    }

    public function test_user_can_upload_profile_avatar_and_it_is_stored_on_public_disk(): void
    {
        Storage::fake('public');

        $user = User::factory()->jobseeker()->create();
        $file = $this->createFakeImage('profile_1080x1080.png');

        $response = $this->actingAs($user)->post(route('settings.avatar.update'), [
            'avatar' => $file,
        ]);

        $response->assertSessionHas('success');
        $user->refresh();

        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
        $this->assertStringContainsString('storage/'.$user->avatar, $user->avatar_url);
    }

    public function test_uploading_new_avatar_deletes_old_avatar_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->jobseeker()->create();
        $oldFile = $this->createFakeImage('old_avatar.png');

        $this->actingAs($user)->post(route('settings.avatar.update'), [
            'avatar' => $oldFile,
        ]);

        $user->refresh();
        $oldPath = $user->avatar;
        Storage::disk('public')->assertExists($oldPath);

        // Upload second avatar
        $newFile = $this->createFakeImage('new_avatar_1080.png');
        $this->actingAs($user)->post(route('settings.avatar.update'), [
            'avatar' => $newFile,
        ]);

        $user->refresh();
        $this->assertNotSame($oldPath, $user->avatar);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_user_can_remove_their_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->jobseeker()->create();
        $file = $this->createFakeImage('avatar.png');

        $this->actingAs($user)->post(route('settings.avatar.update'), [
            'avatar' => $file,
        ]);

        $user->refresh();
        $path = $user->avatar;
        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($user)->delete(route('settings.avatar.destroy'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNull($user->avatar);
        $this->assertNull($user->avatar_url);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_avatar_upload_fails_for_non_image_files(): void
    {
        Storage::fake('public');

        $user = User::factory()->jobseeker()->create();
        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('settings.avatar.update'), [
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar);
    }

    public function test_login_with_remember_me_generates_remember_token_and_cookie(): void
    {
        $user = User::factory()->jobseeker()->create([
            'email' => 'kandidat@kerjalokal.id',
            'password' => Hash::make('REMOVED_CREDENTIAL'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'kandidat@kerjalokal.id',
            'password' => 'REMOVED_CREDENTIAL',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('jobs.index'));
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertNotNull($user->remember_token);

        // Check recaller cookie is set in response
        $recallerName = Auth::guard()->getRecallerName();
        $response->assertCookie($recallerName);
    }
}
