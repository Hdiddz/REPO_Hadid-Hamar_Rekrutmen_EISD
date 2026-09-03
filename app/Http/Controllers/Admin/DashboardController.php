<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $acceptedApplications = JobApplication::query()
            ->where('status', 'accepted')
            ->with('job:id,salary_amount')
            ->get();

        $metrics = [
            'open_jobs' => Job::where('status', 'open')->count(),
            'accepted_workers' => $acceptedApplications->count(),
            'employers' => User::where('role', 'employer')->count(),
            'jobseekers' => User::where('role', 'jobseeker')->count(),
            'wage_circulation' => $acceptedApplications->sum(fn (JobApplication $application): float => (float) $application->job->salary_amount),
        ];

        $recentJobs = Job::query()
            ->with(['employer:id,name,email,business_name', 'category:id,name'])
            ->withCount('applications')
            ->latest('id')
            ->limit(6)
            ->get();

        $recentApplications = JobApplication::query()
            ->with(['user:id,name', 'job:id,title'])
            ->latest('id')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('metrics', 'recentJobs', 'recentApplications'));
    }
}
