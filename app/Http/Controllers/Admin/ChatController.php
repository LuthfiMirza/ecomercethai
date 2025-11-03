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
            ->where(function ($query) {
                $query->whereNotNull('conversation_id')
                    ->orWhere('is_from_admin', false);
            })
            ->selectRaw('COALESCE(conversation_id, user_id) as conversation_key')
            ->orderByDesc('created_at')
            ->value('conversation_key');

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
            ->where(function ($query) {
                $query->whereNotNull('conversation_id')
                    ->orWhere('is_from_admin', false);
            })
            ->selectRaw('COALESCE(conversation_id, user_id) as conversation_key')
            ->selectRaw('MAX(created_at) as last_message_at')
            ->groupBy('conversation_key')
            ->orderByDesc('last_message_at')
            ->get();

        $conversationIds = $conversationMeta
            ->pluck('conversation_key')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();

        $users = User::query()
            ->select('id', 'name')
            ->whereIn('id', $conversationIds)
            ->get()
            ->keyBy('id');

        $lastMessages = Message::query()
            ->with('user:id,name')
            ->where(function ($query) {
                $query->whereNotNull('conversation_id')
                    ->orWhere('is_from_admin', false);
            })
            ->where(function ($query) use ($conversationIds) {
                $query->whereIn('conversation_id', $conversationIds)
                    ->orWhere(function ($inner) use ($conversationIds) {
                        $inner->whereNull('conversation_id')
                            ->whereIn('user_id', $conversationIds);
                    });
            })
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function (Message $message) {
                return $message->conversation_id ?: $message->user_id;
            })
            ->map->first();

        $items = $conversationMeta->map(function ($meta) use ($users, $lastMessages) {
            $conversationId = (int) $meta->conversation_key;
            $conversationUser = $users->get($conversationId);
            $last = $lastMessages->get($conversationId);

            return [
                'user' => $conversationUser ? [
                    'id' => $conversationUser->id,
                    'name' => $conversationUser->name,
                ] : [
                    'id' => $conversationId,
                    'name' => 'Unknown User',
                ],
                'last_message' => $last ? [
                    'id' => $last->id,
                    'content' => $last->content,
                    'is_from_admin' => $last->is_from_admin,
                    'conversation_id' => $last->conversation_id ?: $conversationId,
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
            'conversations' => $items->values(),
        ]);
    }

    public function messages(User $user): JsonResponse
    {
        $messages = Message::query()
            ->with('user:id,name')
            ->where(function ($query) use ($user) {
                $query->where('conversation_id', $user->id)
                    ->orWhere(function ($inner) use ($user) {
                        $inner->whereNull('conversation_id')
                            ->where('user_id', $user->id);
                    });
            })
            ->orderBy('created_at')
            ->take(300)
            ->get()
            ->map(fn (Message $message) => [
                'id' => $message->id,
                'content' => $message->content,
                'is_from_admin' => $message->is_from_admin,
                'conversation_id' => $message->conversation_id ?: $user->id,
                'created_at' => $message->created_at?->toIso8601String(),
                'sender' => [
                    'id' => $message->user?->id,
                    'name' => $message->user?->name,
                ],
            ])
            ->values();

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
                'conversation_id' => $message->conversation_id ?: $conversationUser->id,
                'created_at' => $message->created_at?->toIso8601String(),
                'sender' => [
                    'id' => $message->user?->id,
                    'name' => $message->user?->name,
                ],
            ],
        ]);
    }
}
