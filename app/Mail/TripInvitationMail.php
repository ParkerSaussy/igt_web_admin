<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TripInvitationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $test;
    public $icsContent;
    public $dynamicData;
    /**
     * Create a new message instance.
     */
    public function __construct($test,$icsContent,$dynamicData)
    {
        $this->test = $test;
        $this->icsContent = $icsContent;
        $this->dynamicData = $dynamicData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your '.$this->dynamicData[0]['trip_name'].' Adventure Awaits: Trip Details Inside')
        ->view('emails.sendinvitation')
        ->with('dynamicData', $this->dynamicData)
        ->attachData($this->icsContent, 'event.ics', [
            'mime' => 'text/calendar'
        ]);
    }
}
