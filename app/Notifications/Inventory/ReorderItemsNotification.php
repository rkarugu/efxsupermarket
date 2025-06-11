<?php

namespace App\Notifications\Inventory;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReorderItemsNotification extends Notification
{
    use Queueable;

    protected $context;

    protected string $date;

    public function __construct($context)
    {
        $this->context = $context;
        $this->date = now()->format('ga d/m/Y');
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->from(env('PROCUREMENT_EMAIL'), env('PROCUREMENT_NAME'))
            ->greeting((isset($notifiable->name) ? "Dear $notifiable->name," : "Greetings!"))
            ->line((isset($notifiable->name) ? "You have " : "There are ") . ($notifiable->stocks_count ?? $this->context['stocks_count']) . " inventory items that are running out of stock.")
            ->line("Please go to Inventory >>> Reports >> Out of Stock and view the items")
            ->action('View Items', route('inventory-reports.out-of-stock-report'));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icon_class' => 'fa fa-shopping-cart text-green',
            'message' => "You have " . ($notifiable->stocks_count ?? $this->context['stocks_count']) . " out of stock items",
            'url' => route('inventory-reports.out-of-stock-report')
        ];
    }

    public function toSms(object $notifiable): string
    {
        return (isset($notifiable->name) ? "Dear $notifiable->name, you have" : "Greetings! There are ") . ($notifiable->stocks_count ?? $this->context['stocks_count']) . " out of stock items as at $this->date";
    }
}
