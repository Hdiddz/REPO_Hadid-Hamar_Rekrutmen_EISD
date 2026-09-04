<?php

namespace App\Models;

use Carbon\Carbon;
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
    'interview_status',
    'rejection_reason',
    'rejection_notes',
    'rejected_at',
    'resignation_date',
    'resignation_reason',
    'resignation_notes',
    'resignation_status',
    'resigned_at',
    'jobseeker_hidden_at',
    'employer_hidden_at',
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
            'rejected_at' => 'datetime',
            'jobseeker_hidden_at' => 'datetime',
            'employer_hidden_at' => 'datetime',
        ];
    }

    public function rejectionDate(): ?Carbon
    {
        return $this->rejected_at ?: $this->updated_at;
    }

    public function canBeReapplied(): bool
    {
        if ($this->status !== 'rejected') {
            return false;
        }

        $rejectionDate = $this->rejectionDate();
        if (! $rejectionDate) {
            return true;
        }

        return now()->toDateString() > $rejectionDate->toDateString();
    }

    public function canBeReappliedTomorrow(): bool
    {
        if ($this->status !== 'rejected') {
            return false;
        }

        $rejectionDate = $this->rejectionDate();
        if (! $rejectionDate) {
            return false;
        }

        return now()->toDateString() <= $rejectionDate->toDateString();
    }

    public function reapplyAvailableAt(): ?Carbon
    {
        $rejectionDate = $this->rejectionDate();
        if (! $rejectionDate) {
            return null;
        }

        return $rejectionDate->copy()->addDay()->startOfDay();
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
