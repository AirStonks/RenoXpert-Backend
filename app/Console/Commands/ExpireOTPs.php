<?php

namespace App\Console\Commands;

use App\Models\OTPRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireOTPs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire pending OTP requests that have passed their expiration time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Find and update all pending OTP requests that have expired
        $count = OTPRequest::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        // Optional: Log the number of expired OTPs for monitoring
        if ($count > 0) {
            Log::info("Expired {$count} OTP requests.");
        }

        return 0; // Success exit code
    }
}
