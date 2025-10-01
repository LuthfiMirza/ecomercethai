<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $messages = Message::with('user')
            ->orderBy('created_at', 'asc')
            ->take(100)
            ->get();

        return view('admin.chat.index', compact('messages'));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'user_id' => $request->user()->id,
            'content' => $data['content'],
        ]);

        broadcast(new MessageSent(
            $request->user(),
            $message->content,
            $message->created_at->toIso8601String()
        ))->toOthers();

        return response()->json([
            'ok' => true,
            'message' => [
                'user' => [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                ],
                'content' => $message->content,
                'created_at' => $message->created_at->toIso8601String(),
            ],
        ]);
    }
}
