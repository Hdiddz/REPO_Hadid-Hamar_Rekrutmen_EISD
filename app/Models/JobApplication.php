<?php

namespace App\Models;

use Database\Factories\JobApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'job_id',
    'user_id',
    'resume_file',
    'note',
    'status',
    'start_date',
    'acceptance_notes',
    'interview_date',
    'interview_time',
    'interview_type',
    'interview_location',
    'interview_notes',
    'rejection_reason',
    'rejection_notes',
    'resignation_date',
    'resignation_reason',
    'resignation_notes',
    'resignation_status',
    'resigned_at',
])]
class JobApplication extends Model
{
    /** @use HasFactory<JobApplicationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'interview_date' => 'date',
            'resignation_date' => 'date',
            'resigned_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
