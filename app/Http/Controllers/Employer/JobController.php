<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Category;
use App\Models\Job;
use App\Models\Skill;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

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
            $data = $request->safe()->except(['skills', 'new_skills', 'cover_image', 'workplace_photos']);

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')->store('jobs/covers', 'public');
            }

            $job = $request->user()->jobs()->create($data);

            if ($request->hasFile('workplace_photos')) {
                foreach ($request->file('workplace_photos') as $idx => $photoFile) {
                    $path = $photoFile->store('jobs/workplace', 'public');
                    $job->workplacePhotos()->create([
                        'photo_path' => $path,
                        'sort_order' => $idx,
                    ]);
                }
            }

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
            'job' => $job->load(['skills:id', 'workplacePhotos']),
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
            $data = $request->safe()->except(['skills', 'new_skills', 'cover_image', 'remove_cover_image', 'workplace_photos', 'delete_workplace_photo_ids']);

            // Handle cover image replacement or removal
            if ($request->boolean('remove_cover_image')) {
                if ($job->cover_image && Storage::disk('public')->exists($job->cover_image)) {
                    Storage::disk('public')->delete($job->cover_image);
                }
                $data['cover_image'] = null;
            } elseif ($request->hasFile('cover_image')) {
                if ($job->cover_image && Storage::disk('public')->exists($job->cover_image)) {
                    Storage::disk('public')->delete($job->cover_image);
                }
                $data['cover_image'] = $request->file('cover_image')->store('jobs/covers', 'public');
            }

            $job->update($data);

            // Handle deletion of specific workplace photos
            $deletePhotoIds = $request->input('delete_workplace_photo_ids', []);
            if (! empty($deletePhotoIds)) {
                $photosToDelete = $job->workplacePhotos()->whereIn('id', $deletePhotoIds)->get();
                foreach ($photosToDelete as $photo) {
                    if (Storage::disk('public')->exists($photo->photo_path)) {
                        Storage::disk('public')->delete($photo->photo_path);
                    }
                    $photo->delete();
                }
            }

            // Handle new additional workplace photos
            if ($request->hasFile('workplace_photos')) {
                $currentMaxSort = (int) $job->workplacePhotos()->max('sort_order');
                foreach ($request->file('workplace_photos') as $idx => $photoFile) {
                    $path = $photoFile->store('jobs/workplace', 'public');
                    $job->workplacePhotos()->create([
                        'photo_path' => $path,
                        'sort_order' => $currentMaxSort + $idx + 1,
                    ]);
                }
            }

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

            // Always touch updated_at so timestamp is updated in database
            $job->touch();
        });

        return redirect()->route('jobs.show', $job)
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

        if ($job->cover_image && Storage::disk('public')->exists($job->cover_image)) {
            Storage::disk('public')->delete($job->cover_image);
        }
        foreach ($job->workplacePhotos as $photo) {
            if (Storage::disk('public')->exists($photo->photo_path)) {
                Storage::disk('public')->delete($photo->photo_path);
            }
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
            if ($job->closed_until && $job->closed_until->isPast()) {
                $job->update([
                    'closed_by_admin' => false,
                    'closed_reason' => null,
                    'closed_until' => null,
                ]);
            } else {
                return back()->with('error', 'Lowongan ini ditutup oleh Administrator ('.$job->closed_reason.'). Hubungi pengawas untuk evaluasi pembukaan kembali.');
            }
        }

        $job->update($validated);

        if ($validated['status'] === 'open') {
            $job->reports()
                ->where('status', 'action_taken')
                ->where('action_taken', 'like', '%tutup%')
                ->update([
                    'status' => 'resolved',
                    'action_taken' => 'Lowongan telah dibuka kembali (Masa Sanksi Berakhir)',
                ]);
        }

        return back()->with('success', 'Status lowongan berhasil diperbarui.');
    }
}
