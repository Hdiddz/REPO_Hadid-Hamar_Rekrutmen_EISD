<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\Job;
use App\Models\JobApplication;
use App\Notifications\ApplicationStatusUpdatedNotification;
use App\Notifications\JobComplianceWarningNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();

        $jobs = Job::query()
            ->with(['employer:id,name,email,phone,business_name,banned_at,banned_until', 'category:id,name'])
            ->withCount([
                'applications',
                'reports' => fn ($q) => $q->visibleToAdmin(),
            ])
            ->when($request->filled('category_id'), function ($query) use ($request): void {
                $query->where('category_id', $request->integer('category_id'));
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $status = $request->string('status')->toString();
                if ($status === 'closed_by_admin') {
                    $query->where('closed_by_admin', true);
                } elseif ($status === 'open') {
                    $query->where('status', 'open');
                } elseif ($status === 'closed') {
                    $query->where('status', 'closed')->where('closed_by_admin', false);
                } elseif ($status === 'reported') {
                    $query->whereHas('reports', fn ($q) => $q->visibleToAdmin());
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

        return view('admin.jobs.index', compact('jobs', 'stats', 'categories'));
    }

    public function show(Request $request, Job $job): View
    {
        $previousUrl = url()->previous();
        if ($request->filled('return_to')) {
            $returnTo = $request->string('return_to')->toString();
            $appUrl = url('/');
            if ((str_starts_with($returnTo, '/') && ! str_starts_with($returnTo, '//')) || str_starts_with($returnTo, $appUrl)) {
                session()->put('admin_jobs_return_to', $returnTo);
            }
        } elseif ($previousUrl && $previousUrl !== $request->fullUrl() && ! str_contains($previousUrl, '/admin/lowongan/'.$job->id)) {
            if (str_starts_with($previousUrl, url('/')) && ! str_contains($previousUrl, 'login') && ! str_contains($previousUrl, 'logout')) {
                session()->put('admin_jobs_return_to', $previousUrl);
            }
        }

        $returnUrl = session('admin_jobs_return_to', route('admin.jobs.index'));

        $job->load([
            'employer:id,name,username,email,phone,business_name,created_at,banned_at,banned_until',
            'category:id,name',
            'skills:id,name',
            'workplacePhotos',
            'applications' => fn ($q) => $q->with('user:id,name,username,email,phone,avatar,created_at,banned_at,banned_until,ban_reason')->latest('id'),
            'reports' => fn ($q) => $q->visibleToAdmin()->with('reporter:id,name,email')->latest('id'),
        ]);

        $job->loadCount([
            'applications',
            'reports' => fn ($q) => $q->visibleToAdmin(),
        ]);

        return view('admin.jobs.show', compact('job', 'returnUrl'));
    }

    public function warn(Request $request, Job $job): RedirectResponse
    {
        $validated = $request->validate([
            'warning_category' => ['required', 'string', 'in:salary_not_standard,excessive_hours,misleading_info,unethical_conditions,other'],
            'warning_message' => ['required', 'string', 'min:5', 'max:2000'],
            'also_close_job' => ['nullable', 'boolean'],
            'duration_type' => ['nullable', 'in:7_days,14_days,30_days,custom,permanent'],
            'custom_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
        ], [
            'warning_category.required' => 'Kategori pelanggaran kepatuhan wajib dipilih.',
            'warning_category.in' => 'Kategori pelanggaran yang dipilih tidak valid.',
            'warning_message.required' => 'Catatan peringatan wajib diisi.',
            'warning_message.min' => 'Catatan peringatan minimal 5 karakter.',
            'warning_message.max' => 'Catatan peringatan maksimal 2000 karakter.',
        ]);

        $alsoClose = $request->boolean('also_close_job');

        $updateData = [
            'admin_warning_category' => $validated['warning_category'],
            'admin_warning_message' => $validated['warning_message'],
            'admin_warned_at' => now(),
        ];

        if ($alsoClose) {
            $closedUntil = match ($validated['duration_type'] ?? 'permanent') {
                '7_days' => now()->addDays(7),
                '14_days' => now()->addDays(14),
                '30_days' => now()->addDays(30),
                'custom' => ! empty($validated['custom_days']) ? now()->addDays((int) $validated['custom_days']) : null,
                default => null,
            };

            $updateData['status'] = 'closed';
            $updateData['closed_by_admin'] = true;
            $updateData['closed_reason'] = 'Ditutup Administrator terkait Peringatan Kepatuhan: '.$validated['warning_message'];
            $updateData['closed_until'] = $closedUntil;
        }

        $job->update($updateData);

        // Send Database Notification to employer
        if ($job->employer) {
            $job->employer->notify(new JobComplianceWarningNotification(
                job: $job,
                category: $validated['warning_category'],
                warningMessage: $validated['warning_message'],
                alsoClosed: $alsoClose,
            ));

            // Send ChatMessage to employer
            $admin = $request->user();
            $categoryLabel = $job->admin_warning_category_label;
            $nowFormatted = now()->translatedFormat('d F Y, H:i');
            $closeInfo = $alsoClose
                ? "\n\n⚠️ Lowongan ini juga telah dinonaktifkan/ditutup sementara hingga perbaikan dilakukan dan disetujui."
                : "\n\nSilakan tinjau dan perbarui rincian lowongan kerja Anda pada menu Lowongan Kerja Saya agar sesuai dengan standar kerja layak.";

            ChatMessage::create([
                'sender_id' => $admin->id,
                'receiver_id' => $job->employer_id,
                'message' => "⚠️ [Peringatan Kepatuhan Lowongan Kerja]\n\nHalo {$job->employer->name}, Administrator telah menerbitkan peringatan untuk lowongan \"{$job->title}\" pada {$nowFormatted} WIB.\n\nKategori: {$categoryLabel}\nCatatan Admin: \"{$validated['warning_message']}\"{$closeInfo}\n\nAnda dapat membalas pesan ini atau mengedit lowongan Anda untuk memenuhi kepatuhan.",
                'is_read' => false,
            ]);
        }

        $notice = $alsoClose
            ? 'Peringatan kepatuhan berhasil dikirimkan dan status lowongan telah ditutup sementara.'
            : 'Peringatan kepatuhan berhasil dikirimkan kepada Mitra UMKM.';

        return redirect()->route('admin.jobs.show', $job)->with('success', $notice);
    }

    public function dismissWarning(Request $request, Job $job): RedirectResponse
    {
        $job->update([
            'admin_warning_category' => null,
            'admin_warning_message' => null,
            'admin_warned_at' => null,
        ]);

        if ($job->employer) {
            $admin = $request->user();
            $nowFormatted = now()->translatedFormat('d F Y, H:i');
            ChatMessage::create([
                'sender_id' => $admin->id,
                'receiver_id' => $job->employer_id,
                'message' => "✅ [Peringatan Kepatuhan Dicabut]\n\nHalo {$job->employer->name}, catatan peringatan kepatuhan untuk lowongan \"{$job->title}\" telah dicabut oleh Administrator pada {$nowFormatted} WIB. Terima kasih atas kepatuhan Anda terhadap standar kerja layak.",
                'is_read' => false,
            ]);
        }

        return redirect()->route('admin.jobs.show', $job)->with('success', 'Peringatan kepatuhan untuk lowongan ini telah dicabut.');
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

        // Perbarui seluruh laporan aktif terkait penutupan lowongan ini menjadi Selesai (resolved)
        $job->reports()
            ->where('status', 'action_taken')
            ->where('action_taken', 'like', '%tutup%')
            ->update([
                'status' => 'resolved',
                'action_taken' => 'Lowongan telah dibuka kembali oleh Admin (Sanksi Selesai)',
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
            'status' => ['required', 'in:pending,reviewed,interview,accepted,rejected,resigned'],
        ]);

        if ($validated['status'] === 'resigned') {
            $application->update([
                'status' => 'resigned',
                'resignation_status' => 'approved',
                'resigned_at' => $application->resigned_at ?? now(),
            ]);
        } else {
            $application->update([
                'status' => $validated['status'],
            ]);
        }

        // Kirim notifikasi pesan ke pelamar kerja
        $admin = $request->user();
        $statusLabels = [
            'pending' => 'Menunggu Tinjauan',
            'reviewed' => 'Sedang Ditinjau',
            'interview' => 'Tahap Wawancara',
            'accepted' => 'Diterima Bekerja 🎉',
            'rejected' => 'Belum Lolos Seleksi',
            'resigned' => 'Resign (Telah Mengundurkan Diri)',
        ];
        $label = $statusLabels[$validated['status']] ?? ucfirst($validated['status']);
        $employerName = $application->job->employer->business_name ?: $application->job->employer->name;

        $application->user->notify(new ApplicationStatusUpdatedNotification($application, $validated['status']));

        ChatMessage::create([
            'sender_id' => $admin->id,
            'receiver_id' => $application->user_id,
            'message' => "📋 [Pembaruan Status Lamaran Kerja]\n\nHalo {$application->user->name}, status lamaran Anda untuk posisi \"{$application->job->title}\" di {$employerName} telah diperbarui menjadi: \"{$label}\" oleh Administrator pengawas KerjaLokal.\n\nSilakan pantau perkembangan lamaran Anda melalui akun KerjaLokal.",
            'is_read' => false,
        ]);

        return back()->with('success', "Status lamaran kandidat {$application->user->name} berhasil diubah menjadi: ".$label);
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
