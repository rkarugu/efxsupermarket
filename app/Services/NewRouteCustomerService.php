<?php

namespace  App\Services;

use App\Model\Route;
use App\Model\WaRouteCustomer;

class NewRouteCustomerService{
    static public function updateRouteDistanceAndPolylines($shop, $route){

        $startLat = $route->start_lat;
        $startLng = $route->start_lng;
        $endLat = $shop->lat;
        $endLng = $shop->lng;
        $shop->update([
            'distance_estimate' => MappingService::getDistanceBetweenPoints($startLat, $startLng, $endLat, $endLng)
        ]);


//        update route polylines
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

//        update route sections
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