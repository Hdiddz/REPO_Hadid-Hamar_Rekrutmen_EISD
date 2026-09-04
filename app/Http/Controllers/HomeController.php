<?php

namespace App\Http\Controllers;

use App\Models\Job;
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
            ->select(['id', 'employer_id', 'category_id', 'title', 'location', 'salary_amount', 'salary_type', 'created_at'])
            ->where('status', 'open')
            ->with([
                'category:id,name,slug',
                'employer:id,name,business_name',
            ])
            ->latest()
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('home', compact('featuredJobs'));
    }
}
