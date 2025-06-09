<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\BusinessSetting;
use App\Models\Message;
use App\Models\Product;
use Auth;
use Mail;
use App\Mail\ConversationMailManager;
use App\Utility\NotificationUtility;

class ConversationController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_all_product_conversations'])->only('admin_index');
        $this->middleware(['permission:delete_product_conversations'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index()
{
    if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {
        $conversations = Conversation::where(function ($query) {
                $query->where('sender_id', Auth::user()->id)
                      ->orWhere('receiver_id', Auth::user()->id);
            })
            ->whereHas('messages') // Only show conversations that have messages
            ->orderBy('updated_at', 'desc')
            ->paginate(8);

        return view('frontend.user.conversations.index', compact('conversations'));
    } else {
        flash(translate('Conversation is disabled at this moment'))->warning();
        return back();
    }
}


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function admin_index()
{
    if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {
        $conversations = Conversation::whereHas('messages') // Only show non-empty conversations
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('backend.support.conversations.index', compact('conversations'));
    } else {
        flash(translate('Conversation is disabled at this moment'))->warning();
        return back();
    }
}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
public function store(Request $request)
{
    $user = Auth::user();
    $product = Product::findOrFail($request->product_id);
    $receiver = $product->user;

    // Check for existing conversation between same users but DIFFERENT product
    $existingConversation = Conversation::where(function ($q) use ($user, $receiver) {
            $q->where('sender_id', $user->id)
              ->where('receiver_id', $receiver->id);
        })
        ->orWhere(function ($q) use ($user, $receiver) {
            $q->where('sender_id', $receiver->id)
              ->where('receiver_id', $user->id);
        })
        ->where('product_id', $request->product_id) // check for same product
        ->first();

    if (!$existingConversation) {
        $conversation = new Conversation();
        $conversation->sender_id = $user->id;
        $conversation->receiver_id = $receiver->id;
        $conversation->product_id = $request->product_id;
        $conversation->title = $request->title;
        $conversation->save();
    } else {
        $conversation = $existingConversation;
    }

    $message = new Message();
    $message->conversation_id = $conversation->id;
    $message->user_id = $user->id;
    $message->message = $request->message;
    $message->save();

    $this->send_message_to_seller($conversation, $message, $receiver->user_type);

    \Log::info('📤 Message sent with product-aware conversation check', [
        'user_id' => $user->id,
        'receiver_id' => $receiver->id,
        'product_id' => $request->product_id,
        'conversation_id' => $conversation->id,
    ]);

    NotificationUtility::sendAdminToUserMessageNotification($conversation, $message);

    flash(translate('Message has been sent to seller'))->success();
    return back();
}

  
  
  
public function openConversationWithSeller($product_id)
{
    $user = Auth::user();
    $product = Product::findOrFail($product_id);
    $seller = $product->user;

    // Check if conversation already exists between these two for this product
    $conversation = Conversation::where(function ($q) use ($user, $seller) {
            $q->where('sender_id', $user->id)
              ->where('receiver_id', $seller->id);
        })
        ->orWhere(function ($q) use ($user, $seller) {
            $q->where('sender_id', $seller->id)
              ->where('receiver_id', $user->id);
        })
        ->where('product_id', $product_id) // ✅ Check for same product
        ->first();

    // If no conversation exists, create one
    if (!$conversation) {
        $conversation = new Conversation();
        $conversation->sender_id = $user->id;
        $conversation->receiver_id = $seller->id;
        $conversation->product_id = $product_id; // ✅ Save product_id
		$conversation->title = 'Contact about product';
        $conversation->save();
    }

    return redirect()->route('conversations.show', encrypt($conversation->id));
}



    public function send_message_to_seller($conversation, $message, $user_type)
    {
        $array['view'] = 'emails.conversation';
        $array['subject'] = translate('Sender').':- '. Auth::user()->name;
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = translate('Hi! You recieved a message from ') . Auth::user()->name . '.';
        $array['sender'] = Auth::user()->name;

        if ($user_type == 'admin') {
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
  
  
  
  public function admin_message_store(Request $request)
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

    try {
        \App\Utility\NotificationUtility::sendAdminToUserMessageNotification($conversation, $message);
    } catch (\Throwable $e) {
        \Log::error("❌ Error sending Firebase notification from admin: " . $e->getMessage());
    }

    return back()->with('success', translate('Message sent and notification dispatched.'));
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
        return view('frontend.user.conversations.show', compact('conversation'));
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function admin_show($id)
    {
        $conversation = Conversation::findOrFail(decrypt($id));
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->sender_viewed = 1;
        } elseif ($conversation->receiver_id == Auth::user()->id) {
            $conversation->receiver_viewed = 1;
        }
        $conversation->save();
        return view('backend.support.conversations.show', compact('conversation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $conversation = Conversation::findOrFail(decrypt($id));
        $conversation->messages()->delete();

        if (Conversation::destroy(decrypt($id))) {
            flash(translate('Conversation has been deleted successfully'))->success();
            return back();
        }
    }
}
