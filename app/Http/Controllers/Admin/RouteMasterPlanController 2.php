<?php

namespace App\Http\Controllers\Admin;

use App\Model\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RouteMasterPlanController extends Controller
{
    public function store($routeId, Request $request)
    {
        $route = Route::find($routeId);

        $route->routePlan()->create([
            'start_location_name' => $request->starting_location,
            'start_location_latitude' => $request->starting_lat,
            'start_location_longitude' => $request->starting_lng,
        ]);

        return redirect()->route('admin.routes.plan', $route->id)->with('success', 'Route plan updated successfully');
    }

    public function update($routeId, Request $request)
    {
        try {
            $route = Route::with(['routePlan', 'routePlan.segments'])->find($routeId);
            $route->routePlan()->update([
                'start_location_name' => $request->starting_location,
                'start_location_latitude' => $request->starting_lat,
                'start_location_longitude' => $request->starting_lng,
            ]);

            foreach ($route->routePlan->segments as $segment) {
                $segment->update([
                    'fuel_estimate' => $request->get("fuel_estimate-$segment->id", 0),
                    'distance_estimate' => $request->get("distance_estimate-$segment->id", 0),
                    'time_estimate' => $request->get("time_estimate-$segment->id", 0),
                ]);
            }

            return redirect()->route('admin.routes.plan', $route->id)->with('success', 'Route plan updated successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'An error was encountered. Try again.');
        }
    }
}
