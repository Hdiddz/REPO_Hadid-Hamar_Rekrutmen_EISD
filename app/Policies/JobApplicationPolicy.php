<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('jobseeker', 'employer');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JobApplication $jobApplication): bool
    {
        return $jobApplication->user_id === $user->id
            || ($user->hasRole('employer') && $jobApplication->job()->where('employer_id', $user->id)->exists());
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('jobseeker');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobApplication $jobApplication): bool
    {
        return $user->hasRole('employer') && $jobApplication->job()->where('employer_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobApplication $jobApplication): bool
    {
        if ($jobApplication->status === 'accepted') {
            return false;
        }

        return $jobApplication->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JobApplication $jobApplication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JobApplication $jobApplication): bool
    {
        return false;
    }

    public function download(User $user, JobApplication $jobApplication): bool
    {
        return $this->view($user, $jobApplication);
    }
}
