<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewApplicationReceivedNotification extends Notification
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
        $applicantName = $this->application->user->name;
        $jobTitle = $this->application->job->title;

        return [
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'job_title' => $jobTitle,
            'applicant_name' => $applicantName,
            'status' => 'new_applicant',
            'title' => 'Pelamar Baru Masuk 👤',
            'message' => "{$applicantName} baru saja mengirimkan lamaran kerja untuk posisi '{$jobTitle}'.",
            'icon' => 'person_add',
            'color' => 'teal',
            'url' => route('employer.applications.index'),
        ];
    }
}
