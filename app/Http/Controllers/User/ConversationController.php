<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use Auth;

class ConversationController extends Controller
{
    public function adminChat()
    {
        $adminId = 9;
        $userId = Auth::id();

        $conversation = Conversation::where(function ($q) use ($adminId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $adminId);
        })->orWhere(function ($q) use ($adminId, $userId) {
            $q->where('sender_id', $adminId)->where('receiver_id', $userId);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'sender_id' => $userId,
                'receiver_id' => $adminId,
                'title' => 'Support with Admin',
                'sender_viewed' => 1,
                'receiver_viewed' => 0,
            ]);
        }

        if ($conversation->sender_id == $userId) {
            $conversation->sender_viewed = 1;
        } elseif ($conversation->receiver_id == $userId) {
            $conversation->receiver_viewed = 1;
        }
        $conversation->save();

        return view('frontend.conversations.admin_show', compact('conversation'));
    }

    public function message_store(Request $request)
    {
        $message = new Message;
        $message->conversation_id = $request->conversation_id;
        $message->user_id = Auth::id();
        $message->message = $request->message;
        $message->save();

        $conversation = $message->conversation;
        if ($conversation->sender_id == Auth::id()) {
            $conversation->sender_viewed = 1;
            $conversation->receiver_viewed = 0;
        } else {
            $conversation->sender_viewed = 0;
            $conversation->receiver_viewed = 1;
        }
        $conversation->save();

        return back();
    }

    public function refresh(Request $request)
    {
        $conversation_id = decrypt($request->id);
        $conversation = Conversation::findOrFail($conversation_id);
        return view('frontend.partials.messages', compact('conversation'));
    }
}
