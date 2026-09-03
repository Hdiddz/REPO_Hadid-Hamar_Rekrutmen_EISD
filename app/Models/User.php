<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'phone', 'avatar', 'business_name', 'banned_at', 'banned_until', 'ban_reason'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'banned_at' => 'datetime',
            'banned_until' => 'datetime',
        ];
    }

    public function isBanned(): bool
    {
        if (! $this->banned_at) {
            return false;
        }

        if ($this->banned_until === null) {
            return true;
        }

        return $this->banned_until->isFuture();
    }

    public function getBanStatusTextAttribute(): string
    {
        if (! $this->isBanned()) {
            return 'Aktif';
        }

        if (! $this->banned_until) {
            return 'Diblokir Permanen';
        }

        return 'Diblokir s.d '.$this->banned_until->translatedFormat('d M Y');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'employer_id');
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function submittedReports(): HasMany
    {
        return $this->hasMany(JobReport::class, 'reporter_id');
    }

    public function appliedJobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_applications')
            ->withPivot(['id', 'resume_file', 'note', 'status'])
            ->withTimestamps();
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        return asset('storage/'.$this->avatar);
    }
}
