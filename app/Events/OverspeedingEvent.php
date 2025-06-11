<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OverspeedingEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $vehicle;
    public $averageSpeed;
    public $message;
    public $time;

    /**
     * Create a new event instance.
     */
    public function __construct($vehicle, $averageSpeed, $message, $time)
    {
        $this->vehicle = $vehicle;
        $this->averageSpeed = $averageSpeed;
        $this->message = $message;
        $this->time = $time;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications'),
        ];
    }
    public function broadcastAs()
    {
        return 'OverspeedingEvent';
    }
}
