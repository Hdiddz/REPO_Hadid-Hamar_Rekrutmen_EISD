<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = Job::query()
            ->with(['employer:id,name,email,phone,business_name', 'category:id,name'])
            ->withCount('applications')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->where(function ($nested) use ($term): void {
                    $nested->where('title', 'like', "%{$term}%")
                        ->orWhereHas('employer', fn ($employer) => $employer->where('business_name', 'like', "%{$term}%"));
                });
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.jobs.index', compact('jobs'));
    }

    public function show(Job $job): View
    {
        $job->load([
            'employer:id,name,email,phone,business_name,created_at',
            'category:id,name',
            'skills:id,name',
            'applications.user:id,name,email,phone',
        ])->loadCount('applications');

        return view('admin.jobs.show', compact('job'));
    }

    public function updateStatus(Request $request, Job $job): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,closed'],
        ], [
            'status.in' => 'Status lowongan tidak valid.',
        ]);

        $job->update($validated);

        return back()->with('success', 'Status lowongan berhasil diperbarui oleh administrator.');
    }
}
