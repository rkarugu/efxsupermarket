<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PaymentReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    use InteractsWithBroadcasting;

    public $paymentDetails;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($paymentDetails)
    {
        $this->paymentDetails = $paymentDetails;
        $this->broadcastVia('pusher_pos');

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
//        Log::info('Payment even publishing..');
        return [
            new Channel('payments'),
        ];
    }
    public function broadcastAs()
    {
        return 'payments';
    }
}
