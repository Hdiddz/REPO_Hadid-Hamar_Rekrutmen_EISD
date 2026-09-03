<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user()->loadCount(['jobApplications', 'jobs']);

        return view('profile.index', compact('user'));
    }

    public function settings(Request $request): View
    {
        $user = $request->user();

        return view('settings.index', compact('user'));
    }

    public function updateUsername(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($request->user()->id),
            ],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik (.), tanda hubung (-), dan garis bawah (_).',
            'username.unique' => 'Username ini sudah digunakan oleh akun lain. Silakan pilih username lain.',
        ]);

        $username = strtolower(trim($validated['username']));

        $request->user()->update([
            'username' => $username,
        ]);

        return back()->with('success', "Username berhasil diubah menjadi @{$username}.");
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata sandi berhasil diperbarui dengan aman.');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'avatar.required' => 'Pilih berkas foto untuk diunggah.',
            'avatar.image' => 'Berkas harus berupa gambar yang valid.',
            'avatar.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'avatar.max' => 'Ukuran foto profil maksimal 10 MB.',
        ]);

        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update([
            'avatar' => $path,
        ]);

        return back()->with('success', 'Foto profil (1080x1080) berhasil disimpan dan diperbarui.');
    }

    public function destroyAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update([
            'avatar' => null,
        ]);

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }
}
