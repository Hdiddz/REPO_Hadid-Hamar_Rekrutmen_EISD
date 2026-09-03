<?php

namespace App\Models;

use Database\Factories\JobFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['employer_id', 'category_id', 'title', 'description', 'location', 'salary_type', 'salary_amount', 'work_hours_per_day', 'status', 'closed_reason', 'closed_until', 'closed_by_admin'])]
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
}
