<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkillRequest;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function store(StoreSkillRequest $request): RedirectResponse
    {
        Skill::create($request->validated());

        return back()->with('success', 'Keahlian berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSkillRequest $request, Skill $skill): RedirectResponse
    {
        $skill->update($request->validated());

        return back()->with('success', 'Keahlian berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill): RedirectResponse
    {
        if ($skill->jobs()->exists()) {
            return back()->with('error', 'Keahlian masih digunakan oleh lowongan dan tidak dapat dihapus.');
        }

        $skill->delete();

        return back()->with('success', 'Keahlian berhasil dihapus.');
    }
}
