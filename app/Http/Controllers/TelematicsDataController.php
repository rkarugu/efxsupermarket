<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelematicsDataController extends Controller
{
    public function receiveData(Request $request)
    {
        try {
            Log::info("Data Received");
            Log::info(json_encode($request->all()));

            return response()->json(['data' => $request->all()]);
        } catch (\Throwable $e) {
            Log::info("Data Failed");
            return response()->json(['data' => $request->all(), 'error' => $e->getMessage(), 'trace' => $e->getTrace()]);
        }
    }
}
