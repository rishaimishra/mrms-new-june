<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AutoInterested extends Mailable
{
    use Queueable, SerializesModels;
    private $user;
    private $auto;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($auto,$user)
    {
        $this->auto = $auto;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.auto.interested')->with(["auto"=>$this->auto,"user"=>$this->user]);
    }
}
