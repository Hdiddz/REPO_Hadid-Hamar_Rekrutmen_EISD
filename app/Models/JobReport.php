<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['job_id', 'reporter_id', 'reason', 'details', 'status', 'admin_notes', 'action_taken', 'reporter_hidden_at', 'admin_hidden_at'])]
class JobReport extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reporter_hidden_at' => 'datetime',
            'admin_hidden_at' => 'datetime',
        ];
    }

    /**
     * Scope a query to only include reports visible to admin (not removed from history).
     */
    public function scopeVisibleToAdmin(Builder $query): Builder
    {
        return $query->whereNull('admin_hidden_at');
    }

    /**
     * Scope a query to only include reports visible to the reporter.
     */
    public function scopeVisibleToReporter(Builder $query): Builder
    {
        return $query->whereNull('reporter_hidden_at');
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isDismissed(): bool
    {
        return $this->status === 'dismissed';
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, ['resolved', 'dismissed'], true);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
