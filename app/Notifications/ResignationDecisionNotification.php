<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ResignationDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public JobApplication $application,
        public string $decision,
        public string $responseMessage
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
        $job = $this->application->job;
        $employer = $job->employer;
        $employerName = $employer->business_name ?: $employer->name;

        if ($this->decision === 'approved') {
            return [
                'application_id' => $this->application->id,
                'job_id' => $job->id,
                'job_title' => $job->title,
                'employer_name' => $employerName,
                'status' => 'resignation_approved',
                'title' => 'Pengajuan Resign Disetujui ✅',
                'message' => "Permohonan resign Anda untuk posisi '{$job->title}' telah DISETUJUI oleh {$employerName}. Pesan: \"{$this->responseMessage}\"",
                'icon' => 'check_circle',
                'color' => 'emerald',
                'url' => route('applications.index', ['status' => 'accepted']),
            ];
        }

        return [
            'application_id' => $this->application->id,
            'job_id' => $job->id,
            'job_title' => $job->title,
            'employer_name' => $employerName,
            'status' => 'resignation_rejected',
            'title' => 'Pengajuan Resign Belum Disetujui ℹ️',
            'message' => "Permohonan resign Anda untuk posisi '{$job->title}' belum dapat disetujui oleh {$employerName}. Catatan: \"{$this->responseMessage}\"",
            'icon' => 'info',
            'color' => 'rose',
            'url' => route('applications.index', ['status' => 'accepted']),
        ];
    }
}
