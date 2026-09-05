<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobComplianceWarningNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Job $job,
        public string $category,
        public string $warningMessage,
        public bool $alsoClosed = false
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $closureText = $this->alsoClosed ? ' dan lowongan telah ditutup sementara oleh Administrator.' : '. Silakan lakukan perbaikan segera.';

        return [
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'employer_name' => $this->job->employer?->business_name ?: $this->job->employer?->name,
            'status' => 'job_warning',
            'type' => 'compliance_warning',
            'category' => $this->category,
            'title' => '⚠️ Peringatan Kepatuhan Lowongan: '.$this->job->title,
            'message' => "Lowongan '{$this->job->title}' menerima catatan peringatan kepatuhan: \"{$this->warningMessage}\"{$closureText}",
            'icon' => 'warning',
            'color' => 'amber',
            'url' => route('employer.dashboard'),
        ];
    }
}
