<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateApplicationStatusRequest;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ->with(['user:id,name,email,phone', 'job:id,employer_id,title', 'job.skills:id,name'])
            ->when($request->integer('job'), fn ($query, $job) => $query->where('job_id', $job))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $jobs = Job::query()
            ->whereBelongsTo($employer, 'employer')
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('employer.applications.index', compact('applications', 'jobs'));
    }

    public function update(UpdateApplicationStatusRequest $request, JobApplication $application): RedirectResponse
    {
        $application->update($request->validated());
        $application->loadMissing('user:id,name');

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
}
