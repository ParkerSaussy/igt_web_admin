<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PdfReportEmail extends Mailable
{
    use SerializesModels;

    public $pdf;

    public function __construct($pdf)
    {
        $this->pdf = $pdf;
    }

    public function build()
    {
        return $this->view('emails.report')
            ->subject('Expense Report')
            ->attachData($this->pdf, 'report.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}

