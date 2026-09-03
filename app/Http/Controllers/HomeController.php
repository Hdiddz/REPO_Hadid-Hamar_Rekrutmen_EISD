<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->hasRole('employer')) {
                return redirect()->route('employer.dashboard');
            }
            if (Auth::user()->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('jobs.index');
        }

        $featuredJobs = Job::query()
            ->where('status', 'open')
            ->with(['category:id,name', 'employer:id,name,business_name'])
            ->withCount('applications')
            ->latest()
            ->limit(4)
            ->get();

        $metrics = [
            'open_jobs' => Job::where('status', 'open')->count(),
            'employers' => User::where('role', 'employer')->count(),
            'accepted_workers' => JobApplication::where('status', 'accepted')->count(),
        ];

        return view('home', compact('featuredJobs', 'metrics'));
    }
}
