<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code) {}

    public function build()
    {
        return $this->subject('Mã OTP khôi phục mật khẩu')
            ->view('emails.reset_otp')
            ->with(['code' => $this->code]);
    }
}
