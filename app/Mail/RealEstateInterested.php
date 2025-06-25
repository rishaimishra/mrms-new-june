<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RealEstateInterested extends Mailable
{
    use Queueable, SerializesModels;
    private $user;
    private $auto;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($realEstate,$user)
    {
        $this->realEstate = $realEstate;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.realestate.interested')->with(["realEstate"=>$this->realEstate,"user"=>$this->user]);
    }
}
