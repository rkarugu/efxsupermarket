<?php

namespace App\Notifications\HelpDesk;

use App\Notifications\Channels\InfoSky as SmsMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TicketCreationNotification extends Notification
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
            ->subject('Your Support Ticket has been Created')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Thank you for reaching out to our support team. We have successfully received your request and created a support ticket for you. Our team is committed to providing you with the best possible assistance.')
            ->line('Here are the details of your support ticket:')
            ->line('**Ticket ID:** ' . $this->ticket->code)
            ->line('**Subject:** ' . $this->ticket->subject)
            ->line('**Description:** ' . $this->ticket->message)
            ->line('**Created On:** ' . $this->ticket->created_at->format('Y-m-d H:i:s'))
            ->line('**Next Steps:**')
            ->line('1. Our support team will review your ticket and assign it to the appropriate department.')
            ->line('2. You will receive further communication from us regarding the status and progress of your ticket.')
            ->line('**Support Portal:**')
            ->action('Track Your Ticket', route('help-desk.tickets.show', $this->ticket->id));
    }

    public function toSms(object $notifiable): string
    {
        return (isset($notifiable->name) ? "Dear $notifiable->name, " : "Greetings! ") . "Your Ticket {$this->ticket->code} has ben created, we'll attend to it right away.";
    }

}
