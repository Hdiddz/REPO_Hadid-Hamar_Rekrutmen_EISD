<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use App\Models\Skill;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $jobs = Job::query()
            ->where('status', 'open')
            ->with(['category:id,name', 'employer:id,name,business_name', 'skills:id,name'])
            ->withCount(['applications', 'workplacePhotos'])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->where(function ($nested) use ($term): void {
                    $nested->where('title', 'like', "%{$term}%")
                        ->orWhere('location', 'like', "%{$term}%");
                });
            })
            ->when($request->integer('category'), fn ($query, $category) => $query->where('category_id', $category))
            ->when($request->integer('skill'), fn ($query, $skill) => $query->whereHas('skills', fn ($skillQuery) => $skillQuery->whereKey($skill)))
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $skills = Skill::orderBy('name')->get(['id', 'name']);

        return view('jobs.index', compact('jobs', 'categories', 'skills'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Job $job): View
    {
        $application = $request->user()?->hasRole('jobseeker')
            ? $job->applications()->where('user_id', $request->user()->id)->first()
            : null;
        $hasApplied = $application !== null;

        if ($job->status !== 'open'
            && ! $request->user()?->hasRole('admin')
            && $job->employer_id !== $request->user()?->id
            && ! $hasApplied) {
            abort(404);
        }

        $job->load(['category:id,name', 'employer:id,name,email,phone,business_name', 'skills:id,name', 'workplacePhotos'])
            ->loadCount('applications');

        $previousUrl = url()->previous();
        if ($request->filled('return_to')) {
            $returnTo = $request->string('return_to')->toString();
            $appUrl = url('/');
            if ((str_starts_with($returnTo, '/') && ! str_starts_with($returnTo, '//')) || str_starts_with($returnTo, $appUrl)) {
                session()->put('job_show_return_to', $returnTo);
            }
        } elseif ($previousUrl && $previousUrl !== $request->fullUrl() && ! str_contains($previousUrl, '/lowongan/'.$job->id)) {
            if (str_starts_with($previousUrl, url('/')) && ! str_contains($previousUrl, 'login') && ! str_contains($previousUrl, 'logout')) {
                session()->put('job_show_return_to', $previousUrl);
            }
        }

        $returnUrl = session('job_show_return_to', route('jobs.index'));

        return view('jobs.show', compact('job', 'hasApplied', 'application', 'returnUrl'));
    }
}
