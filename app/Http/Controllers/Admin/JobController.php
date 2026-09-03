<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Skill;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = Job::query()
            ->with(['employer:id,name,email,phone,business_name,banned_at,banned_until', 'category:id,name'])
            ->withCount(['applications', 'reports'])
            ->when($request->filled('status'), function ($query) use ($request): void {
                $status = $request->string('status')->toString();
                if ($status === 'closed_by_admin') {
                    $query->where('closed_by_admin', true);
                } elseif ($status === 'open') {
                    $query->where('status', 'open');
                } elseif ($status === 'closed') {
                    $query->where('status', 'closed')->where('closed_by_admin', false);
                }
            })
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($sub) use ($term): void {
                    $sub->where('title', 'like', $term)
                        ->orWhere('location', 'like', $term)
                        ->orWhereHas('employer', function ($emp) use ($term): void {
                            $emp->where('name', 'like', $term)
                                ->orWhere('business_name', 'like', $term);
                        });
                });
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => Job::count(),
            'open' => Job::where('status', 'open')->count(),
            'closed' => Job::where('status', 'closed')->count(),
            'closed_by_admin' => Job::where('closed_by_admin', true)->count(),
        ];

        return view('admin.jobs.index', compact('jobs', 'stats'));
    }

    public function show(Job $job): View
    {
        $job->load([
            'employer:id,name,username,email,phone,business_name,created_at,banned_at,banned_until',
            'category:id,name',
            'skills:id,name',
            'applications' => fn ($q) => $q->with('user:id,name,username,email,phone')->latest('id'),
            'reports' => fn ($q) => $q->with('reporter:id,name,email')->latest('id'),
        ]);

        $job->loadCount(['applications', 'reports']);

        return view('admin.jobs.show', compact('job'));
    }

    public function edit(Job $job): View
    {
        $categories = Category::orderBy('name')->get();
        $skills = Skill::orderBy('name')->get();
        $job->load('skills:id,name');

        return view('admin.jobs.edit', compact('job', 'categories', 'skills'));
    }

    public function update(Request $request, Job $job): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:20'],
            'location' => ['required', 'string', 'max:100'],
            'salary_type' => ['nullable', 'in:hourly,daily,monthly'],
            'salary_amount' => ['nullable', 'numeric', 'min:0'],
            'work_hours_per_day' => ['required', 'integer', 'min:1', 'max:24'],
            'status' => ['required', 'in:open,closed'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
        ]);

        $job->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'salary_type' => $validated['salary_type'] ?? $job->salary_type,
            'salary_amount' => $validated['salary_amount'] ?? $job->salary_amount,
            'work_hours_per_day' => $validated['work_hours_per_day'],
            'status' => $validated['status'],
        ]);

        $job->skills()->sync($request->input('skills', []));

        // Kirim pesan notifikasi ke Mitra UMKM bahwa lowongannya telah diperbarui oleh Administrator
        $admin = $request->user();
        $nowFormatted = now()->translatedFormat('d F Y, H:i');
        ChatMessage::create([
            'sender_id' => $admin->id,
            'receiver_id' => $job->employer_id,
            'message' => "📢 [Pemberitahuan Penyesuaian Lowongan]\n\nHalo {$job->employer->name}, Administrator telah memperbarui rincian lowongan kerja Anda \"{$job->title}\" pada {$nowFormatted} WIB demi menjamin kesesuaian informasi dan standar kepatuhan kerja layak.\n\nSilakan periksa detailnya pada menu Lowongan Saya. Anda dapat membalas pesan ini jika membutuhkan konfirmasi lebih lanjut.",
            'is_read' => false,
        ]);

        return redirect()->route('admin.jobs.show', $job)->with('success', 'Informasi lowongan berhasil diperbarui oleh administrator.');
    }

    public function close(Request $request, Job $job): RedirectResponse
    {
        $validated = $request->validate([
            'close_reason' => ['required', 'string', 'max:1000'],
            'duration_type' => ['required', 'in:7_days,14_days,30_days,custom,permanent'],
            'custom_days' => ['nullable', 'required_if:duration_type,custom', 'integer', 'min:1', 'max:3650'],
        ], [
            'close_reason.required' => 'Alasan penutupan lowongan wajib diisi.',
            'duration_type.required' => 'Durasi penutupan lowongan wajib dipilih.',
            'custom_days.required_if' => 'Jumlah hari wajib diisi untuk durasi kustom.',
        ]);

        $closedUntil = match ($validated['duration_type']) {
            '7_days' => now()->addDays(7),
            '14_days' => now()->addDays(14),
            '30_days' => now()->addDays(30),
            'custom' => now()->addDays((int) $validated['custom_days']),
            'permanent' => null,
        };

        $job->update([
            'status' => 'closed',
            'closed_by_admin' => true,
            'closed_reason' => trim($validated['close_reason']),
            'closed_until' => $closedUntil,
        ]);

        $durationText = $closedUntil ? "hingga {$closedUntil->translatedFormat('d M Y')}" : 'secara permanen';

        // Kirim pesan notifikasi penutupan lowongan ke Mitra pemilik lowongan
        $admin = $request->user();
        ChatMessage::create([
            'sender_id' => $admin->id,
            'receiver_id' => $job->employer_id,
            'message' => "📋 [Pemberitahuan Penutupan Lowongan]\n\nHalo {$job->employer->name}, lowongan pekerjaan Anda \"{$job->title}\" telah ditutup oleh Administrator {$durationText}.\n• Alasan Penutupan: \"{$validated['close_reason']}\"\n\nPenerimaan lamaran kerja baru untuk lowongan ini sementara dinonaktifkan.",
            'is_read' => false,
        ]);

        return back()->with('success', "Lowongan '{$job->title}' berhasil ditutup oleh administrator {$durationText}.");
    }

    public function reopen(Job $job): RedirectResponse
    {
        $job->update([
            'status' => 'open',
            'closed_by_admin' => false,
            'closed_reason' => null,
            'closed_until' => null,
        ]);

        // Kirim pesan notifikasi pembukaan kembali lowongan ke Mitra
        $admin = auth()->user();
        ChatMessage::create([
            'sender_id' => $admin->id,
            'receiver_id' => $job->employer_id,
            'message' => "✅ [Lowongan Dibuka Kembali]\n\nHalo {$job->employer->name}, lowongan pekerjaan Anda \"{$job->title}\" telah dibuka kembali oleh Administrator dan kini aktif kembali menerima pelamar kerja.",
            'is_read' => false,
        ]);

        return back()->with('success', "Lowongan '{$job->title}' telah dibuka kembali dan aktif menerima pelamar.");
    }

    public function destroy(Job $job): RedirectResponse
    {
        $employer = $job->employer;
        $jobTitle = $job->title;
        $resumeFiles = $job->applications()->pluck('resume_file')->filter()->all();

        // Kirim notifikasi pesan ke mitra sebelum lowongan dihapus
        $admin = auth()->user();
        $nowFormatted = now()->translatedFormat('d F Y, H:i');
        ChatMessage::create([
            'sender_id' => $admin->id,
            'receiver_id' => $employer->id,
            'message' => "🗑️ [Pemberitahuan Penghapusan Lowongan]\n\nHalo {$employer->name}, lowongan pekerjaan Anda \"{$jobTitle}\" telah dihapus dari platform KerjaLokal oleh Administrator pada {$nowFormatted} WIB karena pertimbangan kesesuaian sistem atau pelanggaran standar kepatuhan.\n\nSeluruh data pelamar pada lowongan tersebut telah dibersihkan secara aman.",
            'is_read' => false,
        ]);

        DB::transaction(fn () => $job->delete());

        if (! empty($resumeFiles)) {
            Storage::disk('local')->delete($resumeFiles);
        }

        return redirect()->route('admin.jobs.index')->with('success', "Lowongan '{$jobTitle}' dan data lamarannya berhasil dihapus.");
    }

    public function updateApplicationStatus(Request $request, JobApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,reviewed,accepted,rejected'],
        ]);

        $application->update([
            'status' => $validated['status'],
        ]);

        // Kirim notifikasi pesan ke pelamar kerja
        $admin = $request->user();
        $statusLabels = [
            'pending' => 'Menunggu Tinjauan',
            'reviewed' => 'Sedang Ditinjau',
            'accepted' => 'Diterima Bekerja 🎉',
            'rejected' => 'Belum Lolos Seleksi',
        ];
        $label = $statusLabels[$validated['status']] ?? ucfirst($validated['status']);
        $employerName = $application->job->employer->business_name ?: $application->job->employer->name;

        ChatMessage::create([
            'sender_id' => $admin->id,
            'receiver_id' => $application->user_id,
            'message' => "📋 [Pembaruan Status Lamaran Kerja]\n\nHalo {$application->user->name}, status lamaran Anda untuk posisi \"{$application->job->title}\" di {$employerName} telah diperbarui menjadi: \"{$label}\" oleh Administrator pengawas KerjaLokal.\n\nSilakan pantau perkembangan lamaran Anda melalui akun KerjaLokal.",
            'is_read' => false,
        ]);

        return back()->with('success', "Status lamaran kandidat {$application->user->name} berhasil diubah menjadi: ".ucfirst($validated['status']));
    }

    public function updateStatus(Request $request, Job $job): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,closed'],
        ]);

        $job->update([
            'status' => $validated['status'],
            'closed_by_admin' => $validated['status'] === 'closed',
            'closed_reason' => $validated['status'] === 'closed' ? 'Ditutup oleh administrator.' : null,
            'closed_until' => null,
        ]);

        // Kirim pesan notifikasi ke pemilik lowongan
        $admin = $request->user();
        $statusAction = $validated['status'] === 'open' ? 'dibuka kembali' : 'ditutup';
        ChatMessage::create([
            'sender_id' => $admin->id,
            'receiver_id' => $job->employer_id,
            'message' => "📢 [Perubahan Status Lowongan]\n\nHalo {$job->employer->name}, status publikasi lowongan pekerjaan Anda \"{$job->title}\" telah {$statusAction} oleh Administrator KerjaLokal.",
            'is_read' => false,
        ]);

        return back()->with('success', 'Status lowongan berhasil diperbarui.');
    }
}
