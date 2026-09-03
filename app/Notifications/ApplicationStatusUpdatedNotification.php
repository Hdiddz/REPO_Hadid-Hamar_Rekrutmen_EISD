<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public JobApplication $application,
        public string $status,
        public ?string $customMessage = null
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

        $statusInfo = match ($this->status) {
            'accepted' => [
                'title' => 'Lamaran Diterima! 🎉',
                'message' => $this->customMessage ?: "Selamat! Lamaran Anda untuk posisi '{$jobTitle}' di {$employerName} telah DITERIMA.",
                'icon' => 'check_circle',
                'color' => 'emerald',
            ],
            'interview' => [
                'title' => 'Undangan Wawancara 📅',
                'message' => $this->customMessage ?: "Kabar baik! Anda diundang mengikuti wawancara posisi '{$jobTitle}' di {$employerName}.",
                'icon' => 'event',
                'color' => 'indigo',
            ],
            'reviewed' => [
                'title' => 'Lamaran Sedang Ditinjau 🔍',
                'message' => $this->customMessage ?: "Lamaran Anda untuk posisi '{$jobTitle}' sedang dipelajari oleh {$employerName}.",
                'icon' => 'visibility',
                'color' => 'amber',
            ],
            'rejected' => [
                'title' => 'Pembaruan Seleksi Lamaran',
                'message' => $this->customMessage ?: "Terima kasih telah melamar posisi '{$jobTitle}'. Sayangnya, proses belum dapat dilanjutkan kali ini.",
                'icon' => 'cancel',
                'color' => 'rose',
            ],
            default => [
                'title' => 'Status Lamaran Diperbarui',
                'message' => $this->customMessage ?: "Status lamaran untuk '{$jobTitle}' telah diperbarui menjadi {$this->status}.",
                'icon' => 'notifications',
                'color' => 'teal',
            ],
        };

        return [
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'job_title' => $jobTitle,
            'employer_name' => $employerName,
            'status' => $this->status,
            'title' => $statusInfo['title'],
            'message' => $statusInfo['message'],
            'icon' => $statusInfo['icon'],
            'color' => $statusInfo['color'],
            'url' => route('applications.index', ['status' => $this->status]),
        ];
    }
}
