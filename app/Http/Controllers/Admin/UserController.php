<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->withCount(['jobs', 'jobApplications'])
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Administrator tidak dapat menghapus akun sendiri.');
        }

        if ($user->hasRole('admin')) {
            return back()->with('error', 'Akun administrator lain tidak dapat dihapus melalui halaman ini.');
        }

        $resumeFiles = JobApplication::query()
            ->where('user_id', $user->id)
            ->orWhereHas('job', fn ($query) => $query->where('employer_id', $user->id))
            ->pluck('resume_file')
            ->unique()
            ->all();

        DB::transaction(fn () => $user->delete());
        Storage::disk('local')->delete($resumeFiles);

        return back()->with('success', 'Akun pengguna dan data terkait berhasil dihapus.');
    }
}
