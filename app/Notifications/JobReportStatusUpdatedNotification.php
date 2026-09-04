<?php

namespace App\Notifications;

use App\Models\JobReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobReportStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public JobReport $report,
        public string $type,
        public string $title,
        public string $message,
        public ?string $url = null
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
        $jobTitle = $this->report->job?->title ?? 'Lowongan';
        $employerName = $this->report->job?->employer?->business_name ?: $this->report->job?->employer?->name;

        $icon = match ($this->type) {
            'reviewed' => 'visibility',
            'action_taken' => 'verified',
            'employer_action' => 'gavel',
            'dismissed' => 'info',
            default => 'notifications',
        };

        $color = match ($this->type) {
            'reviewed' => 'amber',
            'action_taken' => 'emerald',
            'employer_action' => 'rose',
            'dismissed' => 'slate',
            default => 'brand',
        };

        return [
            'report_id' => $this->report->id,
            'job_id' => $this->report->job_id,
            'job_title' => $jobTitle,
            'employer_name' => $employerName,
            'status' => 'report_'.$this->type,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'icon' => $icon,
            'color' => $color,
            'url' => $this->url ?? route('reports.index'),
        ];
    }
}
