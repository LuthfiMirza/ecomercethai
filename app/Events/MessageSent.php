<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
        $this->message->loadMissing(['user:id,name', 'conversation:id,name']);
    }

    public function broadcastOn(): array
    {
        $channels = [
            new PresenceChannel('chat.admin'),
        ];

        if ($this->message->conversation_id) {
            $channels[] = new PrivateChannel('chat.user.'.$this->message->conversation_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $conversationId = $this->message->conversation_id ?: $this->message->user_id;
        $conversationUser = $this->message->conversation;

        if (! $conversationUser && $conversationId === $this->message->user_id && ! $this->message->is_from_admin) {
            $conversationUser = $this->message->user;
        }

        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'is_from_admin' => $this->message->is_from_admin,
            'conversation_id' => $conversationId,
            'created_at' => $this->message->created_at?->toIso8601String(),
            'user' => [
                'id' => $this->message->user?->id,
                'name' => $this->message->user?->name,
            ],
            'conversation_user' => [
                'id' => $conversationUser?->id ?? $conversationId,
                'name' => $conversationUser?->name ?? $this->message->user?->name,
            ],
        ];
    }
}
