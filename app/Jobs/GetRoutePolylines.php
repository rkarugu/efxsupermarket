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

class GetRoutePolylines implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public function __construct(public Route $route)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $route = Route::with('waRouteCustomer')->find($this->route->id);
        $route->polylines()->delete();
        $shops = $route->waRouteCustomer()->orderBy('distance_estimate')->get();

        $numberOfWaypointGroups = ceil($shops->count() / 25);

        $skip = 0;
        $waypointGroups = [];
        for ($counter = 1; $counter <= $numberOfWaypointGroups; $counter++) {
            $waypointGroups[] = $shops->skip($skip)->take(25);
            $skip += 25;
        }

        $startLat = $route->start_lat;
        $startLng = $route->start_lng;
        foreach ($waypointGroups as $group) {
            $lastShop = $group->last();
            $group->pop();
            $waypoints = [];
            foreach ($group as $shop) {
                $waypoints[] = [
                    "location" => [
                        "latLng" => [
                            "latitude" => $shop->lat,
                            "longitude" => $shop->lng
                        ]
                    ],
                    "vehicleStopover" => true
                ];
            }

            $response = MappingService::getRoute($startLat, $startLng, $lastShop->lat, $lastShop->lng, waypoints: $waypoints);
            if (isset($response['routes'][0]['polyline']['encodedPolyline'])) {
                $polyline = $route->polylines()->create([
                    'polyline' => $response['routes'][0]['polyline']['encodedPolyline'],
                ]);

                if (isset($response['routes'][0]['optimizedIntermediateWaypointIndex'])) {
                    $polyline->update(['waypoint_order' => implode(",", $response['routes'][0]['optimizedIntermediateWaypointIndex'])]);
                }

            }

            $startLat = $lastShop->lat;
            $startLng = $lastShop->lng;

        }

    }
}
