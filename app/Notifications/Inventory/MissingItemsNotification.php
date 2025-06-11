<?php

namespace App\Notifications\Inventory;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\InfoSky as  SmsMessage;

class MissingItemsNotification extends Notification
{
    use Queueable;

    protected $context;

    protected string $date;

    public function __construct(array $context)
    {
        $this->context = $context;
        $this->date = now()->format('ga d/m/Y');
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database', SmsMessage::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->from(env('PROCUREMENT_EMAIL'), env('PROCUREMENT_NAME'))
            ->greeting((isset($notifiable->name) ? "Dear $notifiable->name," : "Greetings!"))
            ->line("There are a total of " . ($notifiable->stocks_count ?? $this->context['stocks_count']) . " Missing Items from  " . ($notifiable->suppliers_count ?? $this->context['suppliers_count']) . " Suppliers as at $this->date")
            ->action('View Items', route('inventory-reports.missing-items-report.index'));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icon_class' => 'fa fa-shopping-cart text-warning',
            'message' => "There are  " . ($notifiable->stocks_count ?? $this->context['stocks_count']) . "  Missing Items as at $this->date",
            'url' => route('inventory-reports.missing-items-report.index')
        ];
    }

    public function toSms(object $notifiable): string
    {
        return (isset($notifiable->name) ? "Dear $notifiable->name," : "Greetings!") . " There are a total of  " . ($notifiable->stocks_count ?? $this->context['stocks_count']) . " Missing Items from " . ($notifiable->suppliers_count ?? $this->context['suppliers_count']) . " Suppliers as at $this->date";
    }
}
