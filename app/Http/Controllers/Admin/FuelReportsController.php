<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class FuelReportsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'fuel-reports';
        $this->title = 'Fuel Reports';
        $this->pmodule = 'fuel-reports';
        $this->basePath = 'admin.fuel_reports';
    }
    public function consumptionIndex(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'consumption-report';
        $basePath = $this->basePath;
        $branches = Restaurant::all();
        $vehicles = Vehicle::all();
        $authuser = Auth::user();   
        $start_date = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfDay();
        $end_date = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $vehicle = $request->vehicle ? Vehicle::find($request->vehicle)->license_plate_number : 'KDN 134D';
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $fuelData = DB::connection('telematics')->table('vehicle_telematics')
                ->whereBetween('timestamp', [$start_date, $end_date])
                ->orderBy('timestamp', 'asc')
                ->where('device_number', $vehicle)
                ->get(['timestamp', 'fuel_level'])
                ->map(function ($record) {
                    $record->timestamp = Carbon::parse($record->timestamp)->format('H:i:s');
                    return $record;
                });
           
            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            return view('admin.fuel_reports.consumption_report', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'branches','authuser', 'vehicles','fuelData'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

}
