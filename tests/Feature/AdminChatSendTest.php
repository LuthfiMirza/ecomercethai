<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminChatSendTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_reply_to_customer_chat(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        Message::create([
            'user_id' => $customer->id,
            'conversation_id' => $customer->id,
            'content' => 'Halo admin',
            'is_from_admin' => false,
        ]);

        $payload = [
            'conversation_id' => $customer->id,
            'content' => 'Hi, how can I help?',
        ];

        $response = $this->actingAs($admin)
            ->post(localized_route('admin.chat.send'), $payload);

        $response->assertOk()
            ->assertJson([
                'ok' => true,
                'message' => [
                    'content' => $payload['content'],
                    'is_from_admin' => true,
                    'conversation_id' => $customer->id,
                ],
            ]);

        $this->assertDatabaseHas('messages', [
            'user_id' => $admin->id,
            'conversation_id' => $customer->id,
            'content' => $payload['content'],
            'is_from_admin' => true,
        ]);
    }
}
