<?php

namespace App\Models;

use Database\Factories\JobFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['employer_id', 'category_id', 'title', 'description', 'cover_image', 'location', 'salary_type', 'salary_amount', 'work_hours_per_day', 'status', 'closed_reason', 'closed_until', 'closed_by_admin', 'admin_warning_category', 'admin_warning_message', 'admin_warned_at'])]
class Job extends Model
{
    /** @use HasFactory<JobFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'salary_amount' => 'decimal:2',
            'work_hours_per_day' => 'integer',
            'closed_until' => 'datetime',
            'closed_by_admin' => 'boolean',
            'admin_warned_at' => 'datetime',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class)->withTimestamps();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(JobReport::class);
    }

    public function workplacePhotos(): HasMany
    {
        return $this->hasMany(JobWorkplacePhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    public function applicants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'job_applications')
            ->withPivot(['id', 'resume_file', 'note', 'status'])
            ->withTimestamps();
    }

    public function isClosedByAdmin(): bool
    {
        return $this->closed_by_admin && $this->status === 'closed';
    }

    public function hasAdminWarning(): bool
    {
        return ! empty($this->admin_warning_message) && ! empty($this->admin_warned_at);
    }

    public function getAdminWarningCategoryLabelAttribute(): ?string
    {
        return match ($this->admin_warning_category) {
            'salary_not_standard' => 'Upah Tidak Sesuai / Di Bawah Standar Kelayakan',
            'excessive_hours' => 'Jam Kerja Melebihi Batas Etis (>8 jam/hari)',
            'misleading_info' => 'Informasi Lowongan Tidak Jelas / Menyesatkan',
            'unethical_conditions' => 'Persyaratan Kerja Tidak Wajar / Diskriminatif',
            default => 'Pelanggaran Standar Kepatuhan',
        };
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')) {
            return $this->cover_image;
        }

        return asset('storage/'.$this->cover_image);
    }
}
