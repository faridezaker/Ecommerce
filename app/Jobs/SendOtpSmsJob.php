<?php

namespace App\Jobs;

use Ghasedak\GhasedakApi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendOtpSmsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct($cellphone, $otpCode)
    {
        $this->cellphone = $cellphone;
        $this->otpCode = $otpCode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $ghasedak = new GhasedakApi(env('GHASEDAK_API_KEY'));
            $message = "کد تأیید شما: {$this->otpCode}";
            $ghasedak->SendSimple($this->cellphone, $message);
        } catch (\Exception $e) {
            Log::error("خطا در ارسال پیامک: " . $e->getMessage());
        }
    }
}
