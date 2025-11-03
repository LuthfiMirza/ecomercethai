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
        $messages = Message::with('user:id,name')
            ->where(function ($query) use ($request) {
                $query->where('conversation_id', $request->user()->id)
                    ->orWhere(function ($inner) use ($request) {
                        $inner->whereNull('conversation_id')
                            ->where('user_id', $request->user()->id);
                    });
            })
            ->orderBy('created_at')
            ->take(200)
            ->get()
            ->map(fn (Message $message) => $this->transformMessage($message))
            ->values();

        return response()->json([
            'ok' => true,
            'messages' => $messages,
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
