<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
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
        $applications = JobApplication::query()
            ->whereBelongsTo($request->user())
            ->with(['job.category:id,name', 'job.employer:id,name,business_name'])
            ->latest('id')
            ->paginate(10);

        return view('applications.index', compact('applications'));
    }

    public function store(StoreJobApplicationRequest $request, Job $job): RedirectResponse
    {
        if ($job->status !== 'open') {
            return back()->with('error', 'Lowongan sudah ditutup dan tidak menerima lamaran baru.');
        }

        if ($job->applications()->where('user_id', $request->user()->id)->exists()) {
            return back()->with('warning', 'Anda sudah mengajukan lamaran untuk lowongan ini.');
        }

        $resumePath = $request->file('resume')->store('resumes', 'local');

        try {
            DB::transaction(function () use ($job, $request, $resumePath): void {
                $job->applicants()->attach($request->user()->id, [
                    'resume_file' => $resumePath,
                    'note' => $request->validated('note'),
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
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
}
