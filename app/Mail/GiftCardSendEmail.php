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

class GiftCardSendEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $giftCardInfo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($giftCardInfo) {
        $this->giftCardInfo = $giftCardInfo;
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
        ->subject('Gift Card')
        ->markdown('emails.giftcardreceive');
    }
}
