<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Conversation;
use App\Http\Resources\V2\ConversationCollection;
use App\Http\Resources\V2\MessageCollection;
use App\Mail\ConversationMailManager;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Mail;
use App\Utility\NotificationUtility;


class ChatController extends Controller
{

  public function conversations()
{
    $userId = auth()->user()->id;

    $conversations = Conversation::with(['receiver', 'receiver.shop', 'product'])
        ->where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
              ->orWhere('receiver_id', $userId);
        })
        ->whereHas('messages')
        ->orderByRaw("
            CASE 
                WHEN sender_id = 9 THEN 0
                ELSE 1
            END,
            updated_at DESC
        ")
        ->paginate(10);

    return new ConversationCollection($conversations);
}



    public function messages($id)
    {
        $messages = Message::where('conversation_id', $id)->latest('id')->paginate(10);
        return new MessageCollection($messages);
    }

    public function insert_message(Request $request)
    {
        $message = new Message;
        $message->conversation_id = $request->conversation_id;
        $message->user_id = auth()->user()->id;
        $message->message = $request->message;
        $message->save();
        $conversation = $message->conversation;
        if ($conversation->sender_id == $request->user_id) {
            $conversation->receiver_viewed = "1";
        } elseif ($conversation->receiver_id == $request->user_id) {
            $conversation->sender_viewed = "1";
        }
        $conversation->save();
$currentUserId = auth()->id();

$receiverId = $conversation->sender_id == $currentUserId
    ? $conversation->receiver_id
    : $conversation->sender_id;

$receiver = User::find($receiverId);

if ($receiver && $receiver->id != $currentUserId) {
    NotificationUtility::sendNewConversationNotification($receiver, $conversation, $message);
}

        $messages = Message::where('id', $message->id)->paginate(1);
        return new MessageCollection($messages);
    }

    public function get_new_messages($conversation_id, $last_message_id)
    {
        $messages = Message::where('conversation_id', $conversation_id)->where('id', '>', $last_message_id)->latest('id')->paginate(10);
        return new MessageCollection($messages);
    }

 public function create_conversation(Request $request)
{
    // Validate the product by slug or ID
    $product = Product::where('slug', $request->product_id)->firstOrFail();
    $seller_user = $product->user;
    $user = auth()->user();

    // Create conversation with product_id
    $conversation = new Conversation;
    $conversation->sender_id = $user->id;
    $conversation->receiver_id = $seller_user->id;
    $conversation->title = $request->title ?? '';
    $conversation->product_id = $product->id; // ✅ Save product_id here
    $conversation->save();

    // Save message if provided
    if ($request->filled('message')) {
        $message = new Message;
        $message->conversation_id = $conversation->id;
        $message->user_id = $user->id;
        $message->message = $request->message;
        $message->save();

        $this->send_message_to_seller($conversation, $message, $seller_user, $user);
        NotificationUtility::sendNewConversationNotification($seller_user, $conversation, $message);
    }

    return response()->json([
        'result' => true,
        'conversation_id' => $conversation->id,
        'shop_name' => $seller_user->user_type == 'admin' ? 'Clothlee' : $seller_user->shop->name,
        'shop_logo' => $seller_user->user_type == 'admin' ? uploaded_asset(get_setting('header_logo')) : uploaded_asset($seller_user->shop->logo),
        'title' => $conversation->title,
        'product_image' => uploaded_asset($product->thumbnail_img), 
        'message' => translate("Conversation created"),
    ]);
}

    public function send_message_to_seller($conversation, $message, $seller_user, $user)
    {
        $array['view'] = 'emails.conversation';
        $array['subject'] = translate('Sender').':- '. $user->name;
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = translate('Hi! You recieved a message from ') . $user->name . '.';
        $array['sender'] = $user->name;

        if ($seller_user->type == 'admin') {
            $array['link'] = route('conversations.admin_show', encrypt($conversation->id));
        } else {
            $array['link'] = route('conversations.show', encrypt($conversation->id));
        }

        $array['details'] = $message->message;

        try {
            Mail::to($conversation->receiver->email)->queue(new ConversationMailManager($array));
        } catch (\Exception $e) {
            //dd($e->getMessage());
        }

    }
}
