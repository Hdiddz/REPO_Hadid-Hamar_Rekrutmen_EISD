<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Notifications\NewChatMessageNotification;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        return view('chat.index', [
            'activeUserId' => $request->query('user'),
        ]);
    }

    public function conversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // User IDs with exchanged messages
        $messagedUserIds = ChatMessage::query()
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->selectRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as other_id', [$userId])
            ->pluck('other_id');

        // Contextual contacts: applicants if employer, employers if jobseeker
        $contextUserIds = collect();
        if ($request->user()->hasRole('employer')) {
            $contextUserIds = JobApplication::query()
                ->whereHas('job', fn ($q) => $q->where('employer_id', $userId))
                ->pluck('user_id');
        } elseif ($request->user()->hasRole('jobseeker')) {
            $contextUserIds = Job::query()
                ->whereHas('applications', fn ($q) => $q->where('user_id', $userId))
                ->pluck('employer_id');
        }

        if ($request->filled('with')) {
            $contextUserIds->push((int) $request->input('with'));
        }

        $allUserIds = $messagedUserIds->merge($contextUserIds)->unique()->filter(fn ($id) => (int) $id !== (int) $userId)->values();

        if ($allUserIds->isEmpty()) {
            return response()->json([]);
        }

        $users = User::whereIn('id', $allUserIds)->get()->map(function (User $user) use ($userId) {
            $lastMsg = ChatMessage::query()
                ->where(fn ($q) => $q->where('sender_id', $userId)->where('receiver_id', $user->id))
                ->orWhere(fn ($q) => $q->where('sender_id', $user->id)->where('receiver_id', $userId))
                ->latest()
                ->first();

            $unreadCount = ChatMessage::query()
                ->where('sender_id', $user->id)
                ->where('receiver_id', $userId)
                ->where('is_read', false)
                ->count();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'business_name' => $user->business_name,
                'role' => $user->role,
                'role_label' => $user->role === 'employer' ? ($user->business_name ?: 'Mitra UMKM') : ($user->role === 'admin' ? 'Super Admin' : 'Pelamar / Pencari Kerja'),
                'initials' => strtoupper(substr($user->name, 0, 2)),
                'last_message' => $lastMsg ? $lastMsg->message : null,
                'last_time' => $lastMsg ? $lastMsg->created_at->diffForHumans() : null,
                'unread_count' => $unreadCount,
            ];
        });

        return response()->json($users);
    }

    public function messages(Request $request, User $user): JsonResponse
    {
        $userId = $request->user()->id;

        ChatMessage::query()
            ->where('sender_id', $user->id)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = ChatMessage::query()
            ->with(['replyTo.sender'])
            ->where(fn ($q) => $q->where('sender_id', $userId)->where('receiver_id', $user->id))
            ->orWhere(fn ($q) => $q->where('sender_id', $user->id)->where('receiver_id', $userId))
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn (ChatMessage $msg) => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'is_me' => $msg->sender_id === $userId,
                'message' => $msg->message,
                'time' => $msg->created_at->format('H:i'),
                'date' => $msg->created_at->translatedFormat('d M Y'),
                'reply_to' => $msg->replyTo ? [
                    'id' => $msg->replyTo->id,
                    'sender_name' => $msg->replyTo->sender_id === $userId ? 'Anda' : ($msg->replyTo->sender?->name ?? 'Pengguna'),
                    'message' => Str::limit($msg->replyTo->message, 90),
                ] : null,
            ]);

        $pendingResignation = null;
        if ($request->user()->hasRole('employer')) {
            $pendingResignationApp = JobApplication::query()
                ->where('user_id', $user->id)
                ->where('resignation_status', 'pending')
                ->whereHas('job', fn ($q) => $q->where('employer_id', $userId))
                ->with('job:id,title')
                ->first();

            if ($pendingResignationApp) {
                $pendingResignation = [
                    'application_id' => $pendingResignationApp->id,
                    'job_title' => $pendingResignationApp->job->title,
                    'resignation_date' => $pendingResignationApp->resignation_date ? $pendingResignationApp->resignation_date->translatedFormat('l, d F Y') : null,
                    'resignation_reason' => $pendingResignationApp->resignation_reason,
                    'resignation_notes' => $pendingResignationApp->resignation_notes,
                ];
            }
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'business_name' => $user->business_name,
                'role' => $user->role,
                'role_label' => $user->role === 'employer' ? ($user->business_name ?: 'Mitra UMKM') : ($user->role === 'admin' ? 'Super Admin' : 'Pelamar / Pencari Kerja'),
                'initials' => strtoupper(substr($user->name, 0, 2)),
            ],
            'messages' => $messages,
            'pending_resignation' => $pendingResignation,
        ]);
    }

    public function sendMessage(Request $request, User $user): JsonResponse
    {
        $authenticatedUserId = $request->user()->id;

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'reply_to_id' => [
                'nullable',
                'integer',
                Rule::exists('chat_messages', 'id')->where(function (Builder $query) use ($authenticatedUserId, $user): void {
                    $query->where(function (Builder $conversation) use ($authenticatedUserId, $user): void {
                        $conversation
                            ->where('sender_id', $authenticatedUserId)
                            ->where('receiver_id', $user->id);
                    })->orWhere(function (Builder $conversation) use ($authenticatedUserId, $user): void {
                        $conversation
                            ->where('sender_id', $user->id)
                            ->where('receiver_id', $authenticatedUserId);
                    });
                }),
            ],
        ], [
            'reply_to_id.exists' => 'Pesan yang dibalas bukan bagian dari percakapan ini.',
        ]);

        $message = ChatMessage::create([
            'sender_id' => $authenticatedUserId,
            'receiver_id' => $user->id,
            'reply_to_id' => $validated['reply_to_id'] ?? null,
            'message' => trim($validated['message']),
            'is_read' => false,
        ]);

        $message->load('replyTo.sender');
        $message->setRelation('sender', $request->user());

        $user->notify(new NewChatMessageNotification($message));

        return response()->json([
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'is_me' => true,
            'message' => $message->message,
            'time' => $message->created_at->format('H:i'),
            'reply_to' => $message->replyTo ? [
                'id' => $message->replyTo->id,
                'sender_name' => $message->replyTo->sender_id === $request->user()->id ? 'Anda' : ($message->replyTo->sender?->name ?? 'Pengguna'),
                'message' => Str::limit($message->replyTo->message, 90),
            ] : null,
        ], 201);
    }

    public function deleteMessage(Request $request, ChatMessage $message): JsonResponse
    {
        $userId = $request->user()->id;

        if ($message->sender_id !== $userId && $message->receiver_id !== $userId) {
            return response()->json(['error' => 'Anda tidak memiliki otorisasi untuk menghapus pesan ini.'], 403);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dihapus.',
        ]);
    }

    public function clearChat(Request $request, User $user): JsonResponse
    {
        $userId = $request->user()->id;

        ChatMessage::query()
            ->where(fn ($q) => $q->where('sender_id', $userId)->where('receiver_id', $user->id))
            ->orWhere(fn ($q) => $q->where('sender_id', $user->id)->where('receiver_id', $userId))
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Seluruh riwayat pesan obrolan berhasil dibersihkan.',
        ]);
    }

    public function removeConversation(Request $request, User $user): JsonResponse
    {
        $userId = $request->user()->id;

        ChatMessage::query()
            ->where(fn ($q) => $q->where('sender_id', $userId)->where('receiver_id', $user->id))
            ->orWhere(fn ($q) => $q->where('sender_id', $user->id)->where('receiver_id', $userId))
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Percakapan dengan {$user->name} berhasil dihapus dari daftar kontak.",
        ]);
    }
}
