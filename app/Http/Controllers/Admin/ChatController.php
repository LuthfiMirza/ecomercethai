<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $latestConversationId = Message::query()
            ->orderByDesc('created_at')
            ->value('conversation_id');

        $initialConversation = null;

        if ($latestConversationId) {
            $initialConversation = User::select('id', 'name')
                ->find($latestConversationId);
        }

        return view('admin.chat.index', [
            'initialConversation' => $initialConversation,
        ]);
    }

    public function conversations(): JsonResponse
    {
        $conversationMeta = Message::query()
            ->select('conversation_id')
            ->selectRaw('MAX(created_at) as last_message_at')
            ->groupBy('conversation_id')
            ->orderByDesc('last_message_at')
            ->get();

        $conversationIds = $conversationMeta->pluck('conversation_id')->filter()->values();

        $users = User::query()
            ->select('id', 'name')
            ->whereIn('id', $conversationIds)
            ->get()
            ->keyBy('id');

        $lastMessages = Message::query()
            ->with('user:id,name')
            ->whereIn('conversation_id', $conversationIds)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('conversation_id')
            ->map->first();

        $items = $conversationMeta->map(function ($meta) use ($users, $lastMessages) {
            $conversationUser = $users->get($meta->conversation_id);
            $last = $lastMessages->get($meta->conversation_id);

            return [
                'user' => $conversationUser ? [
                    'id' => $conversationUser->id,
                    'name' => $conversationUser->name,
                ] : [
                    'id' => $meta->conversation_id,
                    'name' => 'Unknown User',
                ],
                'last_message' => $last ? [
                    'id' => $last->id,
                    'content' => $last->content,
                    'is_from_admin' => $last->is_from_admin,
                    'created_at' => $last->created_at?->toIso8601String(),
                    'sender' => [
                        'id' => $last->user?->id,
                        'name' => $last->user?->name,
                    ],
                ] : null,
                'last_message_at' => $last ? $last->created_at?->toIso8601String() : null,
            ];
        });

        return response()->json([
            'ok' => true,
            'conversations' => $items,
        ]);
    }

    public function messages(User $user): JsonResponse
    {
        $messages = Message::query()
            ->with('user:id,name')
            ->where('conversation_id', $user->id)
            ->orderBy('created_at')
            ->take(300)
            ->get()
            ->map(fn (Message $message) => [
                'id' => $message->id,
                'content' => $message->content,
                'is_from_admin' => $message->is_from_admin,
                'conversation_id' => $message->conversation_id,
                'created_at' => $message->created_at?->toIso8601String(),
                'sender' => [
                    'id' => $message->user?->id,
                    'name' => $message->user?->name,
                ],
            ]);

        return response()->json([
            'ok' => true,
            'conversation' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'messages' => $messages,
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'conversation_id' => ['required', 'integer', 'exists:users,id'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $conversationUser = User::select('id', 'name')->findOrFail($data['conversation_id']);

        $message = Message::create([
            'user_id' => $request->user()->id,
            'conversation_id' => $conversationUser->id,
            'content' => $data['content'],
            'is_from_admin' => true,
        ]);

        $message->loadMissing('user:id,name', 'conversation:id,name');

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'ok' => true,
            'message' => [
                'id' => $message->id,
                'content' => $message->content,
                'is_from_admin' => $message->is_from_admin,
                'conversation_id' => $message->conversation_id,
                'created_at' => $message->created_at?->toIso8601String(),
                'sender' => [
                    'id' => $message->user?->id,
                    'name' => $message->user?->name,
                ],
            ],
        ]);
    }
}
