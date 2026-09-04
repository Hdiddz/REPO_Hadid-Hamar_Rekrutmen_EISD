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
                ->latest('created_at')
                ->latest('id')
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
                'last_message' => $lastMsg ? ($lastMsg->is_deleted ? 'Pesan ini telah dihapus' : $lastMsg->message) : null,
                'last_time' => $lastMsg ? $lastMsg->created_at->diffForHumans() : null,
                'last_time_raw' => $lastMsg?->created_at?->timestamp ?? 0,
                'unread_count' => $unreadCount,
            ];
        })->sortByDesc(fn (array $u) => [$u['unread_count'] > 0 ? 1 : 0, $u['last_time_raw'], $u['id']])->values();

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
                'is_deleted' => (bool) $msg->is_deleted,
                'message' => $msg->is_deleted ? 'Pesan ini telah dihapus' : $msg->message,
                'time' => $msg->created_at->format('H:i'),
                'date' => $msg->created_at->translatedFormat('d M Y'),
                'reply_to' => $msg->replyTo ? [
                    'id' => $msg->replyTo->id,
                    'sender_name' => $msg->replyTo->sender_id === $userId ? 'Anda' : ($msg->replyTo->sender?->name ?? 'Pengguna'),
                    'message' => $msg->replyTo->is_deleted ? 'Pesan ini telah dihapus' : Str::limit($msg->replyTo->message, 90),
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

        $interviewInvitation = null;
        if ($request->user()->hasRole('jobseeker')) {
            $interviewApp = JobApplication::query()
                ->where('user_id', $userId)
                ->where('status', 'interview')
                ->whereHas('job', fn ($q) => $q->where('employer_id', $user->id))
                ->with('job:id,title')
                ->latest('updated_at')
                ->first();

            if ($interviewApp) {
                $interviewInvitation = [
                    'application_id' => $interviewApp->id,
                    'job_title' => $interviewApp->job?->title,
                    'interview_date' => $interviewApp->interview_date ? $interviewApp->interview_date->locale('id')->translatedFormat('l, d F Y') : null,
                    'interview_time' => $interviewApp->interview_time ? $interviewApp->interview_time.' WIB' : null,
                    'interview_type' => $interviewApp->interview_type ?: 'Wawancara Langsung',
                    'interview_location' => $interviewApp->interview_location,
                    'interview_notes' => $interviewApp->interview_notes,
                    'interview_status' => $interviewApp->interview_status ?: 'pending',
                    'can_respond' => true,
                ];
            }
        } elseif ($request->user()->hasRole('employer')) {
            $interviewApp = JobApplication::query()
                ->where('user_id', $user->id)
                ->where('status', 'interview')
                ->whereHas('job', fn ($q) => $q->where('employer_id', $userId))
                ->with('job:id,title')
                ->latest('updated_at')
                ->first();

            if ($interviewApp) {
                $interviewInvitation = [
                    'application_id' => $interviewApp->id,
                    'job_title' => $interviewApp->job?->title,
                    'interview_date' => $interviewApp->interview_date ? $interviewApp->interview_date->locale('id')->translatedFormat('l, d F Y') : null,
                    'interview_time' => $interviewApp->interview_time ? $interviewApp->interview_time.' WIB' : null,
                    'interview_type' => $interviewApp->interview_type ?: 'Wawancara Langsung',
                    'interview_location' => $interviewApp->interview_location,
                    'interview_notes' => $interviewApp->interview_notes,
                    'interview_status' => $interviewApp->interview_status ?: 'pending',
                    'can_respond' => false,
                ];
            }
        }

        $profile = null;
        if ($user->role === 'jobseeker') {
            $applicantAppsQuery = JobApplication::query()
                ->where('user_id', $user->id)
                ->with(['job:id,title,location,salary_type,salary_amount,employer_id'])
                ->latest('id');

            if ($request->user()->hasRole('employer')) {
                $applicantAppsQuery->whereHas('job', fn ($q) => $q->where('employer_id', $userId));
            }

            $applicantApplications = $applicantAppsQuery->get()->map(function ($app) use ($request) {
                return [
                    'id' => $app->id,
                    'job_title' => $app->job?->title ?? 'Posisi Pekerjaan',
                    'status' => $app->status,
                    'status_label' => match ($app->status) {
                        'pending' => 'Menunggu Tinjauan',
                        'interview' => 'Wawancara',
                        'accepted' => 'Diterima',
                        'rejected' => 'Belum Lolos',
                        'resigned' => 'Resign',
                        default => ucfirst($app->status),
                    },
                    'interview_status' => $app->interview_status ?: 'pending',
                    'interview_status_label' => match ($app->interview_status) {
                        'confirmed' => 'Disetujui Pelamar',
                        'reschedule_requested' => 'Diskusi Jadwal Diajukan',
                        'declined' => 'Ditolak Pelamar',
                        default => 'Menunggu Konfirmasi',
                    },
                    'interview_date' => $app->interview_date ? $app->interview_date->locale('id')->translatedFormat('d M Y') : null,
                    'interview_time' => $app->interview_time ? $app->interview_time.' WIB' : null,
                    'interview_type' => $app->interview_type,
                    'interview_location' => $app->interview_location,
                    'note' => $app->note,
                    'has_resume' => (bool) $app->resume_file,
                    'resume_preview_url' => $request->user()->hasRole('employer')
                        ? route('employer.applications.resume.preview', $app)
                        : ($request->user()->hasRole('admin') ? route('admin.applications.resume.preview', $app) : null),
                    'resume_download_url' => $request->user()->hasRole('employer')
                        ? route('employer.applications.resume', $app)
                        : ($request->user()->hasRole('admin') ? route('admin.applications.resume', $app) : null),
                    'applied_at' => $app->created_at->locale('id')->translatedFormat('d F Y'),
                ];
            });

            $profile = [
                'type' => 'jobseeker',
                'name' => $user->name,
                'username' => $user->username ? '@'.$user->username : null,
                'email' => $user->email,
                'phone' => $user->phone ?: '-',
                'avatar_url' => $user->avatar_url,
                'initials' => strtoupper(substr($user->name, 0, 2)),
                'member_since' => $user->created_at->locale('id')->translatedFormat('F Y'),
                'applications' => $applicantApplications,
            ];
        } elseif ($user->role === 'employer') {
            $openJobs = Job::query()
                ->where('employer_id', $user->id)
                ->where('status', 'open')
                ->latest('id')
                ->get()
                ->map(fn ($j) => [
                    'id' => $j->id,
                    'title' => $j->title,
                    'location' => $j->location,
                    'salary_formatted' => $j->salary_amount ? 'Rp '.number_format($j->salary_amount, 0, ',', '.').($j->salary_type === 'monthly' ? '/bln' : '/hari') : 'Kompetitif',
                    'work_hours' => $j->work_hours_per_day ? $j->work_hours_per_day.' jam/hari' : null,
                    'url' => route('jobs.show', $j),
                ]);

            $myApplicationsWithEmployer = [];
            if ($request->user()->hasRole('jobseeker')) {
                $myApplicationsWithEmployer = JobApplication::query()
                    ->where('user_id', $userId)
                    ->whereHas('job', fn ($q) => $q->where('employer_id', $user->id))
                    ->with('job:id,title')
                    ->latest('id')
                    ->get()
                    ->map(fn ($app) => [
                        'id' => $app->id,
                        'job_title' => $app->job?->title ?? 'Posisi',
                        'status' => $app->status,
                        'status_label' => match ($app->status) {
                            'pending' => 'Menunggu Tinjauan',
                            'interview' => 'Wawancara',
                            'accepted' => 'Diterima',
                            'rejected' => 'Belum Lolos',
                            'resigned' => 'Resign',
                            default => ucfirst($app->status),
                        },
                        'applied_at' => $app->created_at->locale('id')->translatedFormat('d M Y'),
                    ]);
            }

            $profile = [
                'type' => 'employer',
                'name' => $user->business_name ?: $user->name,
                'business_name' => $user->business_name,
                'owner_name' => $user->name,
                'username' => $user->username ? '@'.$user->username : null,
                'email' => $user->email,
                'phone' => $user->phone ?: '-',
                'avatar_url' => $user->avatar_url,
                'initials' => strtoupper(substr($user->business_name ?: $user->name, 0, 2)),
                'member_since' => $user->created_at->locale('id')->translatedFormat('F Y'),
                'open_jobs' => $openJobs,
                'my_applications' => $myApplicationsWithEmployer,
            ];
        } else {
            $profile = [
                'type' => 'admin',
                'name' => $user->name,
                'username' => $user->username ? '@'.$user->username : null,
                'email' => $user->email,
                'phone' => $user->phone ?: '-',
                'avatar_url' => $user->avatar_url,
                'initials' => strtoupper(substr($user->name, 0, 2)),
                'member_since' => $user->created_at->locale('id')->translatedFormat('F Y'),
                'role_label' => 'Super Administrator Platform',
            ];
        }

        $directUrl = null;
        if ($request->user()->hasRole('employer')) {
            $directUrl = route('employer.applications.index', ['user' => $user->id]);
        } elseif ($request->user()->hasRole('admin')) {
            $directUrl = route('admin.users.show', $user);
        } elseif ($request->user()->hasRole('jobseeker')) {
            if ($user->role === 'employer') {
                $hasApp = JobApplication::where('user_id', $userId)
                    ->whereHas('job', fn ($q) => $q->where('employer_id', $user->id))
                    ->exists();

                if ($hasApp) {
                    $directUrl = route('applications.index');
                } else {
                    $firstJob = Job::where('employer_id', $user->id)->where('status', 'open')->first();
                    $directUrl = $firstJob ? route('jobs.show', $firstJob) : route('jobs.index');
                }
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
                'avatar_url' => $user->avatar_url,
                'direct_url' => $directUrl,
            ],
            'messages' => $messages,
            'pending_resignation' => $pendingResignation,
            'interview_invitation' => $interviewInvitation,
            'profile' => $profile,
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

        if ($message->sender_id !== $userId) {
            return response()->json(['error' => 'Anda hanya dapat menghapus pesan yang Anda kirim.'], 403);
        }

        $message->update([
            'is_deleted' => true,
            'message' => 'Pesan ini telah dihapus',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dihapus.',
            'chat_message' => [
                'id' => $message->id,
                'is_deleted' => true,
                'message' => 'Pesan ini telah dihapus',
            ],
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
