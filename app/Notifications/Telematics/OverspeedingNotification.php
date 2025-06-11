<?php

namespace App\Notifications\Telematics;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OverspeedingNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct($vehicle, $averageSpeed)
    {
        $this->vehicle = $vehicle;
        $this->averageSpeed = $averageSpeed;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
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
    public function toDatabase(object $notifiable): array
    {
        $vehicleName  = $this->vehicle;
        $speed = $this->averageSpeed;
        return [
            'icon_class' => 'fas fa-shipping-fast text-danger',
            'message' => "$vehicleName is overspeeding at $speed km/hr",
            'url' => route('live-vehicle-movement', $vehicleName)
        ];
    }
    public function toBroadcast($notifiable)
    {
        $broadcast = new BroadcastMessage([
            'vehicle' => $this->vehicle,
            'averageSpeed' => $this->averageSpeed,
            'message' => "Vehicle {$this->vehicle} is overspeeding with an average speed of {$this->averageSpeed} km/h."

        ]);
        return $broadcast;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications');
    }
}
