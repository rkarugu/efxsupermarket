<?php

namespace App\Notifications;

use App\Notifications\Channels\InfoSky as SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnbalancedInvoices extends Notification
{
    use Queueable;

    public $invoices;
    public $date;
    public $unbalanced_stock= 0;
    public $unbalanced_amount = 0;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data, $date = null)
    {
        $this->invoices = $data;
        $this->unbalanced_stock = $data['unbalanced_moves'];
        $this->unbalanced_amount = $data['unbalanced_amounts'];
        $this->date = now();
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
            ->line('There are unbalanced invoices as shown below')
            ->line("Invoices wth unbalanced Stock Moves $this->unbalanced_stock")
            ->line("Invoices wth unbalanced Invoice amount vs Debtor tran amount $this->unbalanced_amount")
            ->line("Please go to sales & Receivables >>> Reports >> Unbalanced Invoices Report")
            ->action('View Items', route('sales-and-receivables-reports.unbalanced-invoices-report'));
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
            'message' => "There are unbalanced invoices as at $this->date",
            'url' => route('sales-and-receivables-reports.unbalanced-invoices-report')
        ];
    }

    public function toSms(object $notifiable): string
    {
        return   "There are unbalanced invoices as at $this->date";
    }
}
