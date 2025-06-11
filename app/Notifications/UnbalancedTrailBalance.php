<?php

namespace App\Notifications;

use App\Notifications\Channels\InfoSky as SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnbalancedTrailBalance extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', SmsMessage::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting((isset($notifiable->name) ? "Dear $notifiable->name," : "Greetings!"))
            ->line('The GL account does not balance debits and credits as at '. today()->format('Y-m-d'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
    public function toDatabase(object $notifiable): array
    {
        return [
            'icon_class' => 'fa fa-shopping-cart text-warning',
            'message' => "The GL account does not balance debits and credits as at ".today()->format('Y-m-d'),
        ];
    }

    public function toSms(object $notifiable): string
    {
        return    "The GL account does not balance debits and credits as at ".today()->format('Y-m-d');
    }
}
