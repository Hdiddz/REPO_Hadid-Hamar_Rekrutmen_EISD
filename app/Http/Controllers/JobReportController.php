<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobReport;
use App\Models\User;
use App\Notifications\NewJobReportNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobReportController extends Controller
{
    public function store(Request $request, Job $job): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'reason.required' => 'Kategori alasan laporan wajib dipilih.',
            'details.required' => 'Rincian penjelasan laporan wajib diisi minimal 10 karakter.',
            'details.min' => 'Rincian penjelasan laporan minimal 10 karakter.',
        ]);

        $report = JobReport::create([
            'job_id' => $job->id,
            'reporter_id' => $request->user()->id,
            'reason' => $validated['reason'],
            'details' => trim($validated['details']),
            'status' => 'pending',
        ]);

        $report->setRelation('job', $job);
        $report->setRelation('reporter', $request->user());

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewJobReportNotification($report));
        }

        return back()->with('success', 'Laporan Anda telah diterima oleh Tim Pengawas KerjaLokal untuk ditinjau.');
    }
}
