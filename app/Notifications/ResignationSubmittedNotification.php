<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ResignationSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public JobApplication $application
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
        $candidate = $this->application->user;
        $job = $this->application->job;
        $dateFormatted = $this->application->resignation_date
            ? Carbon::parse($this->application->resignation_date)->locale('id')->translatedFormat('d M Y')
            : 'segera';

        return [
            'application_id' => $this->application->id,
            'job_id' => $job->id,
            'job_title' => $job->title,
            'candidate_name' => $candidate?->name ?? 'Kandidat',
            'status' => 'resignation_requested',
            'title' => 'Pengajuan Resign Masuk 📄',
            'message' => "Kandidat {$candidate?->name} mengajukan pengunduran diri dari posisi '{$job->title}' (efektif: {$dateFormatted}). Alasan: {$this->application->resignation_reason}.",
            'icon' => 'exit_to_app',
            'color' => 'rose',
            'url' => route('employer.applications.index', ['status' => 'accepted']),
        ];
    }
}
