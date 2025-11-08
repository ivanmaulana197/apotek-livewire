<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotification
{
    public static function send($message)
    {
        $token   = env('TELEGRAM_BOT_TOKEN');
        $chat_id = env('TELEGRAM_CHAT_ID');

        if (! $token || ! $chat_id) {
            Log::warning("Telegram: token atau chat_id belum diset di .env");
            return;
        }

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        Http::post($url, [
            'chat_id'    => $chat_id,
            'text'       => $message,
            'parse_mode' => 'HTML',
        ]);

    }
}
