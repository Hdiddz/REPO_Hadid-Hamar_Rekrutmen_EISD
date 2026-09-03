<?php

namespace App\Notifications;

use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewChatMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ChatMessage $message
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
        $sender = $this->message->sender;
        $senderName = $sender->business_name ?: $sender->name;

        return [
            'message_id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $senderName,
            'status' => 'chat_message',
            'title' => "Pesan Baru dari {$senderName} 💬",
            'message' => Str::limit($this->message->message, 120),
            'icon' => 'chat',
            'color' => 'teal',
            'url' => route('chat.index', ['user' => $sender->id]),
        ];
    }
}
