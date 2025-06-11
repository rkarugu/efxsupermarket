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
use Illuminate\Support\Facades\Log;

class VehicleLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $vehicleData;

    /**
     * Create a new event instance.
     */
    public function __construct($vehicleData)
    {
        $this->vehicleData = $vehicleData;
        // Log::info('Vehicle Location Updated: '. json_encode($this->vehicleData));
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('vehicle-location'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'device_number' => $this->vehicleData['device_number'],
            'latitude' => $this->vehicleData['latitude'],
            'longitude' => $this->vehicleData['longitude'],
            'direction' => $this->vehicleData['direction'],
            'mileage' => $this->vehicleData['mileage'],
            'speed' => $this->vehicleData['speed'],
            'fuel_level' => $this->vehicleData['fuel_level'],
            // 'time' => $this->vehicleData['time'],
            'ignition_status' => $this->vehicleData['ignition_status'],
            'movement' => $this->vehicleData['movement'],
            'is_offline' => $this->vehicleData['is_offline'],
            'timestamp' => $this->vehicleData['timestamp'],

        ];
    }
    public function broadcastAs()
    {
        return 'VehicleLocationUpdated';
    }
}
