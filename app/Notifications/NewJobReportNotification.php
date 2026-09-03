<?php

namespace App\Notifications;

use App\Models\JobReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewJobReportNotification extends Notification
{
    use Queueable;

    public function __construct(
        public JobReport $report
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
        $jobTitle = $this->report->job->title ?? 'Lowongan';
        $reporterName = $this->report->reporter->name ?? 'Pengguna';

        return [
            'report_id' => $this->report->id,
            'job_id' => $this->report->job_id,
            'job_title' => $jobTitle,
            'reporter_name' => $reporterName,
            'reason' => $this->report->reason,
            'status' => 'report_pending',
            'title' => 'Laporan Lowongan Baru ⚠️',
            'message' => "Lowongan '{$jobTitle}' dilaporkan oleh {$reporterName} dengan alasan: {$this->report->reason}.",
            'icon' => 'flag',
            'color' => 'rose',
            'url' => route('admin.reports.index'),
        ];
    }
}
