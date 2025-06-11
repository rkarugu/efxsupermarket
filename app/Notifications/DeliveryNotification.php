<?php

namespace App\Notifications;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliveryNotification extends Notification
{
    use Queueable;
    public $data;
    

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
   
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $greeting = isset($this->notifiable->name) ? "Dear {$this->notifiable->name}," : "Greetings!";

        $date = Carbon::now()->toDateString();
          $pdf = Pdf::loadView('emails.delivery_notification', [
            'greeting' => $greeting,
            'subject' => "ORDER DELIVERY REPORT- $date",
            'deliverySchedules' => $this->data['deliverySchedules'],
        ])->setPaper('a4', 'landscape');

        $pdfPath = storage_path('app/public/delivery_report.pdf');
        $pdf->save($pdfPath);

        return (new MailMessage())
            ->subject("ORDER DELIVERY REPORT - $date")
            ->line("Please find the attached delivery report.")
            ->attach($pdfPath, [
                'as' => 'delivery_report.pdf',
                'mime' => 'application/pdf',
            ]);
    }
    // public function toDatabase(object $notifiable): array
    // {
    //     return [
    //     ];
    // }

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
}
