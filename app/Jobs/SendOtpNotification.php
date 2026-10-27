<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SendOtpNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $channel,
        public string $recipient,
        public string $encryptedCode,
        public string $purpose,
    ) {}

    public function backoff(): array
    {
        return [10, 30];
    }

    public function handle(): void
    {
        // The queue payload contains an encrypted OTP, not plaintext.
        $code = Crypt::decryptString($this->encryptedCode);

        if ($this->channel === 'email') {
            $key = config('services.resend.key');
            $from = config('services.resend.from');

            if (!$key || !$from) {
                throw new RuntimeException(
                    'Resend credentials are not configured.'
                );
            }

            $response = Http::withToken($key)
                ->acceptJson()
                ->post('https://api.resend.com/emails', [
                    'from' => $from,
                    'to' => [$this->recipient],
                    'subject' => 'Your Government Job Portal verification code',
                    'text' => "Your verification code is {$code}. "
                        . 'It expires in '
                        . config('services.portal_auth.otp_ttl_minutes', 5)
                        . ' minutes. Do not share this code.',
                ]);
        } elseif ($this->channel === 'sms') {
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $from = config('services.twilio.from');

            if (!$sid || !$token || !$from) {
                throw new RuntimeException(
                    'Twilio credentials are not configured.'
                );
            }

            $response = Http::asForm()
                ->withBasicAuth($sid, $token)
                ->post(
                    "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json",
                    [
                        'From' => $from,
                        'To' => $this->recipient,
                        'Body' => "Your Government Job Portal verification code is "
                            . "{$code}. It expires in "
                            . config('services.portal_auth.otp_ttl_minutes', 5)
                            . ' minutes. Do not share it.',
                    ]
                );
        } else {
            throw new RuntimeException('Unsupported OTP channel.');
        }

        if (!$response->successful()) {
            // Do not include the provider response, recipient, or OTP
            // in the exception message.
            throw new RuntimeException(
                "OTP delivery failed for channel: {$this->channel}."
            );
        }
    }
}
