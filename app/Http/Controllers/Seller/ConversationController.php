<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\BusinessSetting;
use App\Models\Message;
use App\Models\ProductQuery;
use Auth;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {
            $conversations = Conversation::where('sender_id', Auth::user()->id)->orWhere('receiver_id', Auth::user()->id)->orderBy('updated_at', 'desc')->paginate(5);
            return view('seller.conversations.index', compact('conversations'));
        } else {
            flash(translate('Conversation is disabled at this moment'))->warning();
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $conversation = Conversation::findOrFail(decrypt($id));
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->sender_viewed = 1;
        } elseif ($conversation->receiver_id == Auth::user()->id) {
            $conversation->receiver_viewed = 1;
        }
        $conversation->save();
        return view('seller.conversations.show', compact('conversation'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $conversation = Conversation::findOrFail(decrypt($request->id));
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->sender_viewed = 1;
            $conversation->save();
        } else {
            $conversation->receiver_viewed = 1;
            $conversation->save();
        }
        return view('frontend.partials.messages', compact('conversation'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function message_store(Request $request)
{
    $authUser = Auth::user();

    $message = new Message;
    $message->conversation_id = $request->conversation_id;
    $message->user_id = $authUser->id;
    $message->message = $request->message;
    $message->save();

    $conversation = $message->conversation;
    $conversation->sender_viewed = "0";
    $conversation->receiver_viewed = "1";
    $conversation->save();

    // Send Firebase push notification to the customer (receiver)
    try {
        $customer = $conversation->receiver;
\App\Utility\NotificationUtility::sendSellerToCustomerMessageNotification($conversation, $message);
    } catch (\Throwable $e) {
        \Log::error("❌ Error sending Firebase notification to customer: " . $e->getMessage());
    }

    return back()->with('success', translate('Message sent and notification dispatched.'));
}
  
  
public function adminChat()
{
    $adminId = 9;
    $sellerId = Auth::id();

    // Check if conversation exists in either direction
    $conversation = Conversation::where(function ($q) use ($adminId, $sellerId) {
        $q->where('sender_id', $sellerId)->where('receiver_id', $adminId);
    })->orWhere(function ($q) use ($adminId, $sellerId) {
        $q->where('sender_id', $adminId)->where('receiver_id', $sellerId);
    })->first();

    // If not, create it with seller as sender
    if (!$conversation) {
        $conversation = Conversation::create([
            'sender_id' => $sellerId,
            'receiver_id' => $adminId,
            'title' => 'Support with Admin',
            'sender_viewed' => 1,
            'receiver_viewed' => 0,
        ]);
    }

    // Mark as viewed by current user
    if ($conversation->sender_id == $sellerId) {
        $conversation->sender_viewed = 1;
    } elseif ($conversation->receiver_id == $sellerId) {
        $conversation->receiver_viewed = 1;
    }
    $conversation->save();

    return view('seller.conversations.admin_show', compact('conversation'));
}



}
