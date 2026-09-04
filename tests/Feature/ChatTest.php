<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_chat(): void
    {
        $response = $this->get('/chat');
        $response->assertRedirect(route('login'));

        $apiResponse = $this->getJson('/chat/conversations');
        $apiResponse->assertStatus(401);
    }

    public function test_conversations_empty_when_no_interactions(): void
    {
        $user = User::factory()->create(['role' => 'jobseeker']);

        $response = $this->actingAs($user)->getJson('/chat/conversations');

        $response->assertOk();
        $response->assertJsonCount(0);
    }

    public function test_users_can_send_and_receive_messages(): void
    {
        $sender = User::factory()->create(['name' => 'Alice', 'role' => 'jobseeker']);
        $receiver = User::factory()->create(['name' => 'Bob UMKM', 'role' => 'employer']);

        $sendResponse = $this->actingAs($sender)->postJson("/chat/messages/{$receiver->id}", [
            'message' => 'Halo apakah lowongan masih buka?',
        ]);

        $sendResponse->assertStatus(201);
        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Halo apakah lowongan masih buka?',
            'is_read' => false,
        ]);

        $conversationsResponse = $this->actingAs($receiver)->getJson('/chat/conversations');
        $conversationsResponse->assertOk();
        $conversationsResponse->assertJsonFragment([
            'id' => $sender->id,
            'name' => 'Alice',
            'unread_count' => 1,
        ]);

        $fetchResponse = $this->actingAs($receiver)->getJson("/chat/messages/{$sender->id}");
        $fetchResponse->assertOk();
        $fetchResponse->assertJsonPath('messages.0.message', 'Halo apakah lowongan masih buka?');

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'is_read' => true,
        ]);
    }

    public function test_chat_page_and_floating_chat_do_not_contain_bebas_calo(): void
    {
        $user = User::factory()->create(['role' => 'jobseeker']);

        $response = $this->actingAs($user)->get('/chat');
        $response->assertOk();
        $response->assertDontSee('100% Bebas Calo');
        $response->assertDontSee('100% Saluran Resmi Etis');

        $homeResponse = $this->actingAs($user)->followingRedirects()->get('/');
        $homeResponse->assertOk();
        $homeResponse->assertDontSee('100% Bebas Calo');
    }

    public function test_user_can_only_delete_their_own_sent_message(): void
    {
        $userA = User::factory()->create(['role' => 'jobseeker']);
        $userB = User::factory()->create(['role' => 'employer']);

        // Send message from userB to userA
        $message = ChatMessage::create([
            'sender_id' => $userB->id,
            'receiver_id' => $userA->id,
            'message' => 'Pesan pengujian dari user B ke user A',
            'is_read' => false,
        ]);

        // User A (receiver) cannot delete message sent by User B
        $forbiddenResponse = $this->actingAs($userA)->deleteJson("/chat/messages/{$message->id}");
        $forbiddenResponse->assertForbidden();

        // User B (sender) can delete their own message
        $response = $this->actingAs($userB)->deleteJson("/chat/messages/{$message->id}");
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'chat_message' => [
                'id' => $message->id,
                'is_deleted' => true,
                'message' => 'Pesan ini telah dihapus',
            ],
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'id' => $message->id,
            'is_deleted' => true,
            'message' => 'Pesan ini telah dihapus',
        ]);
    }

    public function test_user_can_clear_chat_history_with_another_user(): void
    {
        $userA = User::factory()->create(['role' => 'jobseeker']);
        $userB = User::factory()->create(['role' => 'employer']);

        ChatMessage::create([
            'sender_id' => $userA->id,
            'receiver_id' => $userB->id,
            'message' => 'Pesan 1',
        ]);
        ChatMessage::create([
            'sender_id' => $userB->id,
            'receiver_id' => $userA->id,
            'message' => 'Pesan 2',
        ]);

        $this->assertCount(2, ChatMessage::all());

        // User B clears chat with User A
        $response = $this->actingAs($userB)->deleteJson("/chat/clear/{$userA->id}");
        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertCount(0, ChatMessage::all());
    }

    public function test_user_cannot_delete_unrelated_chat_message(): void
    {
        $userA = User::factory()->create(['role' => 'jobseeker']);
        $userB = User::factory()->create(['role' => 'employer']);
        $intruder = User::factory()->create(['role' => 'jobseeker']);

        $message = ChatMessage::create([
            'sender_id' => $userA->id,
            'receiver_id' => $userB->id,
            'message' => 'Pesan rahasia',
        ]);

        $response = $this->actingAs($intruder)->deleteJson("/chat/messages/{$message->id}");
        $response->assertStatus(403);

        $this->assertDatabaseHas('chat_messages', [
            'id' => $message->id,
        ]);
    }

    public function test_user_can_reply_to_chat_message(): void
    {
        $userA = User::factory()->create(['name' => 'Alice', 'role' => 'jobseeker']);
        $userB = User::factory()->create(['name' => 'Bob UMKM', 'role' => 'employer']);

        $originalMsg = ChatMessage::create([
            'sender_id' => $userB->id,
            'receiver_id' => $userA->id,
            'message' => 'Halo Alice, apakah Anda bisa wawancara besok?',
        ]);

        $replyResponse = $this->actingAs($userA)->postJson("/chat/messages/{$userB->id}", [
            'message' => 'Bisa pak, saya siap jam 10 pagi.',
            'reply_to_id' => $originalMsg->id,
        ]);

        $replyResponse->assertStatus(201);
        $replyResponse->assertJsonPath('reply_to.id', $originalMsg->id);
        $replyResponse->assertJsonPath('reply_to.sender_name', 'Bob UMKM');

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $userA->id,
            'receiver_id' => $userB->id,
            'reply_to_id' => $originalMsg->id,
            'message' => 'Bisa pak, saya siap jam 10 pagi.',
        ]);

        // When fetching messages, reply_to information is included
        $fetchResponse = $this->actingAs($userB)->getJson("/chat/messages/{$userA->id}");
        $fetchResponse->assertOk();
        $fetchResponse->assertJsonPath('messages.1.reply_to.id', $originalMsg->id);
    }

    public function test_user_cannot_reply_to_message_from_another_conversation(): void
    {
        $sender = User::factory()->jobseeker()->create();
        $receiver = User::factory()->employer()->create();
        $unrelatedUser = User::factory()->employer()->create();
        $unrelatedMessage = ChatMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $unrelatedUser->id,
            'message' => 'Pesan dari percakapan lain yang bersifat privat.',
        ]);

        $response = $this->actingAs($sender)->postJson(route('chat.send', $receiver), [
            'message' => 'Mencoba membalas pesan yang tidak terkait.',
            'reply_to_id' => $unrelatedMessage->id,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('reply_to_id');
        $this->assertDatabaseMissing('chat_messages', [
            'receiver_id' => $receiver->id,
            'message' => 'Mencoba membalas pesan yang tidak terkait.',
        ]);
    }

    public function test_chat_views_do_not_embed_user_content_in_inline_event_handlers(): void
    {
        $user = User::factory()->jobseeker()->create();

        $response = $this->actingAs($user)->get(route('chat.index'));

        $response->assertOk()
            ->assertDontSee('onclick="selectConversation', false)
            ->assertDontSee('onclick="startReply', false)
            ->assertDontSee('onclick="openPopupChatDetail', false)
            ->assertDontSee('onclick="startPopupReply', false);
    }

    public function test_user_can_remove_conversation_person(): void
    {
        $userA = User::factory()->create(['role' => 'jobseeker']);
        $userB = User::factory()->create(['role' => 'employer']);

        ChatMessage::create([
            'sender_id' => $userA->id,
            'receiver_id' => $userB->id,
            'message' => 'Halo ini pesan dari A',
        ]);

        $this->assertDatabaseCount('chat_messages', 1);

        $response = $this->actingAs($userA)->deleteJson("/chat/conversations/{$userB->id}");
        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseCount('chat_messages', 0);
    }

    public function test_deleted_messages_appear_as_deleted_placeholder_in_messages_and_conversations(): void
    {
        $userA = User::factory()->create(['role' => 'jobseeker']);
        $userB = User::factory()->create(['role' => 'employer']);

        $message = ChatMessage::create([
            'sender_id' => $userA->id,
            'receiver_id' => $userB->id,
            'message' => 'Pesan rahasia',
            'is_deleted' => true,
        ]);

        $reply = ChatMessage::create([
            'sender_id' => $userB->id,
            'receiver_id' => $userA->id,
            'reply_to_id' => $message->id,
            'message' => 'Balasan pesan',
            'is_deleted' => false,
        ]);

        $messagesResponse = $this->actingAs($userA)->getJson("/chat/messages/{$userB->id}");
        $messagesResponse->assertOk();
        $messagesResponse->assertJsonPath('messages.0.is_deleted', true);
        $messagesResponse->assertJsonPath('messages.0.message', 'Pesan ini telah dihapus');
        $messagesResponse->assertJsonPath('messages.1.reply_to.message', 'Pesan ini telah dihapus');

        $conversationsResponse = $this->actingAs($userA)->getJson('/chat/conversations');
        $conversationsResponse->assertOk();
        $conversationsResponse->assertJsonPath('0.last_message', 'Balasan pesan');
    }

    public function test_chat_messages_endpoint_returns_interview_invitation_and_profile_for_applicant_and_employer(): void
    {
        $employer = User::factory()->employer()->create([
            'name' => 'Budi Santoso',
            'business_name' => 'Toko Buku Aksara',
            'email' => 'toko@aksara.test',
        ]);
        $jobseeker = User::factory()->jobseeker()->create([
            'name' => 'Sari Indah',
            'email' => 'sari@test.com',
            'phone' => '08123456789',
        ]);
        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Staf Penjualan Toko',
            'status' => 'open',
        ]);

        $application = JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $jobseeker->id,
            'status' => 'interview',
            'interview_date' => now()->addDays(2)->toDateString(),
            'interview_time' => '10:00',
            'interview_type' => 'Tatap Muka Langsung',
            'interview_location' => 'Jl. Braga No. 20, Bandung',
            'interview_notes' => 'Harap membawa portofolio.',
            'interview_status' => 'pending',
        ]);

        // 1. Employer views chat with jobseeker
        $employerResp = $this->actingAs($employer)->getJson(route('chat.messages', $jobseeker));
        $employerResp->assertOk();
        $employerResp->assertJsonPath('interview_invitation.can_respond', false);
        $employerResp->assertJsonPath('interview_invitation.job_title', 'Staf Penjualan Toko');
        $employerResp->assertJsonPath('interview_invitation.interview_status', 'pending');
        $employerResp->assertJsonPath('profile.type', 'jobseeker');
        $employerResp->assertJsonPath('profile.name', 'Sari Indah');
        $employerResp->assertJsonPath('profile.phone', '08123456789');
        $employerResp->assertJsonPath('profile.applications.0.job_title', 'Staf Penjualan Toko');

        // 2. Jobseeker views chat with employer
        $jobseekerResp = $this->actingAs($jobseeker)->getJson(route('chat.messages', $employer));
        $jobseekerResp->assertOk();
        $jobseekerResp->assertJsonPath('interview_invitation.can_respond', true);
        $jobseekerResp->assertJsonPath('interview_invitation.job_title', 'Staf Penjualan Toko');
        $jobseekerResp->assertJsonPath('profile.type', 'employer');
        $jobseekerResp->assertJsonPath('profile.business_name', 'Toko Buku Aksara');
        $jobseekerResp->assertJsonPath('profile.owner_name', 'Budi Santoso');
        $this->assertNotEmpty($jobseekerResp->json('profile.open_jobs'));
    }

    public function test_jobseeker_can_open_chat_page_with_specific_employer_from_job(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'business_name' => 'Kedai Kopi Bahagia',
            'name' => 'Budi Raharjo',
        ]);
        $jobseeker = User::factory()->create(['role' => 'jobseeker']);

        $response = $this->actingAs($jobseeker)->get(route('chat.index', ['user' => $employer->id]));

        $response->assertOk();
        $response->assertSee('chatActionMenuDropdown');
        $response->assertSee((string) $employer->id);
    }

    public function test_conversations_api_includes_employer_when_requested_with_param(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'business_name' => 'Kedai Kopi Bahagia',
            'name' => 'Budi Raharjo',
        ]);
        $jobseeker = User::factory()->create(['role' => 'jobseeker']);

        // Without prior messages or applications, passing ?with={employer->id} brings employer into conversation list
        $response = $this->actingAs($jobseeker)->getJson('/chat/conversations?with='.$employer->id);

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $employer->id,
            'business_name' => 'Kedai Kopi Bahagia',
        ]);
    }

    public function test_admin_can_view_conversations_and_send_messages_to_user(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Sari Pelamar']);

        // 1. Initially, admin has empty conversations list because no messages have been sent yet
        $convResponse = $this->actingAs($admin)->getJson('/chat/conversations');
        $convResponse->assertOk();
        $convResponse->assertJsonCount(0);

        // When opened with ?with={jobseeker->id}, Sari Pelamar is included
        $withResponse = $this->actingAs($admin)->getJson('/chat/conversations?with='.$jobseeker->id);
        $withResponse->assertOk();
        $withResponse->assertJsonFragment([
            'id' => $jobseeker->id,
            'name' => 'Sari Pelamar',
        ]);

        // 2. Admin sends a direct message to user
        $sendResponse = $this->actingAs($admin)->postJson(route('chat.send', $jobseeker), [
            'message' => 'Halo Sari, kami ingin mengonfirmasi kelengkapan dokumen akun Anda.',
        ]);
        $sendResponse->assertStatus(201);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $admin->id,
            'receiver_id' => $jobseeker->id,
            'message' => 'Halo Sari, kami ingin mengonfirmasi kelengkapan dokumen akun Anda.',
            'is_read' => false,
        ]);

        // Now conversations list includes Sari Pelamar because a message exists
        $convAfterMsg = $this->actingAs($admin)->getJson('/chat/conversations');
        $convAfterMsg->assertOk();
        $convAfterMsg->assertJsonFragment([
            'id' => $jobseeker->id,
            'name' => 'Sari Pelamar',
        ]);

        // 3. User can fetch messages from admin
        $fetchResponse = $this->actingAs($jobseeker)->getJson(route('chat.messages', $admin));
        $fetchResponse->assertOk();
        $fetchResponse->assertJsonPath('messages.0.message', 'Halo Sari, kami ingin mengonfirmasi kelengkapan dokumen akun Anda.');

        // 4. User can reply to admin
        $replyResponse = $this->actingAs($jobseeker)->postJson(route('chat.send', $admin), [
            'message' => 'Terima kasih Admin, dokumen saya sudah saya perbarui.',
        ]);
        $replyResponse->assertStatus(201);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $jobseeker->id,
            'receiver_id' => $admin->id,
            'message' => 'Terima kasih Admin, dokumen saya sudah saya perbarui.',
        ]);
    }

    public function test_admin_user_detail_profile_contains_chat_button_and_index_does_not(): void
    {
        $admin = User::factory()->admin()->create();
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Dewi Lestari']);

        // 1. Index page does NOT include chat link for the user row
        $indexResponse = $this->actingAs($admin)->get(route('admin.users.index'));
        $indexResponse->assertOk();
        $indexResponse->assertDontSee(route('chat.index', ['user' => $jobseeker->id]));

        // 2. Show user detail profile page includes the chat action button with text "Kirim Pesan"
        $showResponse = $this->actingAs($admin)->get(route('admin.users.show', $jobseeker));
        $showResponse->assertOk();
        $showResponse->assertSee(route('chat.index', ['user' => $jobseeker->id]));
        $showResponse->assertSee('Kirim Pesan');
        $showResponse->assertDontSee('Kirim Pesan Langsung');
    }

    public function test_chat_messages_endpoint_provides_direct_url_to_applicant_selection_status_or_profile(): void
    {
        $employer = User::factory()->employer()->create();
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Budi Santoso']);
        $admin = User::factory()->admin()->create();

        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'status' => 'open',
        ]);

        JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $jobseeker->id,
            'status' => 'pending',
        ]);

        // 1. Employer chatting with applicant -> direct_url points to employer.applications.index?user={jobseeker_id}
        $employerResponse = $this->actingAs($employer)->getJson(route('chat.messages', $jobseeker));
        $employerResponse->assertOk();
        $employerResponse->assertJsonPath('user.direct_url', route('employer.applications.index', ['user' => $jobseeker->id]));

        // 2. Admin chatting with user -> direct_url points to admin.users.show
        $adminResponse = $this->actingAs($admin)->getJson(route('chat.messages', $jobseeker));
        $adminResponse->assertOk();
        $adminResponse->assertJsonPath('user.direct_url', route('admin.users.show', $jobseeker));

        // 3. Jobseeker chatting with employer who has received jobseeker's application -> direct_url points to applications.index
        $jobseekerResponse = $this->actingAs($jobseeker)->getJson(route('chat.messages', $employer));
        $jobseekerResponse->assertOk();
        $jobseekerResponse->assertJsonPath('user.direct_url', route('applications.index'));
    }

    public function test_employer_applications_index_can_filter_by_specific_user_and_displays_status_banner(): void
    {
        $employer = User::factory()->employer()->create();
        $candidateA = User::factory()->jobseeker()->create(['name' => 'Kandidat Pertama']);
        $candidateB = User::factory()->jobseeker()->create(['name' => 'Kandidat Kedua']);

        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Barista Kafe',
            'status' => 'open',
        ]);

        JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $candidateA->id,
            'status' => 'interview',
        ]);

        JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $candidateB->id,
            'status' => 'pending',
        ]);

        // Filter by candidateA
        $response = $this->actingAs($employer)->get(route('employer.applications.index', ['user' => $candidateA->id]));
        $response->assertOk();
        $response->assertSee('Status Seleksi Pelamar: Kandidat Pertama');
        $response->assertSee('Kandidat Pertama');
        $response->assertDontSee('Kandidat Kedua');
    }

    public function test_chat_views_do_not_contain_separate_profile_buttons_or_modals(): void
    {
        $user = User::factory()->jobseeker()->create();

        $response = $this->actingAs($user)->get(route('chat.index'));
        $response->assertOk();
        $response->assertDontSee('id="chatViewProfileBtn"', false);
        $response->assertDontSee('id="chatMenuProfileOption"', false);
        $response->assertDontSee('id="chatProfileModal"', false);
        $response->assertDontSee('id="popupProfileHeaderBtn"', false);
        $response->assertDontSee('id="popupProfileModal"', false);
        $response->assertSee('id="activeChatUserDirectContainer"', false);
    }
}
