<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterviewResponseNotification extends Notification
{
    use Queueable;

    public function __construct(
        public JobApplication $application,
        public string $responseType,
        public ?string $responseNotes = null,
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

        $title = match ($this->responseType) {
            'confirmed' => 'Wawancara Disetujui Pelamar ✅',
            'reschedule_requested' => 'Pelamar Mengajukan Diskusi Jadwal 💬',
            'declined' => 'Wawancara Ditolak Pelamar ❌',
            default => 'Tanggapan Undangan Wawancara',
        };

        $message = match ($this->responseType) {
            'confirmed' => "Kandidat {$candidate?->name} menyetujui jadwal wawancara untuk posisi '{$job?->title}'.",
            'reschedule_requested' => "Kandidat {$candidate?->name} mengajukan permohonan diskusi/penyesuaian jadwal wawancara untuk posisi '{$job?->title}'.".($this->responseNotes ? " Catatan: {$this->responseNotes}" : ''),
            'declined' => "Kandidat {$candidate?->name} menyatakan tidak dapat menghadiri wawancara untuk posisi '{$job?->title}'.".($this->responseNotes ? " Alasan: {$this->responseNotes}" : ''),
            default => "Kandidat {$candidate?->name} telah memberikan tanggapan untuk wawancara '{$job?->title}'.",
        };

        $icon = match ($this->responseType) {
            'confirmed' => 'event_available',
            'reschedule_requested' => 'edit_calendar',
            'declined' => 'event_busy',
            default => 'event',
        };

        $color = match ($this->responseType) {
            'confirmed' => 'emerald',
            'reschedule_requested' => 'sky',
            'declined' => 'rose',
            default => 'indigo',
        };

        return [
            'application_id' => $this->application->id,
            'job_id' => $job?->id,
            'job_title' => $job?->title,
            'candidate_name' => $candidate?->name ?? 'Kandidat',
            'status' => 'interview_'.$this->responseType,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'color' => $color,
            'url' => route('chat.index', ['user' => $candidate?->id]),
        ];
    }
}
