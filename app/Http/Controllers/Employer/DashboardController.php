<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        return $this->index($request);
    }

    public function index(Request $request): View
    {
        $employer = $request->user();

        $jobs = Job::query()
            ->whereBelongsTo($employer, 'employer')
            ->with('category:id,name')
            ->withCount('applications')
            ->latest('id')
            ->paginate(8);

        $applications = JobApplication::query()
            ->whereHas('job', fn ($query) => $query->whereBelongsTo($employer, 'employer'));

        $acceptedWages = (clone $applications)
            ->where('status', 'accepted')
            ->with('job:id,salary_amount')
            ->get()
            ->sum(fn (JobApplication $application): float => (float) $application->job->salary_amount);

        $acceptedWorkers = (clone $applications)
            ->where('status', 'accepted')
            ->with(['user:id,name,username,email,phone,avatar,created_at', 'job:id,title,salary_amount,salary_type,location'])
            ->latest('updated_at')
            ->get();

        $metrics = [
            'open_jobs' => Job::whereBelongsTo($employer, 'employer')->where('status', 'open')->count(),
            'applications' => (clone $applications)->count(),
            'accepted' => (clone $applications)->where('status', 'accepted')->count(),
            'accepted_wages' => $acceptedWages,
        ];

        return view('employer.dashboard', compact('employer', 'jobs', 'metrics', 'acceptedWorkers'));
    }
}
