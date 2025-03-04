<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function index()
    {
        return Inertia::render('Messages/Index');
    }

    public function getConversations(Request $request)
    {
        $userId = $request->user()->id;

        $conversations = Message::select('sender_id', 'recipient_id')
            ->where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($message) use ($userId) {
                $otherUserId = $message->sender_id === $userId ? $message->recipient_id : $message->sender_id;
                return $otherUserId;
            })
            ->unique()
            ->values();

        $users = User::whereIn('id', $conversations)->get()->map(function ($user) use ($userId) {
            $unreadCount = Message::where('sender_id', $user->id)
                ->where('recipient_id', $userId)
                ->where('is_read', false)
                ->count();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'profile_photo' => $user->profile_photo,
                'unread_count' => $unreadCount
            ];
        });

        return response()->json(['conversations' => $users]);
    }

    public function getMessages(Request $request, User $otherUser)
    {
        $userId = $request->user()->id;

        $messages = Message::with(['sender:id,name,profile_photo'])
            ->betweenUsers($userId, $otherUser->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'sender_id' => $message->sender_id,
                    'sender' => $message->sender,
                    'created_at' => $message->created_at,
                    'is_read' => $message->is_read
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    public function sendMessage(Request $request, User $recipient)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'recipient_id' => $recipient->id,
            'content' => $request->content
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['message' => $message->load('sender:id,name,profile_photo')]);
    }

    public function markAsRead(Request $request, Message $message)
    {
        if ($request->user()->id === $message->recipient_id) {
            $message->markAsRead();
        }

        return response()->json(['success' => true]);
    }
}
