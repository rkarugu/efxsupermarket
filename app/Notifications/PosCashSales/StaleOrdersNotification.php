<?php

namespace App\Notifications\PosCashSales;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\InfoSky as  sms;

class StaleOrdersNotification extends Notification
{
    use Queueable;

    protected mixed $order;

    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [sms::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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

    public function toSMS(object $notifiable): string
    {

        $startTime = Carbon::parse($this->order->paid_at);
        $endTime = Carbon::now();
        $difference = $endTime->diffInMinutes($startTime);
        $message = 'Order' . $this->order->sales_no .' for Customer '.$this->order->customer.' has been in dispatching state for over '.$difference.' minutes';
        return $message;
    }
}
