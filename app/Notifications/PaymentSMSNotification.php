<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class PaymentSMSNotification extends Notification
{
    use Queueable;
    private $property;
    private $mobile_number;
    private $payment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($property, $mobile_number, $payment)
    {
        $this->property = $property;
        $this->mobile_number = $mobile_number;
        $this->payment = $payment;
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
        $amount = $this->payment->amount;

        return (new TwilioSmsMessage())
            ->content("Dear Property Owner thank you for making payment of NLe {$amount} for your WARDC property rate.");
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
