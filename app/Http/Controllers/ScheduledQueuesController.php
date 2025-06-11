<?php

namespace App\Http\Controllers;

use App\Jobs\GetShopDistanceEstimates;
use App\Jobs\GetShopRoutePolylines;
use App\Jobs\GetShopRouteSections;
use App\Model\WaRouteCustomer;
use Illuminate\Http\Request;

class ScheduledQueuesController extends Controller
{
    public function recalculatePolylinesSections($shopId)
    {
        $distanceEstimates = new GetShopDistanceEstimates($shopId);
        $routeSections = new GetShopRouteSections($shopId);
        $routePolylines = new GetShopRoutePolylines($shopId);
        $this->dispatch($distanceEstimates);
        $this->dispatch($routeSections);
        $this->dispatch($routePolylines);
    }

    public function calculatePolylinesSections()
    {
        $shops = WaRouteCustomer::where('distance_estimates',0)->get()->filter(function ($shop) {
            return $shop->has_valid_location;
        });
        foreach ($shops as $shop){
            $distanceEstimates = new GetShopDistanceEstimates($shop->id);
            $routeSections = new GetShopRouteSections($shop->id);
            $routePolylines = new GetShopRoutePolylines($shop->id);
            $this->dispatch($distanceEstimates);
            $this->dispatch($routeSections);
            $this->dispatch($routePolylines);
        }
    }

}
