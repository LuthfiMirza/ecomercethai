<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $initialConversations = $this->conversationItems();
        $initialConversation = null;
        $initialMessages = collect();
        $initialConversationLatestId = null;

        $availableConversationIds = $initialConversations
            ->pluck('user.id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();

        $requestedConversationId = (int) $request->query('conversation');

        $initialUserId = null;
        if ($requestedConversationId && $availableConversationIds->contains($requestedConversationId)) {
            $initialUserId = $requestedConversationId;
        } elseif ($availableConversationIds->isNotEmpty()) {
            $initialUserId = $availableConversationIds->first();
        }

        if ($initialUserId) {
            $initialConversation = User::select('id', 'name')->find($initialUserId);
            if ($initialConversation) {
                $payload = $this->buildMessagesPayload($initialConversation);
                $initialMessages = $payload['messages'];
                $initialConversationLatestId = $payload['latest_id'];
            }
        }

        $customerChoices = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->take(200)
            ->get();

        return view('admin.chat.index', [
            'initialConversation' => $initialConversation,
            'initialConversations' => $initialConversations,
            'initialMessages' => $initialMessages,
            'initialConversationLatestId' => $initialConversationLatestId,
            'customerChoices' => $customerChoices,
        ]);
    }

    public function conversations(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'conversations' => $this->conversationItems(),
        ]);
    }

    public function messages(Request $request, User $user): JsonResponse
    {
        $afterId = max((int) $request->query('after'), 0);
        $payload = $this->buildMessagesPayload($user, $afterId);

        return response()->json([
            'ok' => true,
            'conversation' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'messages' => $payload['messages'],
            'latest_id' => $payload['latest_id'],
        ]);
    }

    public function send(Request $request)
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

        $payload = [
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
            'latest_id' => $message->id,
        ];

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return back()->with('success', __('Message sent.'));
    }

    protected function conversationItems(): Collection
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

        return $conversationMeta->map(function ($meta) use ($users, $lastMessages) {
            $conversationId = (int) $meta->conversation_key;
            $conversationUser = $users->get($conversationId);
            $last = $lastMessages->get($conversationId);
            $isUnread = $last ? ! $last->is_from_admin : false;

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
                'last_message_id' => $last?->id,
                'unread' => $isUnread,
            ];
        })->values();
    }

    protected function buildMessagesPayload(User $user, int $afterId = 0): array
    {
        $baseQuery = Message::query()
            ->with('user:id,name')
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
            ->when($afterId <= 0, fn ($query) => $query->take(300))
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

        return [
            'messages' => $messages,
            'latest_id' => $latestId ? (int) $latestId : null,
        ];
    }
}
