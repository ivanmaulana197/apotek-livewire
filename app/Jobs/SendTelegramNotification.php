<?php
namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendTelegramNotification implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;
    public $tries      = 5;
    public $retryAfter = [60, 120];
    /**
     * Create a new job instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId   = env('TELEGRAM_CHAT_ID');
            dump($this->message);

            $response = Http::asForm()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $this->message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->failed()) {
                                    // throw exception supaya queue otomatis retry
                $this->release(10); // tunda 60 detik sebelum retry
                throw new \Exception('Gagal mengirim Telegram, coba lagi nanti.');
            }
        } catch (\Exception $e) {
                                // Laravel akan otomatis retry sesuai $tries & $backoff
            $this->release(10); // tunda 60 detik sebelum retry
        }
    }
}
