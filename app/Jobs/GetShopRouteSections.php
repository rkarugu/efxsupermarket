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

class GetShopRouteSections implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;



    public function __construct(public WaRouteCustomer $customer)
    {

    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $shop = WaRouteCustomer::with('route')->find($this->customer->id);
        $route= $shop->route;
        $route->sections()->delete();
        $shops = $route->waRouteCustomer()->orderBy('distance_estimate')->get()
            ->filter(function ($shop) {
                return $shop->has_valid_location;
            })->map(function (WaRouteCustomer $shop) use ($route) {
                return [
                    'id' => $shop->id,
                    'lat' => (float)$shop->lat,
                    'lng' => (float)$shop->lng,
                    'name' => $shop->bussiness_name,
                ];
            });

        $lastShop = null;
        for ($counter = 0; $counter < count($shops); $counter++) {
            $currentShop = $shops[$counter];
            $startLat = $counter == 0 ? $route->start_lat : $lastShop['lat'];
            $startLng = $counter == 0 ? $route->start_lng : $lastShop['lng'];
            $endLat = $currentShop['lat'];
            $endLng = $currentShop['lng'];

            $distanceEstimate = MappingService::getDistanceBetweenPoints($startLat, $startLng, $endLat, $endLng);
            $timeEstimate = MappingService::getDurationBetweenPoints($startLat, $startLng, $endLat, $endLng);
            $route->sections()->create([
                'start_shop_id' => $counter == 0 ? null : $lastShop['id'],
                'start_lat' => $startLat,
                'start_lng' => $startLng,
                'end_shop_id' => $currentShop['id'],
                'end_lat' => $endLat,
                'end_lng' => $endLng,
                'start_point_is_plan_start_point' => $counter == 0,
                'fuel_estimate' => 0,
                'road_type' => null,
                'road_condition' => null,
                'rainy_fuel_estimate' => 0,
                'rainy_road_type' => null,
                'rainy_road_condition' => null,
                'distance_estimate' => $distanceEstimate,
                'rainy_distance_estimate' => $distanceEstimate,
                'time_estimate' => $timeEstimate,
                'rainy_time_estimate' => $timeEstimate,
            ]);

            $lastShop = $currentShop;
        }
    }
}
