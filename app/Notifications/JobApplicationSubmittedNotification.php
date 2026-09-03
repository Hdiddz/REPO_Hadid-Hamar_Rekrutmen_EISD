<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobApplicationSubmittedNotification extends Notification
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
        $jobTitle = $this->application->job->title;
        $employer = $this->application->job->employer;
        $employerName = $employer->business_name ?: $employer->name;

        $submittedAt = Carbon::parse($this->application->created_at)->locale('id');
        $dayName = $submittedAt->translatedFormat('l');
        $dateFormatted = $submittedAt->translatedFormat('d F Y');
        $timeFormatted = $submittedAt->format('H:i');

        return [
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'job_title' => $jobTitle,
            'employer_name' => $employerName,
            'status' => 'application_submitted',
            'title' => 'Lamaran Berhasil Dikirim 📝',
            'message' => "Anda telah melamar pada lowongan '{$jobTitle}' di {$employerName} pada hari {$dayName}, {$dateFormatted} pukul {$timeFormatted} WIB.",
            'icon' => 'task_alt',
            'color' => 'teal',
            'url' => route('applications.index'),
        ];
    }
}
