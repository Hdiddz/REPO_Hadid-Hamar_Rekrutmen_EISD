<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Models\ChatMessage;
use App\Models\Job;
use App\Models\JobApplication;
use App\Notifications\InterviewResponseNotification;
use App\Notifications\JobApplicationSubmittedNotification;
use App\Notifications\NewApplicationReceivedNotification;
use App\Notifications\ResignationSubmittedNotification;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Throwable;

class JobApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $userApps = JobApplication::whereBelongsTo($request->user())
            ->whereNull('jobseeker_hidden_at');

        $counts = [
            'all' => (clone $userApps)->count(),
            'pending' => (clone $userApps)->where('status', 'pending')->count(),
            'interview' => (clone $userApps)->where('status', 'interview')->count(),
            'accepted' => (clone $userApps)->where('status', 'accepted')->where(fn ($q) => $q->whereNull('resignation_status')->orWhere('resignation_status', '!=', 'approved'))->count(),
            'rejected' => (clone $userApps)->where('status', 'rejected')->count(),
            'resigned' => (clone $userApps)->where(fn ($q) => $q->where('status', 'resigned')->orWhere('resignation_status', 'approved'))->count(),
        ];

        $applications = JobApplication::query()
            ->whereBelongsTo($request->user())
            ->whereNull('jobseeker_hidden_at')
            ->when($status === 'accepted', function ($query) {
                $query->where('status', 'accepted')
                    ->where(fn ($q) => $q->whereNull('resignation_status')->orWhere('resignation_status', '!=', 'approved'));
            })
            ->when($status === 'resigned', function ($query) {
                $query->where(fn ($q) => $q->where('status', 'resigned')->orWhere('resignation_status', 'approved'));
            })
            ->when($status && in_array($status, ['pending', 'interview', 'rejected']), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->with(['job.category:id,name', 'job.employer:id,name,business_name'])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('applications.index', compact('applications', 'counts'));
    }

    public function store(StoreJobApplicationRequest $request, Job $job): RedirectResponse
    {
        if ($job->status !== 'open') {
            return back()->with('error', 'Lowongan sudah ditutup dan tidak menerima lamaran baru.');
        }

        $existingApp = $job->applications()->where('user_id', $request->user()->id)->first();

        if ($existingApp) {
            if ($existingApp->status === 'rejected') {
                if ($existingApp->canBeReappliedTomorrow()) {
                    $availableAt = $existingApp->reapplyAvailableAt()?->translatedFormat('l, d F Y') ?? 'besok';

                    return back()->with('warning', "Anda baru dapat mengajukan lamaran kembali ke lowongan ini mulai besok ({$availableAt}).");
                }
            } else {
                return back()->with('warning', 'Anda sudah mengajukan lamaran untuk lowongan ini.');
            }
        }

        $resumePath = $request->file('resume')->store('resumes', 'local');
        $oldResume = $existingApp?->resume_file;

        try {
            DB::transaction(function () use ($job, $request, $resumePath, $existingApp): void {
                if ($existingApp) {
                    $existingApp->update([
                        'resume_file' => $resumePath,
                        'note' => $request->validated('note'),
                        'status' => 'pending',
                        'rejection_reason' => null,
                        'rejection_notes' => null,
                        'rejected_at' => null,
                        'interview_date' => null,
                        'interview_time' => null,
                        'interview_type' => null,
                        'interview_location' => null,
                        'interview_notes' => null,
                        'interview_status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $application = $existingApp;
                } else {
                    $job->applicants()->attach($request->user()->id, [
                        'resume_file' => $resumePath,
                        'note' => $request->validated('note'),
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $application = JobApplication::where('job_id', $job->id)
                        ->where('user_id', $request->user()->id)
                        ->first();
                }

                if ($application) {
                    $application->setRelation('job', $job);
                    $application->setRelation('user', $request->user());

                    if ($job->employer) {
                        $job->employer->notify(new NewApplicationReceivedNotification($application));
                    }

                    $request->user()->notify(new JobApplicationSubmittedNotification($application));
                }
            });

            if ($oldResume && $oldResume !== $resumePath && Storage::disk('local')->exists($oldResume)) {
                Storage::disk('local')->delete($oldResume);
            }
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($resumePath);
            report($exception);

            return back()->withInput()->with('error', 'Lamaran belum dapat disimpan. Silakan coba kembali.');
        }

        return redirect()->route('applications.index')
            ->with('success', 'Lamaran berhasil dikirim dan dapat dipantau pada riwayat lamaran.');
    }

    public function destroy(Request $request, JobApplication $application): RedirectResponse
    {
        Gate::authorize('delete', $application);

        $job = $application->job;
        $jobTitle = $job?->title ?? 'posisi pekerjaan';
        $resumeFile = $application->resume_file;

        DB::transaction(function () use ($application): void {
            $application->delete();
        });

        if ($resumeFile && Storage::disk('local')->exists($resumeFile)) {
            Storage::disk('local')->delete($resumeFile);
        }

        if ($job && $job->status !== 'open') {
            return redirect()->route('applications.index')
                ->with('success', "Lamaran untuk \"{$jobTitle}\" berhasil dibatalkan.");
        }

        return back()->with('success', "Lamaran untuk \"{$jobTitle}\" berhasil dibatalkan.");
    }

    /**
     * Hide an application from the jobseeker's application history.
     */
    public function hide(Request $request, JobApplication $application): RedirectResponse
    {
        if ($application->user_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus riwayat lamaran ini.');
        }

        $jobTitle = $application->job?->title ?? 'posisi pekerjaan';
        $application->update(['jobseeker_hidden_at' => now()]);

        return back()->with('success', "Lamaran untuk \"{$jobTitle}\" berhasil dihapus dari riwayat lamaran Anda.");
    }

    /**
     * Submit a resignation request for an accepted job application.
     */
    public function requestResignation(Request $request, JobApplication $application): RedirectResponse
    {
        if ($application->user_id !== $request->user()->id || $application->status !== 'accepted') {
            abort(403, 'Hanya peserta yang telah resmi diterima yang dapat mengajukan pengunduran diri.');
        }

        $validated = $request->validate([
            'resignation_date' => ['required', 'date'],
            'resignation_reason' => ['required', 'string', 'max:255'],
            'resignation_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'resignation_date.required' => 'Tanggal efektif pengunduran diri wajib ditentukan.',
            'resignation_reason.required' => 'Alasan pengunduran diri wajib dipilih atau diisi.',
        ]);

        $application->update([
            'resignation_date' => $validated['resignation_date'],
            'resignation_reason' => $validated['resignation_reason'],
            'resignation_notes' => $validated['resignation_notes'] ?? null,
            'resignation_status' => 'pending',
            'resigned_at' => now(),
        ]);

        $job = $application->job;
        $employer = $job->employer;
        $candidate = $request->user();
        $dateFormatted = Carbon::parse($validated['resignation_date'])->locale('id')->translatedFormat('l, d F Y');

        if ($employer) {
            $employer->notify(new ResignationSubmittedNotification($application));

            ChatMessage::create([
                'sender_id' => $candidate->id,
                'receiver_id' => $employer->id,
                'message' => "📄 [Pengajuan Pengunduran Diri (Resign)]\n\nHalo {$employer->business_name},\nSaya ({$candidate->name}) mengajukan permohonan pengunduran diri dari posisi \"{$job->title}\" efektif per hari {$dateFormatted}.\n\n• Alasan: {$validated['resignation_reason']}".($validated['resignation_notes'] ? "\n• Catatan: {$validated['resignation_notes']}" : '')."\n\nTerima kasih banyak atas bimbingan dan kesempatan kerja sama yang telah diberikan.",
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Permohonan pengunduran diri (resign) berhasil dikirimkan ke Mitra UMKM.');
    }

    /**
     * Respond to an interview schedule (confirm/approve, request reschedule/discussion, or decline).
     */
    public function respondToInterview(Request $request, JobApplication $application): RedirectResponse|JsonResponse
    {
        if ($application->user_id !== $request->user()->id || $application->status !== 'interview') {
            abort(403, 'Hanya pelamar yang sedang dalam tahap wawancara yang dapat memberikan tanggapan jadwal.');
        }

        $validated = $request->validate([
            'action' => ['required', 'string', 'in:confirmed,reschedule_requested,declined,confirm,reschedule,decline'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $statusMap = [
            'confirm' => 'confirmed',
            'confirmed' => 'confirmed',
            'reschedule' => 'reschedule_requested',
            'reschedule_requested' => 'reschedule_requested',
            'decline' => 'declined',
            'declined' => 'declined',
        ];

        $interviewStatus = $statusMap[$validated['action']];
        $application->update(['interview_status' => $interviewStatus]);

        $application->loadMissing(['job.employer', 'user']);
        $job = $application->job;
        $employer = $job?->employer;
        $candidate = $request->user();
        $employerName = $employer?->business_name ?: ($employer?->name ?? 'Mitra UMKM');
        $note = $validated['note'] ?? null;

        $dateFormatted = $application->interview_date
            ? Carbon::parse($application->interview_date)->locale('id')->translatedFormat('l, d F Y')
            : null;
        $timeFormatted = $application->interview_time ? $application->interview_time.' WIB' : null;
        $scheduleInfo = $dateFormatted ? " pada hari {$dateFormatted}".($timeFormatted ? " pukul {$timeFormatted}" : '') : '';

        $chatMessageText = match ($interviewStatus) {
            'confirmed' => "✅ [Konfirmasi Kehadiran Wawancara]\n\nHalo {$employerName},\nSaya ({$candidate->name}) MENYETUJUI dan bersedia menghadiri sesi wawancara untuk posisi \"{$job?->title}\"{$scheduleInfo}.".($note ? "\n\n• Catatan Pelamar: {$note}" : '')."\n\nTerima kasih atas kesempatan yang diberikan!",
            'reschedule_requested' => "💬 [Permohonan Diskusi Jadwal Wawancara]\n\nHalo {$employerName},\nSaya ({$candidate->name}) bermaksud mengajukan penyesuaian jadwal wawancara untuk posisi \"{$job?->title}\".".($note ? "\n\n• Usulan/Alasan Pelamar: {$note}" : '')."\n\nBisakah kita mendiskusikan opsi waktu lain yang memungkinkan? Terima kasih banyak!",
            'declined' => "❌ [Penolakan Undangan Wawancara]\n\nHalo {$employerName},\nMohon maaf, saya ({$candidate->name}) belum dapat menghadiri undangan wawancara untuk posisi \"{$job?->title}\"{$scheduleInfo}.".($note ? "\n\n• Alasan Pelamar: {$note}" : '')."\n\nTerima kasih banyak atas perhatian dan kesempatan yang telah diberikan.",
        };

        $flashMessage = match ($interviewStatus) {
            'confirmed' => 'Jadwal wawancara berhasil disetujui. Pemberitahuan telah dikirimkan ke Mitra.',
            'reschedule_requested' => 'Permohonan diskusi jadwal berhasil diajukan ke Mitra melalui obrolan.',
            'declined' => 'Undangan wawancara telah ditolak dan disampaikan ke Mitra.',
        };

        if ($employer) {
            $employer->notify(new InterviewResponseNotification($application, $interviewStatus, $note));

            ChatMessage::create([
                'sender_id' => $candidate->id,
                'receiver_id' => $employer->id,
                'message' => $chatMessageText,
                'is_read' => false,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'interview_status' => $interviewStatus,
                'message' => $flashMessage,
                'chat_message' => $chatMessageText,
            ]);
        }

        return back()->with('success', $flashMessage);
    }
}
