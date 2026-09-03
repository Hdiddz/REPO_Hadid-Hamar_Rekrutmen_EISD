<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->withCount(['jobs', 'jobApplications'])
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->when($request->filled('status'), function ($query) use ($request): void {
                if ($request->string('status')->toString() === 'banned') {
                    $query->whereNotNull('banned_at')
                        ->where(fn ($q) => $q->whereNull('banned_until')->orWhere('banned_until', '>', now()));
                } elseif ($request->string('status')->toString() === 'active') {
                    $query->where(fn ($q) => $q->whereNull('banned_at')->orWhere('banned_until', '<=', now()));
                }
            })
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->where(function ($nested) use ($term): void {
                    $nested->where('name', 'like', "%{$term}%")
                        ->orWhere('username', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('business_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load([
            'jobs' => fn ($q) => $q->withCount('applications')->latest(),
            'jobs.category',
            'jobApplications' => fn ($q) => $q->with('job.employer')->latest(),
            'submittedReports' => fn ($q) => $q->with('job')->latest(),
        ])->loadCount(['jobs', 'jobApplications']);

        return view('admin.users.show', compact('user'));
    }

    public function updateAccount(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:25'],
            'business_name' => ['nullable', 'string', 'max:255'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik (.), tanda hubung (-), dan garis bawah (_).',
            'username.unique' => 'Username ini sudah digunakan oleh akun lain. Silakan pilih username lain.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar pada akun lain.',
        ]);

        $oldUsername = $user->username;
        $oldName = $user->name;
        $oldEmail = $user->email;
        $oldPhone = $user->phone;
        $oldBusiness = $user->business_name;

        $validated['username'] = strtolower(trim($validated['username']));
        $user->update($validated);

        $admin = $request->user();
        $changes = [];
        if ($oldUsername !== $user->username) {
            $changes[] = "• Username: @{$oldUsername} ➔ @{$user->username}";
        }
        if ($oldName !== $user->name) {
            $changes[] = "• Nama Lengkap: {$oldName} ➔ {$user->name}";
        }
        if ($oldEmail !== $user->email) {
            $changes[] = "• Alamat Email: {$oldEmail} ➔ {$user->email}";
        }
        if ($user->phone !== $oldPhone) {
            $changes[] = '• Nomor Telepon: '.($user->phone ?: '(dikosongkan)');
        }
        if ($user->business_name !== $oldBusiness) {
            $changes[] = '• Nama Usaha / Brand: '.($user->business_name ?: '(dikosongkan)');
        }

        $changesText = count($changes) > 0 ? implode("\n", $changes) : '• Informasi profil akun diperbarui';
        $nowFormatted = now()->translatedFormat('d F Y, H:i');

        ChatMessage::create([
            'sender_id' => $admin->id,
            'receiver_id' => $user->id,
            'message' => "📢 [Pemberitahuan Sistem KerjaLokal]\n\nHalo {$user->name}, Administrator sistem telah memperbarui data akun Anda pada {$nowFormatted} WIB:\n{$changesText}\n\nPembaruan ini dilakukan demi menjaga kerapian dan keamanan akun Anda. Jika terdapat pertanyaan atau kekeliruan, Anda dapat membalas pesan ini langsung kepada Administrator.",
            'is_read' => false,
        ]);

        return redirect()->route('admin.users.show', $user)->with('success', "Informasi profil dan username untuk akun @{$user->username} berhasil diperbarui.");
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', Password::min(8)],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $admin = $request->user();
        $nowFormatted = now()->translatedFormat('d F Y, H:i');

        ChatMessage::create([
            'sender_id' => $admin->id,
            'receiver_id' => $user->id,
            'message' => "🔐 [Pembaruan Keamanan Kata Sandi]\n\nHalo {$user->name}, Administrator sistem KerjaLokal telah mengatur ulang (reset) kata sandi untuk akun Anda pada {$nowFormatted} WIB.\n\nSilakan masuk kembali menggunakan kata sandi baru yang telah dikoordinasikan. Demi menjaga keamanan akun, Anda disarankan untuk memperbarui kembali kata sandi melalui menu Pengaturan akun Anda.",
            'is_read' => false,
        ]);

        return redirect()->route('admin.users.show', $user)->with('success', "Kata sandi untuk pengguna {$user->name} berhasil diperbarui.");
    }

    public function ban(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Administrator tidak dapat memblokir akun sendiri.');
        }

        if ($user->hasRole('admin')) {
            return back()->with('error', 'Akun administrator tidak dapat diblokir.');
        }

        $validated = $request->validate([
            'ban_reason' => ['required', 'string', 'max:1000'],
            'duration_type' => ['required', 'in:3_days,7_days,14_days,30_days,custom,permanent'],
            'custom_days' => ['nullable', 'required_if:duration_type,custom', 'integer', 'min:1', 'max:3650'],
            'close_jobs' => ['nullable', 'boolean'],
        ], [
            'ban_reason.required' => 'Alasan pemblokiran akun wajib diisi.',
            'duration_type.required' => 'Durasi pemblokiran wajib dipilih.',
            'custom_days.required_if' => 'Jumlah hari wajib diisi untuk durasi kustom.',
            'custom_days.min' => 'Jumlah hari minimal 1 hari.',
        ]);

        $bannedUntil = match ($validated['duration_type']) {
            '3_days' => now()->addDays(3),
            '7_days' => now()->addDays(7),
            '14_days' => now()->addDays(14),
            '30_days' => now()->addDays(30),
            'custom' => now()->addDays((int) $validated['custom_days']),
            'permanent' => null,
        };

        $user->update([
            'banned_at' => now(),
            'banned_until' => $bannedUntil,
            'ban_reason' => trim($validated['ban_reason']),
        ]);

        if ($request->boolean('close_jobs') && $user->hasRole('employer')) {
            Job::query()
                ->where('employer_id', $user->id)
                ->where('status', 'open')
                ->update([
                    'status' => 'closed',
                    'closed_by_admin' => true,
                    'closed_reason' => 'Ditutup otomatis karena akun mitra UMKM sedang dibekukan oleh Administrator.',
                    'closed_until' => $bannedUntil,
                ]);
        }

        $durationText = $bannedUntil ? "selama {$bannedUntil->diffInDays(now())} hari (hingga {$bannedUntil->translatedFormat('d M Y')})" : 'secara permanen';

        $admin = $request->user();
        ChatMessage::create([
            'sender_id' => $admin->id,
            'receiver_id' => $user->id,
            'message' => "⚠️ [Pemberitahuan Sanksi Pembekuan Akun]\n\nHalo {$user->name}, akun Anda telah dibekukan sementara oleh Administrator KerjaLokal.\n• Durasi Sanksi: {$durationText}\n• Alasan Pembekuan: \"{$validated['ban_reason']}\"\n\nSelama masa sanksi berlaku, akses masuk akun dan penerbitan lowongan dinonaktifkan sesuai standar etika rekrutmen KerjaLokal.",
            'is_read' => false,
        ]);

        return redirect()->route('admin.users.show', $user)->with('success', "Akun {$user->name} berhasil dibekukan {$durationText}.");
    }

    public function unban(User $user): RedirectResponse
    {
        $user->update([
            'banned_at' => null,
            'banned_until' => null,
            'ban_reason' => null,
        ]);

        $admin = auth()->user();
        $nowFormatted = now()->translatedFormat('d F Y, H:i');

        ChatMessage::create([
            'sender_id' => $admin->id,
            'receiver_id' => $user->id,
            'message' => "✅ [Pemulihan Akun dari Administrator]\n\nHalo {$user->name}, status pembekuan akun Anda telah resmi dibuka oleh Administrator pada {$nowFormatted} WIB.\n\nSeluruh akses masuk dan operasional akun Anda kini telah dipulihkan dan dapat digunakan kembali secara normal.",
            'is_read' => false,
        ]);

        return redirect()->route('admin.users.show', $user)->with('success', "Blokir akun {$user->name} telah dibuka. Pengguna kini dapat kembali mengakses sistem.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Administrator tidak dapat menghapus akun sendiri.');
        }

        if ($user->hasRole('admin')) {
            return back()->with('error', 'Akun administrator lain tidak dapat dihapus melalui halaman ini.');
        }

        $resumeFiles = JobApplication::query()
            ->where('user_id', $user->id)
            ->orWhereHas('job', fn ($query) => $query->where('employer_id', $user->id))
            ->pluck('resume_file')
            ->unique()
            ->all();

        DB::transaction(fn () => $user->delete());
        Storage::disk('local')->delete($resumeFiles);

        return back()->with('success', 'Akun pengguna dan data terkait berhasil dihapus.');
    }
}
