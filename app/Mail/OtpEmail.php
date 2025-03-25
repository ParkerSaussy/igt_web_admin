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

class OtpEmail extends Mailable
{
    
    use Queueable, SerializesModels;
    public $otp;
    public $dynamicData;  // Data you want to pass to the view
    public $template; 
    public $subject;     // Name of the template view

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dynamicData, $template,$subject) {
        $this->dynamicData = $dynamicData;
        $this->template = $template;
        $this->subject = $subject;
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
        ->subject('ItsGoTime - '.$this->subject)
        ->markdown('emails.'.$this->template);

        // return $this->view('emails.'.$this->template)
        // ->subject('Subject for '.$this->template);
    }

    
}
