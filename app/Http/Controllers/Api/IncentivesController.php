<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SalesManPerformanceService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class IncentivesController extends Controller
{
    public function driver(Request $request)
    {
        $service = new SalesManPerformanceService();
        $user = JWTAuth::toUser($request->token);
        $date = $request->date;
        return $service->driver($user->id, $date);
    }

    public function salesman(Request $request)
    {
        $date = $request->date;
        $service = new SalesManPerformanceService();
        $user = JWTAuth::toUser($request->token);
        return $service->salesman($user->id, $date);
    }
}
