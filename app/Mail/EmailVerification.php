<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use Mail;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;
    public $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($link) {
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail_from = env('mail_from');
        $sender_name = env('MAIL_FROM_NAME');
       return $this->from($mail_from,$sender_name)
        ->subject('Email Verification')
        ->markdown('emails.emailVerification');
    }
}