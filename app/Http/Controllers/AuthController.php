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
     * Nama cookie perangkat yang diingat.
     */
    public const SAVED_DEVICE_COOKIE = 'kl_saved_device';

    /**
     * Hitung hash verifikasi integritas kredensial perangkat.
     */
    protected function generateDeviceHash(User $user): string
    {
        return hash_hmac('sha256', $user->id.'|'.$user->password, config('app.key'));
    }

    /**
     * Cari dan validasi pengguna yang tersimpan di perangkat ini melalui cookie aman.
     */
    protected function resolveSavedUser(Request $request, bool $allowBanned = false): ?User
    {
        $raw = $request->cookie(self::SAVED_DEVICE_COOKIE);
        if (! $raw) {
            return null;
        }

        $data = json_decode($raw, true);
        if (! is_array($data) || empty($data['id']) || empty($data['hash'])) {
            return null;
        }

        $user = User::find($data['id']);
        if (! $user) {
            return null;
        }

        if (! hash_equals($this->generateDeviceHash($user), (string) $data['hash'])) {
            return null;
        }

        if (! $allowBanned && $user->isBanned()) {
            return null;
        }

        return $user;
    }

    /**
     * Tampilkan formulir masuk (Login).
     */
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $savedUser = $this->resolveSavedUser($request, allowBanned: true);

        return view('auth.login', compact('savedUser'));
    }

    /**
     * Proses autentikasi masuk cepat 1-klik untuk akun tersimpan di perangkat ini.
     */
    public function quickLogin(Request $request): RedirectResponse
    {
        $user = $this->resolveSavedUser($request, allowBanned: true);

        if (! $user) {
            return redirect()->route('login')
                ->withCookie(cookie()->forget(self::SAVED_DEVICE_COOKIE))
                ->withErrors(['email' => 'Sesi akun tersimpan telah kedaluwarsa atau tidak valid. Silakan masuk kembali dengan email dan kata sandi Anda.']);
        }

        // Periksa jika akun dibekukan
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
                ->withErrors(['email' => "Akun Anda ({$user->name}) sedang dibekukan oleh Administrator {$duration}."]);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user, "Selamat datang kembali, {$user->name}!");
    }

    /**
     * Hapus / keluarkan akun tersimpan dari perangkat ini.
     */
    public function forgetDevice(Request $request): RedirectResponse
    {
        return redirect()->route('login')
            ->withCookie(cookie()->forget(self::SAVED_DEVICE_COOKIE))
            ->with('status', 'Akun telah berhasil dikeluarkan dari perangkat ini.');
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

            $redirect = $this->redirectBasedOnRole($user, "Selamat datang kembali, {$user->name}!");

            if ($remember) {
                $payload = json_encode([
                    'id' => $user->id,
                    'hash' => $this->generateDeviceHash($user),
                ]);

                return $redirect->withCookie(cookie(self::SAVED_DEVICE_COOKIE, $payload, 60 * 24 * 30));
            }

            return $redirect->withCookie(cookie()->forget(self::SAVED_DEVICE_COOKIE));
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

        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar dari akun.');
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
