<?php

namespace App\Services\OTP;

use App\Contracts\SendSms;
use Illuminate\Support\Facades\Http;

class Advansystelecom implements SendSms
{
    public function send($to, $from, $text, $template_id)
    {
        $url = env('ADVANSYS_API_URL');
        $apiKey = env('ADVANSYS_API_KEY');
        $sender = env('ADVANSYS_SENDER_NAME'); 
        $payload = [
            'PhoneNumber' => $to,
            'Message' => $text,
            'SenderName' => $sender,
            'RequestID' => uniqid('msg_'),
        ];

        $response = Http::withHeaders([
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        \Log::info('SMS Response:', ['body' => $response->body()]);
    }
}
