<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class PaymentRequestSMS extends Notification
{
    use Queueable;
    private $dueamount;
    private $year;
    private $council_short_name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($dueamount, $year, $council_short_name)
    {
        $this->dueamount = $dueamount;
        $this->year = $year;
        $this->council_short_name = $council_short_name;
        //$this->mobileNo = $mobileNo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwilioChannel::class];
    }


    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content("Dear Property Owner, you have arrears of NLe {$this->dueamount} for your {$this->year} {$this->council_short_name} PropertyRate. Kindly make payments soon. Ignore if already paid or 76864861 for query.");
    }

    public function canReceiveAlphanumericSender()
    {
        return true;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
