<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
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

    public function test_user_can_delete_message_received_or_sent(): void
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

        // User A (receiver) can delete message from other user
        $response = $this->actingAs($userA)->deleteJson("/chat/messages/{$message->id}");
        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('chat_messages', [
            'id' => $message->id,
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
}
