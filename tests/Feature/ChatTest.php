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
}
