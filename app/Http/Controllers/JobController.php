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
            ->withCount('applications')
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

        $job->load(['category:id,name', 'employer:id,name,email,phone,business_name', 'skills:id,name'])
            ->loadCount('applications');

        return view('jobs.show', compact('job', 'hasApplied', 'application'));
    }
}
