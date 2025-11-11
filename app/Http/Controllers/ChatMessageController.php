<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $afterId = max((int) $request->query('after'), 0);

        $baseQuery = Message::with('user:id,name')
            ->where(function ($query) use ($user) {
                $query->where('conversation_id', $user->id)
                    ->orWhere(function ($inner) use ($user) {
                        $inner->whereNull('conversation_id')
                            ->where('user_id', $user->id);
                    });
            });

        $latestId = (clone $baseQuery)->max('id');

        $messages = (clone $baseQuery)
            ->when($afterId > 0, fn ($query) => $query->where('id', '>', $afterId))
            ->orderBy('created_at')
            ->when($afterId <= 0, fn ($query) => $query->take(200))
            ->get()
            ->map(fn (Message $message) => $this->transformMessage($message))
            ->values();

        return response()->json([
            'ok' => true,
            'messages' => $messages,
            'latest_id' => $latestId ? (int) $latestId : null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        $message = Message::create([
            'user_id' => $user->id,
            'conversation_id' => $user->id,
            'content' => $data['content'],
            'is_from_admin' => false,
        ]);

        $message->loadMissing('user:id,name', 'conversation:id,name');

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'ok' => true,
            'message' => $this->transformMessage($message),
            'latest_id' => $message->id,
        ], 201);
    }

    protected function transformMessage(Message $message): array
    {
        return [
            'id' => $message->id,
            'content' => $message->content,
            'is_from_admin' => $message->is_from_admin,
            'conversation_id' => $message->conversation_id ?: $message->user_id,
            'created_at' => $message->created_at?->toIso8601String(),
            'sender' => [
                'id' => $message->user?->id,
                'name' => $message->user?->name,
            ],
        ];
    }
}
