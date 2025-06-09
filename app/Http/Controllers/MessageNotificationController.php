<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

class MessageNotificationController extends Controller
{
    public function create()
    {
        $customers = User::whereIn('user_type', ['customer', 'seller', 'admin', 'staff'])
            ->where('banned', 0)
            ->get();

        return view('backend.notification.message_notification', compact('customers'));
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => ['required', 'array'],
            'title'    => ['required', 'string', 'max:255'],
            'body'     => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $adminId = auth()->id();
        $title   = $request->title;
        $body    = $request->body;

     foreach ($request->user_ids as $user_id) {
    $user = User::find($user_id);
    if (!$user) continue;

    // ✅ تحقق من وجود محادثة سابقة (في الاتجاهين)
    $conversation = Conversation::where(function ($q) use ($adminId, $user_id) {
        $q->where('sender_id', $adminId)->where('receiver_id', $user_id);
    })->orWhere(function ($q) use ($adminId, $user_id) {
        $q->where('sender_id', $user_id)->where('receiver_id', $adminId);
    })->first();

    // ✅ إذا لا توجد محادثة، أنشئ واحدة جديدة
    if (!$conversation) {
        $conversation = Conversation::create([
            'sender_id'       => $adminId,
            'receiver_id'     => $user_id,
            'title'           => $title,
            'sender_viewed'   => 1,
            'receiver_viewed' => 0,
        ]);
    }

    // ✅ أضف الرسالة دائمًا
    Message::create([
        'conversation_id' => $conversation->id,
        'user_id'         => $adminId,
        'message'         => $body,
    ]);

    // ✅ أرسل إشعار FCM (نفس الكود القديم)
    if (!empty($user->device_token)) {
      
        $accessToken = \App\Utility\NotificationUtility::getFirebaseAccessToken();

        $fields = [
            'message' => [
                'token' => $user->device_token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => [
                    'conversation_id' => (string) $conversation->id,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'type' => 'chat',
                ]
            ]
        ];

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/clothlely-app/messages:send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $response = curl_exec($ch);
        curl_close($ch);

        \Log::info('📨 Firebase Notification Response', [
            'to_user'   => $user_id,
            'response'  => $response,
        ]);
    }
}

        flash(translate('Notifications sent and messages delivered successfully.'))->success();
        return back();
    }
}
