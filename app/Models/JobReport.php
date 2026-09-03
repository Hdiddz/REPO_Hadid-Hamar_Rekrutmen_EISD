<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['job_id', 'reporter_id', 'reason', 'details', 'status', 'admin_notes', 'action_taken'])]
class JobReport extends Model
{
    use HasFactory;

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
