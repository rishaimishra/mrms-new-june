<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPdfMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $pdfPath;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $pdfPath)
    {
        //
        $this->data = $data;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('admin.dstvtransaction.attachpdf')
        ->attach($this->pdfPath);
                    // ->with('transaction', $this->data)
    }
}
