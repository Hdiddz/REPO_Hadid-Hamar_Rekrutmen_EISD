<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Job;
use App\Models\JobReport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $reports = JobReport::query()
            ->with(['job.employer', 'reporter'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->where(function ($nested) use ($term): void {
                    $nested->where('reason', 'like', "%{$term}%")
                        ->orWhere('details', 'like', "%{$term}%")
                        ->orWhereHas('job', fn ($j) => $j->where('title', 'like', "%{$term}%"))
                        ->orWhereHas('reporter', fn ($r) => $r->where('name', 'like', "%{$term}%"));
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'pending' => JobReport::where('status', 'pending')->count(),
            'action_taken' => JobReport::where('status', 'action_taken')->count(),
            'dismissed' => JobReport::where('status', 'dismissed')->count(),
        ];

        return view('admin.reports.index', compact('reports', 'counts'));
    }

    public function show(JobReport $report): View
    {
        $report->load([
            'job.employer',
            'job.category',
            'reporter',
        ]);

        if ($report->status === 'pending') {
            $report->update(['status' => 'reviewed']);
        }

        return view('admin.reports.show', compact('report'));
    }

    public function action(Request $request, JobReport $report): RedirectResponse
    {
        $validated = $request->validate([
            'action_type' => ['required', 'in:close_job,ban_employer,delete_job,dismiss'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
            'ban_duration' => ['nullable', 'required_if:action_type,ban_employer', 'in:3_days,7_days,14_days,30_days,custom,permanent'],
            'ban_custom_days' => ['nullable', 'required_if:ban_duration,custom', 'integer', 'min:1'],
            'close_duration' => ['nullable', 'required_if:action_type,close_job', 'in:7_days,14_days,30_days,custom,permanent'],
            'close_custom_days' => ['nullable', 'required_if:close_duration,custom', 'integer', 'min:1'],
        ]);

        $actionType = $validated['action_type'];
        $notes = $validated['admin_notes'] ?? '';
        $admin = $request->user();

        if ($actionType === 'close_job') {
            $closedUntil = match ($validated['close_duration']) {
                '7_days' => now()->addDays(7),
                '14_days' => now()->addDays(14),
                '30_days' => now()->addDays(30),
                'custom' => now()->addDays((int) $validated['close_custom_days']),
                'permanent' => null,
            };

            $report->job->update([
                'status' => 'closed',
                'closed_by_admin' => true,
                'closed_reason' => 'Ditutup oleh Pengawas KerjaLokal berdasarkan hasil investigasi laporan pelamar: '.($notes ?: $report->reason),
                'closed_until' => $closedUntil,
            ]);

            $report->update([
                'status' => 'action_taken',
                'action_taken' => 'Lowongan ditutup oleh Admin',
                'admin_notes' => $notes,
            ]);

            // Kirim pesan notifikasi ke pemilik lowongan (Mitra)
            ChatMessage::create([
                'sender_id' => $admin->id,
                'receiver_id' => $report->job->employer_id,
                'message' => "⚠️ [Tindakan Pengawasan: Lowongan Ditutup]\n\nHalo {$report->job->employer->name}, lowongan Anda \"{$report->job->title}\" telah ditutup oleh Administrator berdasarkan hasil peninjauan laporan dugaan pelanggaran kepatuhan etis.\n• Alasan: \"{$report->reason}\"".($notes ? "\n• Catatan Administrator: \"{$notes}\"" : ''),
                'is_read' => false,
            ]);

            // Kirim pesan notifikasi ke pelapor (Pencari Kerja) jika ada
            if ($report->reporter_id) {
                ChatMessage::create([
                    'sender_id' => $admin->id,
                    'receiver_id' => $report->reporter_id,
                    'message' => "🛡️ [Laporan Anda Telah Ditindaklanjuti]\n\nHalo {$report->reporter->name}, terima kasih atas kontribusi Anda dalam pengawasan kepatuhan ketenagakerjaan. Laporan Anda mengenai lowongan \"{$report->job->title}\" telah selesai diinvestigasi: Administrator telah menutup lowongan terkait.",
                    'is_read' => false,
                ]);
            }

            return redirect()->route('admin.reports.index')->with('success', "Lowongan '{$report->job->title}' berhasil ditutup dan status laporan diselesaikan.");
        }

        if ($actionType === 'ban_employer') {
            $employer = $report->job->employer;
            $bannedUntil = match ($validated['ban_duration']) {
                '3_days' => now()->addDays(3),
                '7_days' => now()->addDays(7),
                '14_days' => now()->addDays(14),
                '30_days' => now()->addDays(30),
                'custom' => now()->addDays((int) $validated['ban_custom_days']),
                'permanent' => null,
            };

            $employer->update([
                'banned_at' => now(),
                'banned_until' => $bannedUntil,
                'ban_reason' => 'Akun dibekukan akibat pelanggaran etika rekrutmen: '.($notes ?: $report->reason),
            ]);

            // Close all employer's active jobs
            Job::where('employer_id', $employer->id)->where('status', 'open')->update([
                'status' => 'closed',
                'closed_by_admin' => true,
                'closed_reason' => 'Ditutup karena akun Mitra UMKM dibekukan oleh Administrator.',
                'closed_until' => $bannedUntil,
            ]);

            $report->update([
                'status' => 'action_taken',
                'action_taken' => 'Akun Mitra UMKM dibekukan (Ban)',
                'admin_notes' => $notes,
            ]);

            // Kirim pesan sanksi ban ke Mitra
            ChatMessage::create([
                'sender_id' => $admin->id,
                'receiver_id' => $employer->id,
                'message' => "⚠️ [Sanksi Pembekuan Akun Mitra]\n\nHalo {$employer->name}, akun Anda telah dibekukan sementara oleh Administrator KerjaLokal berdasarkan hasil investigasi laporan pelanggaran etika ketenagakerjaan.\n• Alasan: \"{$report->reason}\"".($notes ? "\n• Catatan: \"{$notes}\"" : ''),
                'is_read' => false,
            ]);

            // Kirim pesan konfirmasi ke Pelapor jika ada
            if ($report->reporter_id) {
                ChatMessage::create([
                    'sender_id' => $admin->id,
                    'receiver_id' => $report->reporter_id,
                    'message' => "🛡️ [Laporan Anda Telah Ditindaklanjuti]\n\nHalo {$report->reporter->name}, laporan Anda terhadap mitra \"{$employer->name}\" telah selesai ditindaklanjuti. Pihak terkait telah diberikan sanksi pembekuan akun (ban) dan lowongan aktifnya telah dinonaktifkan.",
                    'is_read' => false,
                ]);
            }

            return redirect()->route('admin.reports.index')->with('success', "Akun mitra '{$employer->name}' berhasil dibekukan dan laporan telah ditindaklanjuti.");
        }

        if ($actionType === 'delete_job') {
            $job = $report->job;
            $employer = $job->employer;
            $jobTitle = $job->title;
            $resumeFiles = $job->applications()->pluck('resume_file')->filter()->all();

            // Kirim pesan ke pemilik lowongan sebelum dihapus
            ChatMessage::create([
                'sender_id' => $admin->id,
                'receiver_id' => $employer->id,
                'message' => "🗑️ [Penghapusan Lowongan]\n\nHalo {$employer->name}, lowongan \"{$jobTitle}\" telah dihapus permanen oleh Administrator berdasarkan hasil investigasi aduan pelanggaran.",
                'is_read' => false,
            ]);

            // Kirim pesan konfirmasi ke pelapor jika ada
            if ($report->reporter_id) {
                ChatMessage::create([
                    'sender_id' => $admin->id,
                    'receiver_id' => $report->reporter_id,
                    'message' => "🛡️ [Laporan Anda Telah Ditindaklanjuti]\n\nHalo {$report->reporter->name}, laporan Anda mengenai lowongan \"{$jobTitle}\" telah diverifikasi oleh tim Administrator. Lowongan yang dilaporkan kini telah dihapus permanen dari platform.",
                    'is_read' => false,
                ]);
            }

            DB::transaction(fn () => $job->delete());

            if (! empty($resumeFiles)) {
                Storage::disk('local')->delete($resumeFiles);
            }

            return redirect()->route('admin.reports.index')->with('success', "Lowongan '{$jobTitle}' berhasil dihapus permanen dari sistem.");
        }

        if ($actionType === 'dismiss') {
            $report->update([
                'status' => 'dismissed',
                'action_taken' => 'Laporan ditolak / tidak ditemukan pelanggaran',
                'admin_notes' => $notes,
            ]);

            // Kirim pesan ke pelapor mengenai status laporan jika ada
            if ($report->reporter_id) {
                ChatMessage::create([
                    'sender_id' => $admin->id,
                    'receiver_id' => $report->reporter_id,
                    'message' => "ℹ️ [Pemberitahuan Hasil Tinjauan Laporan]\n\nHalo {$report->reporter->name}, laporan yang Anda kirimkan terkait lowongan \"{$report->job->title}\" telah selesai ditinjau. Berdasarkan pemeriksaan bukti dan parameter kepatuhan, saat ini belum ditemukan unsur pelanggaran sehingga laporan dinyatakan selesai.".($notes ? "\n• Catatan Pengawas: \"{$notes}\"" : ''),
                    'is_read' => false,
                ]);
            }

            return redirect()->route('admin.reports.index')->with('success', 'Laporan telah ditandai selesai (Ditolak/Tidak Ditemukan Pelanggaran).');
        }

        return back();
    }
}
