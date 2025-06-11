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

class GetRouteShopDistanceEstimates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public  $routeId;
    public function __construct(public Route $route)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $route = Route::with('waRouteCustomer')->find($this->route->id);
        $shops = $route?->waRouteCustomer ?? [];
        foreach ($shops as $shop){
            $startLat = $route->start_lat;
            $startLng = $route->start_lng;
            $endLat = $shop->lat;
            $endLng = $shop->lng;
            $shop->update([
                'distance_estimate' => MappingService::getDistanceBetweenPoints($startLat, $startLng, $endLat, $endLng)
            ]);
        }

    }
}
