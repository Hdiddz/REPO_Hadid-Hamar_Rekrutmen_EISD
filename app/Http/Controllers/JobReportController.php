<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobReport;
use App\Models\User;
use App\Notifications\NewJobReportNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobReportController extends Controller
{
    /**
     * Display a listing of reports submitted by the authenticated user.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasRole('admin')) {
            return redirect()->route('admin.reports.index');
        }

        $status = $request->query('status');

        $userReports = JobReport::where('reporter_id', $request->user()->id)
            ->whereNull('reporter_hidden_at');

        $counts = [
            'all' => (clone $userReports)->count(),
            'pending' => (clone $userReports)->where('status', 'pending')->count(),
            'reviewed' => (clone $userReports)->where('status', 'reviewed')->count(),
            'action_taken' => (clone $userReports)->where('status', 'action_taken')->count(),
            'dismissed' => (clone $userReports)->whereIn('status', ['resolved', 'dismissed'])->count(),
        ];

        $reports = JobReport::query()
            ->where('reporter_id', $request->user()->id)
            ->whereNull('reporter_hidden_at')
            ->when($status, function ($query) use ($status): void {
                if ($status === 'dismissed' || $status === 'resolved') {
                    $query->whereIn('status', ['resolved', 'dismissed']);
                } elseif (in_array($status, ['pending', 'reviewed', 'action_taken'], true)) {
                    $query->where('status', $status);
                }
            })
            ->with(['job.category:id,name', 'job.employer:id,name,business_name'])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('reports.index', compact('reports', 'counts'));
    }

    public function destroy(Request $request, JobReport $report): RedirectResponse
    {
        if ($report->reporter_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus riwayat laporan ini.');
        }

        $report->update(['reporter_hidden_at' => now()]);

        return back()->with('success', 'Laporan berhasil dihapus dari riwayat laporan Anda.');
    }

    public function store(Request $request, Job $job): RedirectResponse
    {
        if ($job->employer_id === $request->user()->id) {
            abort(403, 'Anda tidak dapat melaporkan lowongan pekerjaan milik Anda sendiri.');
        }

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
