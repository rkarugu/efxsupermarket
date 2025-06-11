<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DeliverySchedule;
use App\Models\DeliverySplit;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Vehicle;
use App\Model\Restaurant;


class DeliverySplitController  extends Controller
{
     protected $model;

    protected $title;

    public function __construct()
    {
        $this->model = 'delivery-split';
        $this->title = 'Delivery Splits';
        $this->pmodel = 'delivery-split';
        $this->breadcum = [$this->title => route('reports.items_list_report'), 'Listing' => ''];
      
    }

    public function splitSchedules($scheduleId)
    {
        if (!can('delivery-split', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
         $user = getLoggeduserProfile();
       
    $splits = DeliverySchedule::select(
        'delivery_schedules.*',
        'delivery_schedules.status AS delivery_status',
        'delivery_schedules.id AS schedule_id',
        'routes.*', 
        'salesman_shifts.*',
        'salesman_shifts.created_at AS shift_created_at',
        'vehicles.*', 
        'users.*', 
        DB::raw('SUM(COALESCE(wii.net_weight * oi.quantity, 0) / 1000) AS shift_tonnage'),
        DB::raw("CONCAT('DS-', LPAD(CAST(delivery_schedules.id AS CHAR), 6, '0')) AS delivery_number")
    )
    ->leftJoin('routes', 'delivery_schedules.route_id', '=', 'routes.id')
    ->leftJoin('salesman_shifts', 'delivery_schedules.shift_id', '=', 'salesman_shifts.id')
    ->leftJoin('vehicles', 'delivery_schedules.vehicle_id', '=', 'vehicles.id')
    ->leftJoin('users', 'delivery_schedules.driver_id', '=', 'users.id')
    ->leftJoin('wa_internal_requisitions AS wir', 'salesman_shifts.id', '=', 'wir.wa_shift_id')
    ->leftJoin('wa_internal_requisition_items AS oi', 'wir.id', '=', 'oi.wa_internal_requisition_id')
    ->leftJoin('wa_inventory_items AS wii', 'oi.wa_inventory_item_id', '=', 'wii.id')
    ->where('delivery_schedules.id', $scheduleId)
    ->first();

        $branches = Restaurant::all();
    $vehicles = Vehicle::whereHas('driver')->get();$vehicles = Vehicle::whereHas('driver')
            ->where('branch_id', $user->restaurant_id)
            ->get();
    foreach ($vehicles as $vehicle) {
            $activeSchedule = DeliverySchedule::latest()->active()->where('vehicle_id', $vehicle->id)->first();
            if ($activeSchedule) {
                $vehicle->isAvailable = 0;
            } else {
                $vehicle->isAvailable = 1;
            }

        }
    $tonnagesplits = DeliverySplit::join('vehicles','delivery_splits.vehicle_id','=','vehicles.id')->join('users','vehicles.driver_id','=','users.id')->select('delivery_splits.*','vehicles.license_plate_number as numberplate','users.name as drivername','delivery_splits.created_at as splitdate')->where('delivery_splits.schedule_id',  $scheduleId)->get();
   


    return view('admin.delivery_schedules.split', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => $this->breadcum, 
            'splits' => $splits,
            'vehicles' => $vehicles,
            'tonnagesplits' => $tonnagesplits,
        ]);

}
public function insertDeliverySplit(Request $request)
{
    
 $deliverySplit = DeliverySplit::create([
        'tonnange_before' => $request->tonnage_now,
        'tonnange_split' => $request->tonnage_split,
        'tonnange_remaining' => $request->tonnage_remaining,
        'schedule_id' => $request->schedule_id,
        'vehicle_id' => $request->selected_vehicle,
     
    ]);

     return redirect()->back()->with('success', 'Delivery Schedule Tonnage Splitted Successfully.');
}


}
