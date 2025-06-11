<?php

namespace App\Notifications\Reconciliation;

use App\Models\PaymentVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentApprovalsCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected PaymentVerification $paymentVerification
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting("Dear $notifiable->name,")
            ->line("Processing payment approvals for the period {$this->paymentVerification->start_date} to {$this->paymentVerification->end_date} is complete. Please log in to check on the result.")
            ->action('View Payments', route('payment-reconciliation.verification.list', $this->paymentVerification->id));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icon_class' => 'fa fa-check-circle text-success',
            'message' => "Payment approvals for the period {$this->paymentVerification->start_date} to {$this->paymentVerification->end_date} completed",
            'url' => route('payment-reconciliation.verification.list', $this->paymentVerification->id)
        ];
    }
}
