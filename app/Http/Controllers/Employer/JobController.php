<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Category;
use App\Models\Job;
use App\Models\Skill;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('employer.dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Job::class);

        return view('employer.jobs.create', [
            'job' => null,
            'categories' => Category::orderBy('name')->get(),
            'skills' => Skill::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request): RedirectResponse
    {
        $job = DB::transaction(function () use ($request): Job {
            $job = $request->user()->jobs()->create($request->safe()->except(['skills', 'new_skills']));

            $skillIds = collect($request->input('skills', []))->map(fn ($id) => (int) $id);
            if ($request->has('new_skills')) {
                foreach ((array) $request->input('new_skills') as $name) {
                    $cleanName = trim(strip_tags((string) $name));
                    if ($cleanName !== '') {
                        $skill = Skill::firstOrCreate(['name' => $cleanName]);
                        $skillIds->push($skill->id);
                    }
                }
            }

            $job->skills()->sync($skillIds->unique()->values()->all());

            return $job;
        });

        return redirect()->route('employer.dashboard')
            ->with('success', "Lowongan {$job->title} berhasil dipublikasikan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job): RedirectResponse
    {
        Gate::authorize('view', $job);

        return redirect()->route('jobs.show', $job);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job): View
    {
        Gate::authorize('update', $job);

        return view('employer.jobs.create', [
            'job' => $job->load('skills:id'),
            'categories' => Category::orderBy('name')->get(),
            'skills' => Skill::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, Job $job): RedirectResponse
    {
        DB::transaction(function () use ($request, $job): void {
            $job->update($request->safe()->except(['skills', 'new_skills']));

            $skillIds = collect($request->input('skills', []))->map(fn ($id) => (int) $id);
            if ($request->has('new_skills')) {
                foreach ((array) $request->input('new_skills') as $name) {
                    $cleanName = trim(strip_tags((string) $name));
                    if ($cleanName !== '') {
                        $skill = Skill::firstOrCreate(['name' => $cleanName]);
                        $skillIds->push($skill->id);
                    }
                }
            }

            $job->skills()->sync($skillIds->unique()->values()->all());
        });

        return redirect()->route('employer.dashboard')
            ->with('success', 'Lowongan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job): RedirectResponse
    {
        Gate::authorize('delete', $job);

        if ($job->applications()->exists()) {
            $job->update(['status' => 'closed']);

            return back()->with('warning', 'Lowongan memiliki pelamar sehingga ditutup dan dipertahankan untuk riwayat.');
        }

        $job->delete();

        return back()->with('success', 'Lowongan berhasil dihapus.');
    }

    public function updateStatus(Request $request, Job $job): RedirectResponse
    {
        Gate::authorize('update', $job);
        $validated = $request->validate([
            'status' => ['required', 'in:open,closed'],
        ], [
            'status.in' => 'Status lowongan tidak valid.',
        ]);

        if ($job->isClosedByAdmin() && $validated['status'] === 'open') {
            return back()->with('error', 'Lowongan ini ditutup oleh Administrator ('.$job->closed_reason.'). Hubungi pengawas untuk evaluasi pembukaan kembali.');
        }

        $job->update($validated);

        return back()->with('success', 'Status lowongan berhasil diperbarui.');
    }

    public function destroySkill(Skill $skill): JsonResponse
    {
        $name = $skill->name;
        $skill->jobs()->detach();
        $skill->delete();

        return response()->json([
            'success' => true,
            'message' => "Keterampilan '{$name}' berhasil dihapus.",
        ]);
    }
}
