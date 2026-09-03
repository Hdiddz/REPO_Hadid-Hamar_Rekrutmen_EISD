<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobReport;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        return $this->index();
    }

    public function index(): View
    {
        $metrics = [
            'open_jobs' => Job::where('status', 'open')->count(),
            'accepted_workers' => JobApplication::where('status', 'accepted')->count(),
            'employers' => User::where('role', 'employer')->count(),
            'jobseekers' => User::where('role', 'jobseeker')->count(),
            'pending_reports' => JobReport::where('status', 'pending')->count(),
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

        $recentReports = JobReport::query()
            ->with(['job:id,title', 'reporter:id,name'])
            ->latest('id')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('metrics', 'recentJobs', 'recentApplications', 'recentReports'));
    }
}
