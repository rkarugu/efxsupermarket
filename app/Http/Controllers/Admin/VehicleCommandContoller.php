<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\SmsService;
use App\Model\Restaurant;
use App\Models\VehicleCustomSchedule;
use App\Models\VehicleExemptionSchedule;
use App\Models\VehicleImmobilization;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class VehicleCommandContoller extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request,protected SmsService $smsService)
    {
        $this->model = 'vehicle-command-center';
        $this->title = 'Vehicle Command Center';
        $this->pmodule = 'vehicle-command-center';
        $this->basePath = 'admin.vehicle_command_center';
    }
    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (!can('view', $pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        $user = Auth::user();
        if($user->role_id == 1){
            $branchIds = DB::table('user_branches')
            ->pluck('restaurant_id')
            ->toArray();
        }else{
            $branchIds = DB::table('user_branches')
            ->where('user_id', $user->id)
            ->pluck('restaurant_id')
            ->toArray();
        }
        $branches = Restaurant::whereIn('id', $branchIds)->get();

        $vehicles = Vehicle::all();
        return view('admin.vehicle_command_center.index', compact('title', 'model', 'pmodule', 'vehicles', 'branches'));
    }

    public function controlAction(Request $request)
    {
        try {
            $action = $request->action;
            $user  = Auth::user();
           
            foreach ($request->vehicle as $vehicleId) { 
                $vehicle = Vehicle::find($vehicleId);
                if($vehicle){
                    if($vehicle->sim_card_number && $vehicle->license_plate_number){
                        if($action && $action=='switch-off'){
                            $this->smsService->sendMessage('setdigout 1 84600 8', $vehicle->sim_card_number);
                            $vehicle->switch_off_status = 'off';
                            $vehicle->save();
        
                            $immobilizationEvent = new VehicleImmobilization();
                            $immobilizationEvent->vehicle_id = $vehicle->id;
                            $immobilizationEvent->causer_id = $user->id;
                            $immobilizationEvent->reason = 'vehicle command center action';
                            $immobilizationEvent->time = 84600;
                            $immobilizationEvent->speed = 8;
                            $immobilizationEvent->save();
        
                        }elseif($action && $action=='switch-on'){
                            $vehicle->switch_off_status = 'on';
                            $vehicle->save();
                            $switchOnMsg = 'setdigout 0';
                            $this->smsService->sendMessage($switchOnMsg, $vehicle->sim_card_number);
                            $now = Carbon::now()->toDateTimeString();
                            $immobilizationEvent = VehicleImmobilization::latest()
                                ->where('vehicle_id', $vehicle->id)
                                ->whereNull('switch_on_date')
                                ->first();
                            if($immobilizationEvent){
                            $immobilizationEvent->switch_on_date = $now;
                            $immobilizationEvent->switch_on_by = $user->id;
                            $immobilizationEvent->save();
                            }
            
                        }elseif($action && $action=='reset-mileage'){
                            $vehicle->onboarding_mileage = $request->mileage;
                            $vehicle->save();
                            $switchOnMsg = 'setparam 11807:'.$request->mileage;
                            $this->smsService->sendMessage($switchOnMsg, $vehicle->sim_card_number);
            
                        }elseif($action && $action=='custom'){
                            $this->smsService->sendMessage($request->command, $vehicle->sim_card_number);
            
                        }else{
                            Session::flash('warning', 'Invalid action selected.');
                            return redirect()->back();
                        }
    
                    }

                }
               
            }
            Session::flash('success', 'Vehicle commands executed successfully.');
            return redirect()->back();
           
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    } 
    public function exemptionSchedules(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'vehicle-command-center-exemption-schedules';
        if (!can('view', $pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfDay();
        $end = $request->end_date? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $exemptionSchedules = VehicleExemptionSchedule::latest()
            ->whereBetween('created_at',  [$start, $end])
            ->get();


        return view('admin.vehicle_command_center.exemption_schedules', compact('title', 'model', 'pmodule', 'exemptionSchedules'));

    }
    public function editExemptionScheduleVehicles($id)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'vehicle-command-center-exemption-schedules';
        if (!can('add-vehicles-to-exemption-schedules', $pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        $schedule = VehicleExemptionSchedule::find($id);
        $selectedVehicleIds = explode(',', $schedule->vehicle_ids);
        $vehicles = Vehicle::all();
        return view('admin.vehicle_command_center.exemption_schedules_update', compact('title', 'model', 'pmodule', 'schedule', 'vehicles', 'selectedVehicleIds'));
    }
    public function updateExemptionScheduleVehicles(Request $request, $id)
    {
        try {
            $pmodule = $this->pmodule;
            if (!can('add-vehicles-to-exemption-schedules', $pmodule)) {
                Session::flash(pageRestrictedMessage());
                return redirect()->back();
            }
            $schedule = VehicleExemptionSchedule::find($id);
            $vehicles = implode(', ', $request->vehicle);
            $schedule->vehicle_ids = $vehicles;
            $schedule->save();
            Session::flash('success', 'Vehicle exemption schedule updated successfully');
            return redirect()->route('exemption-schedules');
        
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->route('exemption-schedules');
        }
        

    }
    public function customSchedules(Request $request){
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'vehicle-command-center-custom-schedules';
        $title = $this->title;
        $model = 'vehicle-command-center-custom-schedules';
        if (!can('view', $pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfDay();
        $end = $request->end_date? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $customSchedules = VehicleCustomSchedule::latest()
            ->whereBetween('created_at',  [$start, $end])
            ->get();
        return view('admin.vehicle_command_center.custom_schedules', compact('title', 'model', 'pmodule', 'customSchedules'));
    }
    public function createCustomSchedules(){
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'vehicle-command-center-custom-schedules';
        $title = $this->title;
        $model = 'vehicle-command-center-custom-schedules';
        if (!can('add', $pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        $vehicles = Vehicle::all();
        return view('admin.vehicle_command_center.create_custom_schedules', compact('title', 'model', 'pmodule', 'vehicles'));

    }
    public function storeCustomSchedules(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'vehicle-command-center-custom-schedules';
        $title = $this->title;
        $model = 'vehicle-command-center-custom-schedules';
        $user = Auth::user();
        if (!can('add', $pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        $customSchedule = new VehicleCustomSchedule();
        $vehicles = implode(', ', $request->vehicle);
        $customSchedule->vehicle_ids = $vehicles;
        $customSchedule->created_by = $user->id;
        $customSchedule->action = $request->action;
        $customSchedule->time = Carbon::parse($request->time)->toDateTimeString();
        $customSchedule->save();
        Session::flash('success', 'Vehicle custom schedule created successfully');
        return redirect()->route('custom-schedules');

    }
    public function editCustomSchedules($id){
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'vehicle-command-center-custom-schedules';
        $title = $this->title;
        $model = 'vehicle-command-center-custom-schedules';
        if (!can('edit', $pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        $vehicles = Vehicle::all();
        $schedule = VehicleCustomSchedule::find($id);
        $selectedVehicles = explode(', ', $schedule->vehicle_ids);
        return view('admin.vehicle_command_center.edit_custom_schedules', compact('title', 'model', 'pmodule', 'vehicles', 'schedule', 'selectedVehicles'));

    }
    public function updateCustomSchedules(Request $request, $id)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'vehicle-command-center-custom-schedules';
        $title = $this->title;
        $model = 'vehicle-command-center-custom-schedules';
        $user = Auth::user();
        if (!can('add', $pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        $customSchedule = VehicleCustomSchedule::find($id);
        $vehicles = implode(', ', $request->vehicle);
        $customSchedule->vehicle_ids = $vehicles;
        $customSchedule->created_by = $user->id;
        $customSchedule->action = $request->action;
        $customSchedule->time = Carbon::parse($request->time)->toDateTimeString();
        $customSchedule->save();
        Session::flash('success', 'Vehicle custom schedule updated successfully');
        return redirect()->route('custom-schedules');

    }
}
