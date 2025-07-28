<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVerifyOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code) {}

    public function build()
    {
        return $this->subject('Xác minh Email đăng ký')
            ->view('emails.verify_otp')
            ->with(['code' => $this->code]);
    }
}
