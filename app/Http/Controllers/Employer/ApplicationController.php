<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateApplicationStatusRequest;
use App\Models\ChatMessage;
use App\Models\Job;
use App\Models\JobApplication;
use App\Notifications\ApplicationStatusUpdatedNotification;
use App\Notifications\ResignationDecisionNotification;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $employer = $request->user();

        $applications = JobApplication::query()
            ->whereHas('job', fn ($query) => $query->whereBelongsTo($employer, 'employer'))
            ->whereNull('employer_hidden_at')
            ->with(['user', 'job.skills', 'job.category'])
            ->when($request->filled('job'), fn ($q) => $q->where('job_id', $request->integer('job')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $jobs = Job::query()
            ->where('employer_id', $employer->id)
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('employer.applications.index', compact('applications', 'jobs'));
    }

    public function update(UpdateApplicationStatusRequest $request, JobApplication $application): RedirectResponse
    {
        $oldStatus = $application->status;
        $status = $request->validated('status');

        $updateData = [
            'status' => $status,
        ];

        if ($status === 'interview') {
            $updateData['interview_date'] = $request->input('interview_date');
            $updateData['interview_time'] = $request->input('interview_time');
            $updateData['interview_type'] = $request->input('interview_type');
            $updateData['interview_location'] = $request->input('interview_location');
            $updateData['interview_notes'] = $request->input('interview_notes');
            $updateData['interview_status'] = 'pending';
        } elseif ($status === 'accepted') {
            $updateData['start_date'] = $request->input('start_date');
            $updateData['acceptance_notes'] = $request->input('acceptance_notes');
        } elseif ($status === 'rejected') {
            $updateData['rejection_reason'] = $request->input('rejection_reason');
            $updateData['rejection_notes'] = $request->input('rejection_notes');
            $updateData['rejected_at'] = now();
        }

        $application->update($updateData);
        $application->loadMissing(['user:id,name', 'job.employer']);

        $employer = $request->user();
        $employerName = $employer->business_name ?: $employer->name;

        // 1. Kasus Wawancara (interview) - Dengan pengaturan jadwal & konfirmasi
        if ($status === 'interview') {
            $dateFormatted = $request->filled('interview_date')
                ? Carbon::parse($request->input('interview_date'))->locale('id')->translatedFormat('l, d F Y')
                : null;
            $timeFormatted = $request->filled('interview_time') ? $request->input('interview_time').' WIB' : null;
            $type = $request->input('interview_type') ?: 'Wawancara Langsung';
            $location = $request->input('interview_location') ?: ($employer->business_name ?: 'Lokasi UMKM');
            $notes = $request->input('interview_notes') ?: 'Mohon konfirmasi kesiapan Anda menghadiri sesi wawancara ini.';

            $scheduleSnippet = $dateFormatted ? " pada hari {$dateFormatted}".($timeFormatted ? " pukul {$timeFormatted}" : '') : '';
            $customNotifMessage = "Kabar baik! Anda diundang mengikuti wawancara posisi '{$application->job->title}' di {$employerName}{$scheduleSnippet} ({$type}). Mohon konfirmasi kesiapan Anda.";

            $application->user->notify(new ApplicationStatusUpdatedNotification($application, 'interview', $customNotifMessage));

            $scheduleText = $dateFormatted ? "\n• Hari/Tanggal: {$dateFormatted}" : '';
            if ($timeFormatted) {
                $scheduleText .= "\n• Waktu: {$timeFormatted}";
            }
            $scheduleText .= "\n• Metode/Lokasi: {$type} - {$location}";
            $scheduleText .= "\n• Catatan: {$notes}";

            ChatMessage::create([
                'sender_id' => $employer->id,
                'receiver_id' => $application->user_id,
                'message' => "📅 [Undangan Wawancara Kerja]\n\nHalo {$application->user->name},\nKami dari {$employerName} mengundang Anda untuk mengikuti sesi wawancara untuk posisi \"{$application->job->title}\":{$scheduleText}\n\nMohon konfirmasi kesiapan Anda dengan membalas pesan ini. Terima kasih!",
                'is_read' => false,
            ]);

            return back()->with('success', "Undangan wawancara berhasil dijadwalkan dan dikirimkan ke {$application->user->name}!");
        }

        // 2. Kasus Diterima (accepted)
        if ($status === 'accepted') {
            $startDate = $request->filled('start_date')
                ? Carbon::parse($request->input('start_date'))->locale('id')->translatedFormat('l, d F Y')
                : 'Segera / Menyesuaikan';
            $notes = $request->input('acceptance_notes') ?: 'Silakan koordinasikan persiapan Anda melalui ruang obrolan ini.';

            $customNotifMessage = "🎉 Selamat! Lamaran Anda untuk posisi '{$application->job->title}' telah DITERIMA oleh {$employerName}. Mulai kerja: {$startDate}.";

            $application->user->notify(new ApplicationStatusUpdatedNotification($application, 'accepted', $customNotifMessage));

            ChatMessage::create([
                'sender_id' => $employer->id,
                'receiver_id' => $application->user_id,
                'message' => "🎉 Selamat {$application->user->name}! Lamaran Anda untuk posisi \"{$application->job->title}\" telah DITERIMA oleh {$employerName}. Anda telah resmi tercatat sebagai peserta/tenaga kerja aktif kami.\n\n• Mulai Kerja: {$startDate}\n• Catatan: {$notes}\n\nSilakan koordinasikan persiapan Anda melalui ruang obrolan ini.",
                'is_read' => false,
            ]);

            return back()->with('success', "Kandidat {$application->user->name} resmi diterima dan masuk sebagai peserta aktif di UMKM Anda!");
        }

        // 3. Kasus Ditolak (rejected)
        if ($status === 'rejected') {
            $reason = $request->input('rejection_reason') ?: 'Kualifikasi belum sesuai dengan kebutuhan saat ini';
            $notes = $request->input('rejection_notes') ?: 'Terima kasih telah melamar di UMKM kami. Kami mendoakan kesuksesan untuk langkah karier Anda selanjutnya.';

            $customNotifMessage = "Terima kasih telah melamar posisi '{$application->job->title}'. Proses seleksi belum dapat dilanjutkan kali ini ({$reason}).";

            $application->user->notify(new ApplicationStatusUpdatedNotification($application, 'rejected', $customNotifMessage));

            ChatMessage::create([
                'sender_id' => $employer->id,
                'receiver_id' => $application->user_id,
                'message' => "📋 [Pemberitahuan Hasil Seleksi]\n\nHalo {$application->user->name},\nTerima kasih atas partisipasi dan minat Anda melamar posisi \"{$application->job->title}\" di {$employerName}.\n\nSetelah peninjauan berkas secara saksama, kami menginformasikan bahwa untuk saat ini kami belum dapat melanjutkan ke tahap berikutnya ({$reason}).\n\n{$notes}",
                'is_read' => false,
            ]);

            return back()->with('success', "Pemberitahuan hasil seleksi telah dikirimkan ke {$application->user->name}.");
        }

        // 4. Kasus Default / Menunggu Tinjauan (pending)
        if ($status !== $oldStatus) {
            $application->user->notify(new ApplicationStatusUpdatedNotification($application, $status));
        }

        return back()->with('success', "Status lamaran {$application->user->name} berhasil diperbarui.");
    }

    public function download(JobApplication $application): StreamedResponse
    {
        Gate::authorize('download', $application);
        abort_unless(Storage::disk('local')->exists($application->resume_file), 404, 'Berkas resume tidak ditemukan.');
        $application->loadMissing('user:id,name');

        return Storage::disk('local')->download(
            $application->resume_file,
            'CV-'.$application->user->name.'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function preview(JobApplication $application): StreamedResponse
    {
        Gate::authorize('download', $application);
        abort_unless(Storage::disk('local')->exists($application->resume_file), 404, 'Berkas resume tidak ditemukan.');
        $application->loadMissing('user:id,name');

        return Storage::disk('local')->response(
            $application->resume_file,
            'CV-'.$application->user->name.'.pdf',
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="CV-'.$application->user->name.'.pdf"',
                'X-Content-Type-Options' => 'nosniff',
            ],
        );
    }

    /**
     * Process resignation decision (approve or reject) by employer.
     */
    public function resignDecision(Request $request, JobApplication $application): RedirectResponse|JsonResponse
    {
        $job = $application->job;
        if ($job->employer_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola pengajuan ini.');
        }

        if ($application->resignation_status !== 'pending') {
            return back()->with('warning', 'Pengajuan resign untuk kandidat ini telah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'response_message' => ['required', 'string', 'max:1000'],
        ], [
            'decision.required' => 'Keputusan persetujuan atau penolakan wajib dipilih.',
            'response_message.required' => 'Pesan atau tanggapan untuk kandidat wajib diisi.',
        ]);

        $employer = $request->user();
        $application->loadMissing(['user:id,name']);

        if ($validated['decision'] === 'approved') {
            $application->update([
                'status' => 'resigned',
                'resignation_status' => 'approved',
                'resigned_at' => now(),
            ]);

            // 1. Notifikasi ke pencari kerja
            $application->user->notify(new ResignationDecisionNotification($application, 'approved', $validated['response_message']));

            // 2. Kirim pesan konfirmasi resmi ke ruang obrolan
            ChatMessage::create([
                'sender_id' => $employer->id,
                'receiver_id' => $application->user_id,
                'message' => "✅ [Pengajuan Resign Disetujui]\n\nHalo {$application->user->name},\nPermohonan pengunduran diri Anda dari posisi \"{$job->title}\" telah kami setujui.\n\n• Pesan/Tanggapan Mitra: {$validated['response_message']}\n\nTerima kasih banyak atas dedikasi dan kerja sama yang baik selama ini. Kami mendoakan kesuksesan untuk langkah karier Anda selanjutnya!",
                'is_read' => false,
            ]);

            $message = "Pengajuan resign {$application->user->name} resmi disetujui.";
        } else {
            $application->update([
                'resignation_status' => 'rejected',
            ]);

            // 1. Notifikasi ke pencari kerja
            $application->user->notify(new ResignationDecisionNotification($application, 'rejected', $validated['response_message']));

            // 2. Kirim pesan penolakan resmi ke ruang obrolan
            ChatMessage::create([
                'sender_id' => $employer->id,
                'receiver_id' => $application->user_id,
                'message' => "❌ [Pengajuan Resign Belum Disetujui]\n\nHalo {$application->user->name},\nMohon maaf, permohonan pengunduran diri Anda dari posisi \"{$job->title}\" belum dapat kami setujui saat ini.\n\n• Catatan/Alasan Mitra: {$validated['response_message']}\n\nMari kita bicarakan kembali dan koordinasikan melalui ruang obrolan ini.",
                'is_read' => false,
            ]);

            $message = "Pengajuan resign {$application->user->name} telah ditolak dengan catatan evaluasi.";
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'decision' => $validated['decision'],
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Hide an applicant from the employer's applicant list.
     */
    public function hide(Request $request, JobApplication $application): RedirectResponse
    {
        $application->loadMissing('job');
        if ($application->job?->employer_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus riwayat pelamar ini.');
        }

        $candidateName = $application->user?->name ?? 'Kandidat';
        $application->update(['employer_hidden_at' => now()]);

        return back()->with('success', "Riwayat pelamar \"{$candidateName}\" berhasil dihapus dari daftar pelamar Anda.");
    }

    /**
     * Remove candidate from selection completely (reset application so candidate can re-apply).
     */
    public function resetSelection(Request $request, JobApplication $application): RedirectResponse
    {
        $application->loadMissing(['job', 'user']);
        if ($application->job?->employer_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki hak untuk melepas status seleksi pelamar ini.');
        }

        $candidate = $application->user;
        $candidateName = $candidate?->name ?? 'Kandidat';
        $job = $application->job;
        $jobTitle = $job?->title ?? 'posisi pekerjaan';
        $resumeFile = $application->resume_file;
        $employer = $request->user();

        DB::transaction(function () use ($application): void {
            $application->delete();
        });

        if ($resumeFile && Storage::disk('local')->exists($resumeFile)) {
            Storage::disk('local')->delete($resumeFile);
        }

        if ($candidate) {
            ChatMessage::create([
                'sender_id' => $employer->id,
                'receiver_id' => $candidate->id,
                'message' => "🔄 [Status Seleksi Dilepas / Reset]\n\nHalo {$candidateName},\nStatus lamaran Anda untuk posisi \"{$jobTitle}\" telah dilepas dari proses seleksi oleh {$employer->business_name}.\n\nRiwayat lamaran Anda untuk posisi ini telah di-reset sehingga Anda dapat mengajukan lamaran baru kembali jika berminat.",
                'is_read' => false,
            ]);
        }

        return back()->with('success', "Status seleksi untuk \"{$candidateName}\" berhasil dilepas. Lamaran kandidat telah di-reset dan kandidat kini dapat mengajukan lamaran kembali.");
    }
}
