<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Location\Coordinate;
use Location\Distance\Vincenty;
use Polyline;

class MappingService
{
    static private function computeRoute($originLat, $originLng, $destinationLat, $destinationLng, $waypoints = [])
    {
        $requestData = [
            "origin" => [
                "location" => [
                    "latLng" => [
                        "latitude" => $originLat,
                        "longitude" => $originLng
                    ]
                ]
            ],
            "destination" => [
                "location" => [
                    "latLng" => [
                        "latitude" => $destinationLat,
                        "longitude" => $destinationLng
                    ]
                ]
            ],
            "intermediates" => $waypoints,
            "optimizeWaypointOrder" => true,
            "travelMode" => "DRIVE",
            "routingPreference" => "TRAFFIC_UNAWARE",
            "languageCode" => "en-US",
            "units" => "METRIC"
        ];

//        Log::info(json_encode($requestData));

        $response = Http::withHeaders([
            'X-Goog-Api-Key' => config('app.google_maps_api_key'),
            'X-Goog-FieldMask' => 'routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline,routes.optimizedIntermediateWaypointIndex',
            'Content-Type' => 'application/json'
        ])->post('https://routes.googleapis.com/directions/v2:computeRoutes', $requestData);

//        Log::info("Response status: {$response->status()}");
//        Log::info("Response body: {$response->body()}");

        if ($response->ok()) {
            return json_decode($response->body(), true);
        }

        return null;
    }

    static public function getRoute($originLat, $originLng, $destinationLat, $destinationLng, $waypoints = [])
    {
        return self::computeRoute($originLat, $originLng, $destinationLat, $destinationLng, $waypoints);
    }

    static public function getDistanceBetweenPoints($originLat, $originLng, $destinationLat, $destinationLng, $returnValue = true): float|int|string
    {
        $response = self::computeRoute($originLat, $originLng, $destinationLat, $destinationLng);
        if (!$response) {
            return 0;
        }

        if (isset($response['routes'][0]['distanceMeters'])) {
            return $response['routes'][0]['distanceMeters'];
        }

        return 0;
    }

    static public function getDurationBetweenPoints($originLat, $originLng, $destinationLat, $destinationLng, $returnValue = true): float|int|string
    {
        $response = self::computeRoute($originLat, $originLng, $destinationLat, $destinationLng);
        if (!$response) {
            return 0;
        }

        if (isset($response['routes'][0]['duration'])) {
            $secondsAsString = substr_replace($response['routes'][0]['duration'], "", -1);
            return (int)$secondsAsString;
        }

        return 0;
    }

    static public function decodePolyline(string $polyline): array
    {
        $points = Polyline::decode($polyline);
        return Polyline::pair($points);
    }

    /**
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @return float|int Distance in meters
     */
    static public function getTheaterDistanceBetweenTwoPoints($lat1, $lon1, $lat2, $lon2): float|int
    {
        $coordinate1 = new Coordinate($lat1, $lon1);
        $coordinate2 = new Coordinate($lat2, $lon2);

        $calculator = new Vincenty();

        return $calculator->getDistance($coordinate1, $coordinate2);
    }
}