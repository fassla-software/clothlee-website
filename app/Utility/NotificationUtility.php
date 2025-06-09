<?php

namespace App\Utility;

use App\Mail\InvoiceEmailManager;
use App\Models\User;
use App\Models\SmsTemplate;
use App\Http\Controllers\OTPVerificationController;
use Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderNotification;
use App\Models\FirebaseNotification;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class NotificationUtility
{
    public static function sendOrderPlacedNotification($order, $request = null)
    {       
        // Sends email to customer with invoice attached
        $array['view'] = 'emails.invoice';
        $array['subject'] = translate('A new order has been placed') . ' - ' . $order->code;
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['order'] = $order;
        try {
            if ($order->user->email != null) {
                Mail::to($order->user->email)->queue(new InvoiceEmailManager($array));
            }
            Mail::to($order->orderDetails->first()->product->user->email)->queue(new InvoiceEmailManager($array));
        } catch (\Exception $e) {

        }

        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'order_placement')->first()->status == 1) {
            try {
                $otpController = new OTPVerificationController;
                $otpController->send_order_code($order);
            } catch (\Exception $e) {

            }
        }

        // Sends Notifications to user
        self::sendNotification($order, 'placed');
    }

   public static function sendNotification($order)
{     
    // Ensure the function is triggered for every change in delivery status
    $adminId = User::where('user_type', 'admin')->first()->id;
    $userIds = [$order->user->id, $order->seller_id];

    if ($order->seller_id != $adminId) {
        $userIds[] = $adminId;
    }

    $users = User::findMany($userIds);
    
    // Order notification based on delivery_status
    $order_notification = [
        'order_id' => $order->id,
        'order_code' => $order->code,
        'user_id' => $order->user_id,
        'seller_id' => $order->seller_id,
        'status' => $order->delivery_status, // Use delivery_status as the trigger
    ];

    foreach ($users as $user) {
        // ✅ Prevent duplicate notifications for the same delivery status
        $existingNotification = FirebaseNotification::where([
            ['item_type', '=', 'order'],
            ['item_type_id', '=', $order->id],
            ['receiver_id', '=', $user->id],
            ['text', 'LIKE', "%{$order->delivery_status}%"] // Check if the delivery status already exists
        ])->exists();

        if (!$existingNotification) {
            $notificationType = get_notification_type('order_' . $order->delivery_status . '_' . $user->user_type, 'type');
            if ($notificationType != null && $notificationType->status == 1) {
                $order_notification['notification_type_id'] = $notificationType->id;
                Notification::send($user, new OrderNotification($order_notification));
            }
            if ($user->device_token) {
                self::sendFirebaseNotification($user, $order);
            }
        }
    }
}

public static function sendFirebaseNotification($user, $order)
{
    \Log::info("🚀 Checking Firebase Notification for Order ID: {$order->id}, User ID: {$user->id}, Status: {$order->delivery_status}");

    // ✅ Ensure notifications for different delivery statuses are sent
    $existingFirebaseNotification = FirebaseNotification::where([
        ['item_type', '=', 'order'],
        ['item_type_id', '=', $order->id],
        ['receiver_id', '=', $user->id],
        ['text', 'LIKE', "%{$order->delivery_status}%"] // Prevents duplicate status notifications
    ])->exists();

    if ($existingFirebaseNotification) {
        \Log::info("🚫 Skipping Firebase Notification: Already sent for Order ID: {$order->id}, User ID: {$user->id}, Status: {$order->delivery_status}");
        return;
    }

    if (empty($user->device_token)) {
        \Log::error("❌ ERROR: User {$user->id} has NO device token. Skipping Firebase Notification.");
        return;
    }

    \Log::info("📤 Sending Firebase Notification: User ID {$user->id}, Order ID {$order->id}, Status: {$order->delivery_status}");

    // Set the notification title and message based on the order's delivery status
    $title = 'Order Update!';
    $statusMessages = [
        'pending' => "Your order {$order->code} is pending.",
        'confirmed' => "Your order {$order->code} has been confirmed.",
        'on_the_way' => "Your order {$order->code} is on the way.",
        'delivered' => "Your order {$order->code} has been delivered!",
        'cancelled' => "Your order {$order->code} has been cancelled.",
    ];

    $body = $statusMessages[$order->delivery_status] ?? "Your order {$order->code} has been updated.";

	$url = 'https://fcm.googleapis.com/v1/projects/clothlely-app/messages:send';

    $fields = [
        'message' => [
            'token' => $user->device_token,
            'notification' => [
                'title' => $title,
                'body' => $body
            ],
            'data' => [
                'order_id' => (string) $order->id,
                'order_code' => (string) $order->code,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        ]
    ];

    $headers = [
        'Authorization: Bearer ' . self::getFirebaseAccessToken(),
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Log Firebase Response
    \Log::info("🔥 Firebase API Response", [
        'http_code' => $httpCode,
        'response' => $result
    ]);

    if ($httpCode !== 200) {
        \Log::error("❌ Firebase ERROR: Order ID: {$order->id}, Response: {$result}");
    } else {
        \Log::info("✅ Firebase SUCCESS: Order ID: {$order->id}, Notification Sent.");
    }

    // Save notification in the database
    try {
        $firebase_notification = new FirebaseNotification;
        $firebase_notification->title = $title;
        $firebase_notification->text = $body;
        $firebase_notification->item_type = 'order';
        $firebase_notification->item_type_id = $order->id;
        $firebase_notification->receiver_id = $user->id;
        $firebase_notification->save();

        \Log::info("✅ Firebase Notification SAVED to DB for Order ID: {$order->id}");
    } catch (\Exception $e) {
        \Log::error("❌ ERROR Saving Firebase Notification to DB: " . $e->getMessage());
    }
}

  public static function sendNewSupportTicketNotification($admin, $ticket)
{
    \Log::info("🎯 Sending notification to user ID: {$receiver->id} with token: {$receiver->device_token}");

    if (empty($receiver->device_token)) {
        \Log::warning("🚫 No device token for user {$receiver->id}, skipping Firebase.");
        return;
    }


    $exists = FirebaseNotification::where([
        ['item_type', '=', 'support_ticket'],
        ['item_type_id', '=', $ticket->id],
        ['receiver_id', '=', $admin->id],
    ])->exists();

    if ($exists) {
        \Log::info("⛔ Firebase Notification already exists for support ticket #{$ticket->id} to admin {$admin->id}");
        return;
    }

    $title = '📩 Support Ticket';
    $body = mb_substr(strip_tags($ticket->details), 0, 100);
    $link = route('support_ticket.admin_show', encrypt($ticket->id));

    $fields = [
        'message' => [
            'token' => $admin->device_token,
            'notification' => [
                'title' => $title,
                'body' => $body
            ],
            'data' => [
                'ticket_id' => (string) $ticket->id,
                'link' => $link,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        ]
    ];

    $headers = [
        'Authorization: Bearer ' . self::getFirebaseAccessToken(),
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/clothlely-app/messages:send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_exec($ch);
    curl_close($ch);

    // Save in firebase_notifications table (optional)
    try {
        FirebaseNotification::create([
            'title' => $title,
            'text' => $body,
            'item_type' => 'support_ticket',
            'item_type_id' => $ticket->id,
            'receiver_id' => $admin->id,
        ]);
    } catch (\Exception $e) {
        \Log::error('❌ Error saving support ticket notification: ' . $e->getMessage());
    }
}
public static function sendNewConversationNotification($receiver, $conversation, $message)
{
    \Log::info("🎯 Sending notification to user ID: {$receiver->id} with token: {$receiver->device_token}");

    if (empty($receiver->device_token)) {
        \Log::warning("🚫 No device token for user {$receiver->id}, skipping Firebase.");
        return;
    }

    $link = $receiver->type === 'admin'
        ? route('conversations.admin_show', encrypt($conversation->id))
        : route('conversations.show', encrypt($conversation->id));

    \Log::info("🎯 Sending notification to user ID: {$receiver->id} with link: {$link}");

    $title = '📩 New Message';
$senderName = auth()->user()->name ?? 'Unknown';
$messageText = mb_substr(strip_tags($message->message), 0, 100);
$body = "New message from {$senderName} : {$messageText}";

    $fields = [
        'message' => [
            'token' => $receiver->device_token,
            'notification' => [
                'title' => $title,
                'body' => $body
            ],
            'data' => [
                'conversation_id' => (string) $conversation->id,
                'link' => $link,
                'type' => 'webview',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        ]
    ];

    $headers = [
        'Authorization: Bearer ' . self::getFirebaseAccessToken(),
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/clothlely-app/messages:send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $responseData = json_decode($response, true);

   
    if (
        isset($responseData['error']['details'][0]['errorCode']) &&
        $responseData['error']['details'][0]['errorCode'] === 'UNREGISTERED'
    ) {
        \Log::warning("🗑️ Device token for user {$receiver->id} is invalid (UNREGISTERED). Removing token.");
        
    }

    if ($httpCode === 200 && isset($responseData['name'])) {
        \Log::info("✅ Firebase notification sent successfully to user {$receiver->id}. FCM name: {$responseData['name']}");
    } else {
        \Log::error("❌ Firebase notification failed for user {$receiver->id}. HTTP code: {$httpCode}. Response: " . $response);
    }
}

  
  public static function sendChatNotification($receiver, $conversation, $message)
{
    if (empty($receiver->device_token)) {
        \Log::warning("🚫 No device token for user {$receiver->id}, skipping Firebase.");
        return;
    }

    $title = '💬 New Message';
    $body = mb_substr(strip_tags($message->message), 0, 100);
    $link = $receiver->user_type === 'admin'
        ? route('conversations.admin_show', encrypt($conversation->id))
        : route('conversations.show', encrypt($conversation->id));

 $fields = [
    'message' => [
        'token' => $receiver->device_token,
        'notification' => [
            'title' => $title,
            'body'  => $body
        ],
        'data' => [
            'conversation_id' => (string) $conversation->id,
            'link' => $receiver->user_type === 'admin'
                ? route('conversations.admin_show', encrypt($conversation->id))
                : route('conversations.show', encrypt($conversation->id)),
            'type' => 'webview',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
        ]
    ]
];


    $headers = [
        'Authorization: Bearer ' . self::getFirebaseAccessToken(),
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/clothlely-app/messages:send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        \Log::info("✅ Chat notification sent to user {$receiver->id}");
    } else {
        \Log::error("❌ Failed to send chat notification to user {$receiver->id}. Response: {$response}");
    }

    // Always save, even for identical messages
    try {
        FirebaseNotification::create([
            'title' => $title,
            'text' => $body,
            'item_type' => 'chat',
            'item_type_id' => $conversation->id,
            'receiver_id' => $receiver->id,
        ]);
    } catch (\Exception $e) {
        \Log::error("❌ Error saving chat Firebase notification: " . $e->getMessage());
    }
}

  
  
  
public static function sendSellerToCustomerMessageNotification($conversation, $message)
{
$authUser = auth()->user();
$authUser->loadMissing('shop');

    // Determine the actual receiver
    $receiver = $conversation->sender_id == $authUser->id
        ? $conversation->receiver
        : $conversation->sender;

    \Log::info("📨 Sending notification to receiver ID: {$receiver->id}, token: {$receiver->device_token}");

    if (empty($receiver->device_token)) {
        \Log::warning("🚫 No device token for user {$receiver->id}, skipping notification.");
        return;
    }

    $link = $receiver->user_type === 'admin'
        ? route('conversations.admin_show', encrypt($conversation->id))
        : route('conversations.show', encrypt($conversation->id));

    $senderName = $authUser->name ?? 'Seller';
    $messageText = mb_substr(strip_tags($message->message), 0, 100);
    $title = '📩 New Message from Seller';
    $body = "New message from {$senderName}: {$messageText}";
$shopLogo = $authUser->shop && !empty($authUser->shop->logo)
    ? asset('public/uploads/all/' . $authUser->shop->logo)
    : asset('assets/img/placeholder.jpg');

    $fields = [
        'message' => [
            'token' => $receiver->device_token,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'data' => [
                'conversation_id' => (string) $conversation->id,
                'link'            => $link,
                'type'            => 'chat',
                'click_action'    => 'FLUTTER_NOTIFICATION_CLICK',
               'shop_logo' => $shopLogo,
                'shop_name'     => $senderName,
            ],
        ]
    ];

    $headers = [
        'Authorization: Bearer ' . self::getFirebaseAccessToken(),
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/clothlely-app/messages:send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    \Log::info("📬 Firebase response code: {$httpCode}, response: {$response}");

    if ($httpCode !== 200) {
        \Log::error("❌ Failed to send Firebase message to user ID: {$receiver->id}. Response: {$response}");
    } else {
        \Log::info("✅ Message notification sent to user ID: {$receiver->id}");
    }
}


  
  public static function sendAdminToUserMessageNotification($conversation, $message)
{
    
    $authUser = auth()->user();

    \Log::info('🛠️ [AdminNotif] Preparing to send admin message notification', [
    'conversation_id' => $conversation->id,
    'sender_id' => auth()->id(),
    'message_id' => $message->id
]);

    // Determine the receiver (not the admin)
    $receiver = $conversation->sender_id == $authUser->id
        ? $conversation->receiver
        : $conversation->sender;

    \Log::info("📨 Admin message: Sending to receiver ID: {$receiver->id}, token: {$receiver->device_token}");

    if (empty($receiver->device_token)) {
        \Log::warning("🚫 No device token for user {$receiver->id}, skipping Firebase.");
        return;
    }

    $link = route('conversations.show', encrypt($conversation->id));

    $senderName = 'Clothlee';
    $messageText = mb_substr(strip_tags($message->message), 0, 100);
    $title = '📩 رسالة جديدة من الإدارة';
    $body = "رسالة من {$senderName}: {$messageText}";

    $fields = [
        'message' => [
            'token' => $receiver->device_token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => [
                'conversation_id' => (string) $conversation->id,
                'link'            => $link,
                'type'            => 'chat',
                'click_action'    => 'FLUTTER_NOTIFICATION_CLICK',
                'shop_logo'       => asset('assets/img/admin.png'), // optional admin icon
                'shop_name'       => $senderName,
            ],
        ]
    ];

    $headers = [
        'Authorization: Bearer ' . self::getFirebaseAccessToken(),
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/clothlely-app/messages:send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    \Log::info("📬 Admin Firebase response: {$httpCode}, response: {$response}");

    if ($httpCode !== 200) {
        \Log::error("❌ Failed to send admin message notification to user ID: {$receiver->id}. Response: {$response}");
    } else {
        \Log::info("✅ Admin message notification sent to user ID: {$receiver->id}");
    }
}

  
  
  
  

public static function getFirebaseAccessToken()
    {
        $serviceAccount = json_decode(file_get_contents(storage_path('firebase/firebase-adminsdk.json')), true);
        $now = time();
        $jwtPayload = [
        'iss' => 'firebase-adminsdk-fbsvc@clothlely-app.iam.gserviceaccount.com',
        'sub' => 'firebase-adminsdk-fbsvc@clothlely-app.iam.gserviceaccount.com',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $now + 3600,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging'
    ];

        $jwt = JWT::encode($jwtPayload, "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDqExbuD4pY29sh\nOxoo2A4hXkJ84YmELLvjAy92gU22l/XGdxfndJJiM+blbbA8teqtCovDWe8oMv4a\nzSdckfzyLmHpRJ/sToKptTZewyYPnOPhN9I+80o3HYK9Y2puUdUogXAizP/1TmZp\nmmP9r9itCAKTnd58y+0fSv8zURGxlEpl8U7imn+pcWIDZc4cBsHvrWqlB/Zp6mh1\nXKA8Gp9Uz/NeUkfo0T/b8gQMH9aPz9ihZehwxRf5DoUJuAjQbM13d+lXv+15iKqF\nlj0zsVyjR7b9ZgJd+ymvWWj84zaViJOXy1TKtIJtSCrw8+3q9AgPXDgsWagDhzqj\nelOr4OffAgMBAAECggEABOD8+11LjuqWOK0YsH1AXiWu0ReKXDzqdaLdXGT5j7K/\nJrHYx7RAUNewGlwc7kcEazLrEtlPCNF3Rmu8REuusAWTeN87Thuc0Zi7V2JN6slk\nXo9hTHqZs2hkDzqBP2dpP/zir3cCZsSJke3r45/ErmaaVyVVO9isSpivlQw9iCv6\n5IyNXb1FzQ+lJqR4Q+IVcDspiTxD4sKQiscaS0evj2IAZ3qgEvhmY16qlznJ0rY0\n5zZH/CB5Iuy80sY7fIwdEJ2dUStYp94M99rMyNs4jGR/I7C2ywbTw1fTFQDOw5Y3\n8wRPvS5xzLR3zbesvKaSk6F2H2Ig4TpCuQIDAJSpIQKBgQD2iprY+iWCqCkTdqYx\nuHiMo31GlL81JfNwoJktn+k/W/GA4Hb0OGBwCpmsMRBh53lcq0tZ8ODmkPFTA+1E\nAd7NFUlzpcuh+W9iJXD6AkCpkxwTxqZlDXBVl2RgcEY6irpBzaddVNd4QbuKbyi3\nQpWYS1UJ0J5QFBuz5btfQtA9yQKBgQDzDgrDcMVJB8IVJh6k8Yu2LPc2Wuj3a/xY\nqDoQVhsabkTP6xxBBi9Wy4Zz/LkNHUZwc3IKHNqV+HEznQUw+5uRTRB8q3VDxs6J\nnIjYD1SzIrcoPSXZVXxv/ExJPMxBTzabBbABrO1ZCoPpL86Ab4av0kOSpqXt+5NE\nKS5GAm2sZwKBgHqpxZdVBhq2zhEYBGJSlO/sW+UlFuk7o56S2QOhP2y9sy/a/nRO\nJHU6YPESTENZ5sEbnNb4CP3OebNDDea7Q34oLC50/BbvwuJDHK3XNxLn1Z1lRd6b\nTBvZwiBVXCu+YCNjTfUaxEXZ9pRO8CQV5dYrDPlgKZRQHORp1cr0tCU5AoGAIeS8\nZYKtUEDPtMlLDq9MT0w38RF+WTpqxyY2ap7HSslDuUbM+thU86KQDk6Ys5Z4gyfm\nKDjb1nv9tTfZSHpduEZp3Si/woLPvGrivlZs9koKBod4ZrVAFBG3xaK/zP+x2q5R\nW/p0Yq1Ptc3f2xMyUgRdPe8VRnmFkMS5WjPufVMCgYEAupf3ApHN9m1SaRJydIIl\nEZ34xb1KydL15VQAfg5zwzUIIKQG9jmOmxfLnrSlCbP+pUETfpHX2xi7+YV6uUhu\nBLxtVeChetOoRFxF3X0eXF/Yf8jJP5qh2SP75ZGYsE7q6dEWC/X4wu4wTkoeXq5q\nHtZAY1rxVKyPVdU/2aeUtZw=\n-----END PRIVATE KEY-----\n", 'RS256');
        
        $url = 'https://oauth2.googleapis.com/token';
        $data = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);
        
        return $result['access_token'] ?? '';
    }
}