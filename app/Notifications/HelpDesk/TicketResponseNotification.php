<?php

namespace App\Notifications\HelpDesk;

use App\Notifications\Channels\InfoSky as SmsMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TicketResponseNotification extends Notification
{
    use Queueable;
    
    public function __construct(
        protected Ticket $ticket
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'mail', 
            SmsMessage::class
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting("Dear $notifiable->name,")
            ->line("Your Ticket {$this->ticket->code} has a new response. Please log in to check on the status.")
            ->action('View Ticket', route('help-desk.tickets.show', $this->ticket->id));
    }

    public function toSms(object $notifiable): string
    {
        return (isset($notifiable->name) ? "Dear $notifiable->name, " : "Greetings! ") . "Your Ticket {$this->ticket->code} has a new response. Please log in to check on the status.";
    }

}
