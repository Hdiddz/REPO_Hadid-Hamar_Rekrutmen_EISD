<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir masuk (Login).
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi masuk.
     */
    public function login(Request $request): RedirectResponse
    {
        $input = $request->string('email')->trim()->value();
        $password = $request->string('password')->value();

        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email atau nama pengguna wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');
        $isEmail = (bool) filter_var($input, FILTER_VALIDATE_EMAIL);

        // Cari data pengguna menggunakan identitas unik agar akun bernama sama tidak tertukar.
        $user = $isEmail
            ? User::where('email', $input)->first()
            : User::where('username', $input)->first();

        // 1. Kasus Akun Terhapus / Tidak Ditemukan (cukup warning di bawah kolom input, tanpa pop-up)
        if (! $user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun tidak ditemukan. Akun ini belum terdaftar atau telah dihapus dari sistem.'])
                ->onlyInput('email');
        }

        // 2. Kasus Akun Terkena Ban / Pemblokiran
        if ($user->isBanned()) {
            $duration = $user->banned_until
                ? 'sampai '.$user->banned_until->translatedFormat('d F Y, H:i').' WIB'
                : 'secara permanen';
            $reason = $user->ban_reason ?: 'Pelanggaran standar etika rekrutmen KerjaLokal.';

            return redirect()->route('login')
                ->with('account_banned', [
                    'name' => $user->name,
                    'username' => $user->username,
                    'duration' => $duration,
                    'reason' => $reason,
                    'until' => $user->banned_until?->translatedFormat('d F Y, H:i') ?: 'Permanen',
                ])
                ->with('error', "Akun Anda ({$user->name}) sedang dibekukan oleh Administrator {$duration}. Alasan: {$reason}")
                ->withErrors(['email' => "Akun Anda ({$user->name}) sedang dibekukan oleh Administrator {$duration}."])
                ->onlyInput('email');
        }

        // 3. Autentikasi Kredensial Kata Sandi
        if (Auth::attempt(['id' => $user->id, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user, "Selamat datang kembali, {$user->name}!");
        }

        return back()->withErrors([
            'password' => 'Email, nama pengguna, atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan formulir pendaftaran akun (Register).
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun baru.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $base = Str::slug($validated['name'], '_') ?: 'user';
        $candidate = strtolower($base);
        $i = 1;
        while (User::where('username', $candidate)->exists()) {
            $candidate = strtolower($base).'_'.$i++;
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => $candidate,
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'business_name' => $validated['business_name'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user, 'Pendaftaran berhasil! Selamat bergabung di KerjaLokal.');
    }

    /**
     * Proses keluar sistem (Logout).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Anda telah berhasil keluar dari akun.');
    }

    /**
     * Helper pengalihan halaman berdasarkan peran akun.
     */
    protected function redirectBasedOnRole(User $user, ?string $message = null): RedirectResponse
    {
        $redirect = match ($user->role) {
            'employer' => redirect()->route('employer.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('jobs.index'),
        };

        if ($message) {
            $redirect->with('success', $message);
        }

        return $redirect;
    }
}
