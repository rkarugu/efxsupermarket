<?php

namespace App\Jobs;

use App\Model\DeliveryCentres;
use App\Model\Route;
use App\Model\WaRouteCustomer;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
class RecalculateDistanceTimeEstimates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $routeId; 

    public function __construct($routeId)
    {
        $this->routeId = $routeId; 
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
         

        Log::info('Event data from jobs checking: at ', ['data' => $this->routeId, 'timme' => Carbon::now()]);


        $route = Route::with(['centers', 'centers.waRouteCustomers'])->find($this->routeId); 
        $route->sections()->delete();
         

        $routeMasterData = [
            'route_id' => $route->id,
            'route_name' => $route->route_name,
            'starting_location_name' => $route->starting_location_name,
            'starting_lat' => (float)$route->start_lat,
            'starting_lng' => (float)$route->start_lng,
            'end_lat' => 0,
            'end_lng' => 0,
            'route_centers' => $route->centers->map(function (DeliveryCentres $centre) use ($route) {
                return [
                    'id' => $centre->id,
                    'name' => $centre->name,
                    'lat' => (float)$centre->lat,
                    'lng' => (float)$centre->lng,
                    'preferred_radius' => $centre->preferred_center_radius,
                    'shops' => $centre->waRouteCustomers->map(function (WaRouteCustomer $shop) use ($route) {
                        return [
                            'id' => $shop->id,
                            'lat' => (float)$shop->lat,
                            'lng' => (float)$shop->lng,
                            'name' => $shop->bussiness_name,
                        ];
                    })
                ];
            }),
            'sections' => [],
        ];
 
        $allShops = [];
        foreach ($routeMasterData['route_centers'] as $route_center) {
            foreach ($route_center['shops'] as $shop) {
                $distanceFromStartingPoint = 0;
                $startingLocationLat = (float)$route->start_lat;
                $startingLocationLng = (float)$route->start_lng;
                $shopLat = $shop['lat'];
                $shopLng = $shop['lng']; 

                $distanceFromStartingPointResult = $this->getDurationOrDistanceBetweenPoints($startingLocationLat, $startingLocationLng, $shopLat, $shopLng);
                if ($distanceFromStartingPointResult) {
                    if (isset($distanceFromStartingPointResult['distance'])) {
                        if (isset($distanceFromStartingPointResult['distance']['value'])) {
                            $distanceFromStartingPoint = $distanceFromStartingPointResult['distance']['value'];
                        }
                    }
                }

                $shop['distance'] = $distanceFromStartingPoint;
                $allShops[] = $shop;
            }
        }

        $sortedShops = collect($allShops)->sortBy('distance');
        $allShops = $sortedShops->values(); 
        $lastShop = null;
        for ($counter = 0; $counter < count($allShops); $counter++) {
            $currentShop = $allShops[$counter];
            $section = [
                'id' => null,
                'starting_point' => [
                    'name' => null,
                    'shop_id' => null,
                    'start_point_is_plan_start_point' => false,
                    'lat' => 0,
                    'lng' => 0,
                ],
                'end_point' => [
                    'name' => null,
                    'shop_id' => null,
                    'lat' => 0,
                    'lng' => 0,
                ],
                'fuel_estimate' => 0,
                'distance_estimate' => 0,
                'time_estimate' => 0,
                'road_type' => null,
                'road_condition' => null,
                'rainy_fuel_estimate' => 0,
                'rainy_distance_estimate' => 0,
                'rainy_time_estimate' => 0,
                'rainy_road_type' => null,
                'rainy_road_condition' => null,
            ];

            if ($counter == 0) {
                $section['starting_point']['name'] = $routeMasterData['starting_location_name'];
                $section['starting_point']['start_point_is_plan_start_point'] = true;
                $section['starting_point']['lat'] = $routeMasterData['starting_lat'];
                $section['starting_point']['lng'] = $routeMasterData['starting_lng'];
            } else {
                $section['starting_point']['name'] = $lastShop['name'];
                $section['starting_point']['shop_id'] = $lastShop['id'];
                $section['starting_point']['lat'] = $lastShop['lat'];
                $section['starting_point']['lng'] = $lastShop['lng'];
            }

            $section['end_point']['name'] = $currentShop['name'];
            $section['end_point']['shop_id'] = $currentShop['id'];
            $section['end_point']['lat'] = $currentShop['lat'];
            $section['end_point']['lng'] = $currentShop['lng'];

            $fillingSection = null;
            $savedSection = $route->sections()
                ->where('start_shop_id', '=', $section['starting_point']['shop_id'])
                ->where('end_shop_id', '=', $section['end_point']['shop_id'])
                ->first();

            if (!$savedSection) {
                $newSection = $route->sections()->create([
                    'start_shop_id' => $section['starting_point']['shop_id'],
                    'start_lat' => $section['starting_point']['lat'],
                    'start_lng' => $section['starting_point']['lng'],
                    'end_shop_id' => $section['end_point']['shop_id'],
                    'end_lat' => $section['end_point']['lat'],
                    'end_lng' => $section['end_point']['lng'],
                    'start_point_is_plan_start_point' => $section['starting_point']['start_point_is_plan_start_point'],
                    'fuel_estimate' => 0,
                    'road_type' => null,
                    'road_condition' => null,
                    'rainy_fuel_estimate' => 0,
                    'rainy_road_type' => null,
                    'rainy_road_condition' => null,
                ]);

                // Update time & distance estimates
                $estimates = $this->getDurationOrDistanceBetweenPoints(
                    originLat: $newSection->start_lat,
                    originLng: $newSection->start_lng,
                    destinationLat: $newSection->end_lat,
                    destinationLng: $newSection->end_lng
                );

                $distanceEstimate = $this->extractDistanceFromResponse($estimates);
                $durationEstimate = $this->extractTimeFromResponse($estimates);

                $newSection->update([
                    'distance_estimate' => $distanceEstimate,
                    'rainy_distance_estimate' => $distanceEstimate,
                    'time_estimate' => $durationEstimate,
                    'rainy_time_estimate' => $durationEstimate,
                ]);

                $fillingSection = $newSection;
            } else {
                if (($savedSection->distance_estimate == 0) || ($savedSection->time_estimate == 0)) {
                    $estimates = $this->getDurationOrDistanceBetweenPoints(
                        originLat: $savedSection->start_lat,
                        originLng: $savedSection->start_lng,
                        destinationLat: $savedSection->end_lat,
                        destinationLng: $savedSection->end_lng
                    );

                    $distanceEstimate = $this->extractDistanceFromResponse($estimates);
                    $durationEstimate = $this->extractTimeFromResponse($estimates);

                    $savedSection->update([
                        'distance_estimate' => $distanceEstimate,
                        'rainy_distance_estimate' => $distanceEstimate,
                        'time_estimate' => $durationEstimate,
                        'rainy_time_estimate' => $durationEstimate,
                    ]);
                }

                $fillingSection = $savedSection;
            }

            $section['time_estimate'] = $fillingSection->time_estimate;
            $section['rainy_time_estimate'] = $fillingSection->rainy_time_estimate;
            $section['distance_estimate'] = $fillingSection->distance_estimate;
            $section['rainy_distance_estimate'] = $fillingSection->rainy_distance_estimate;
            $section['fuel_estimate'] = $fillingSection->fuel_estimate;
            $section['rainy_fuel_estimate'] = $fillingSection->rainy_fuel_estimate;
            $section['road_condition'] = $fillingSection->rainy_fuel_estimate;
            $section['rainy_road_condition'] = $fillingSection->rainy_road_condition;
            $section['road_type'] = $fillingSection->road_type;
            $section['rainy_road_type'] = $fillingSection->rainy_road_type;

            $routeMasterData['sections'][] = $section;
            $lastShop = $currentShop;
            $routeMasterData['end_lat'] = $lastShop['lat'];
            $routeMasterData['end_lng'] = $lastShop['lng'];
        }

        
    }

    private function extractDistanceFromResponse($response): float|int
    {
        if (isset($response['distance'])) {
            if (isset($response['distance']['value'])) {
                return round((($response['distance']['value']) / 1000), 1);
            }
        }

        return 0;
    }

    private function extractTimeFromResponse($response): float|int
    {
        if (isset($response['duration'])) {
            if (isset($response['duration']['value'])) {
                return ceil((($response['duration']['value']) / 60));
            }
        }

        return 0;
    }
    public function getDurationOrDistanceBetweenPoints($originLat, $originLng, $destinationLat, $destinationLng)
    {
        $client = new Client();
        $response = $client->get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'query' => [
                'origins' => $originLat . ',' . $originLng,
                'destinations' => $destinationLat . ',' . $destinationLng,
                'key' => config('app.google_maps_api_key'),
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if ($data['status'] === 'OK') {
            return $data['rows'][0]['elements'][0];
        }

        return null; // Handle the case when the API request fails
    }
}
