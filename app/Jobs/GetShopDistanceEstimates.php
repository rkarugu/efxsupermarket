<?php

namespace App\Jobs;

use App\Model\Route;
use App\Model\WaRouteCustomer;
use App\Services\MappingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetShopDistanceEstimates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct(public WaRouteCustomer $customer)
    {
//
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shop = WaRouteCustomer::with('route')->find($this->customer->id);
        $route = $shop->route;
        $startLat = $route->start_lat;
        $startLng = $route->start_lng;
        $endLat = $shop->lat;
        $endLng = $shop->lng;
        $shop->update([
            'distance_estimate' => MappingService::getDistanceBetweenPoints($startLat, $startLng, $endLat, $endLng)
        ]);
    }
}
